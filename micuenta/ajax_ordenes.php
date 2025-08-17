<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/db.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');

header('Content-Type: application/json; charset=utf-8');
while (ob_get_level() > 0) { @ob_end_clean(); }

// Helper para salida JSON
function out(array $arr, int $code = 200): never {
    http_response_code($code);
    echo json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (empty($_SESSION['usuario_id'])) {
    out(['ok'=>false,'message'=>'No autorizado'],401);
}

$uid = (int)$_SESSION['usuario_id'];

// Flag de debug
$DEBUG = !empty($_GET['debug']) || !empty($_POST['debug']);

/** Helpers fecha **/
function dt_immutable(string $s): ?DateTimeImmutable {
    try { return new DateTimeImmutable($s); } catch (Throwable) { return null; }
}
function start_of_day(DateTimeImmutable $d): string { return $d->setTime(0,0,0)->format('Y-m-d H:i:s'); }
function end_of_day(DateTimeImmutable $d): string   { return $d->setTime(23,59,59)->format('Y-m-d H:i:s'); }

$filter = strtolower(trim((string)($_REQUEST['filter'] ?? '')));
$dayStr = trim((string)($_REQUEST['day'] ?? ''));
$month  = (int)($_REQUEST['month'] ?? 0);
$year   = (int)($_REQUEST['year'] ?? 0);
$from   = trim((string)($_REQUEST['from'] ?? ''));
$to     = trim((string)($_REQUEST['to'] ?? ''));

$dateRange = null;

switch ($filter) {
  case 'dia':
    if ($dayStr !== '' && ($d = dt_immutable($dayStr))) {
      $dateRange = [ start_of_day($d), end_of_day($d) ];
    }
    break;

  case 'mes':
    if ($month >= 1 && $month <= 12 && $year >= 1900) {
      $first = dt_immutable(sprintf('%04d-%02d-01',$year,$month));
      if ($first) {
        $last  = $first->modify('last day of this month');
        $dateRange = [ start_of_day($first), end_of_day($last) ];
      }
    }
    break;

  case 'año':
    if ($year >= 1900) {
      $first = dt_immutable("$year-01-01");
      $last  = dt_immutable("$year-12-31");
      if ($first && $last) {
        $dateRange = [ start_of_day($first), end_of_day($last) ];
      }
    }
    break;

  case 'personalizado':
    $dFrom = $from ? dt_immutable($from) : null;
    $dTo   = $to   ? dt_immutable($to)   : null;
    if ($dFrom && $dTo) {
      if ($dFrom > $dTo) [$dFrom,$dTo] = [$dTo,$dFrom];
      $dateRange = [ start_of_day($dFrom), end_of_day($dTo) ];
    } elseif ($dFrom) {
      $dateRange = [ start_of_day($dFrom), end_of_day($dFrom) ];
    } elseif ($dTo) {
      $dateRange = [ start_of_day($dTo), end_of_day($dTo) ];
    }
    break;
}

$where = [
  "AND" => [
    "o.id_usuario" => $uid,
    "o.estado"     => "completado"
  ]
];
if ($dateRange) {
  $where["AND"]["o.fecha[<>]"] = $dateRange;
}

try {
  $rows = $database->select('ordenes(o)', [
    "[>]productos(p)" => ["id_producto" => "id_producto"],
    "[>]caracteristicas_productos(c)" => ["p.id_producto" => "id_producto"],
  ], [
    "o.id_orden",
    "o.fecha",
    "p.id_producto",
    "p.nombre",
    "p.imagen",
    // ⬇️ usar el namespace correcto de Medoo
    "marca" => \Medoo\Medoo::raw("MIN(c.marca)")
  ], array_merge($where, [
    "GROUP" => ["o.id_orden","o.fecha","p.id_producto","p.nombre","p.imagen"],
    "ORDER" => ["o.id_orden" => "DESC"]
  ]));

  $payload = ["ok"=>true,"data"=>$rows];

  if ($DEBUG) {
    // En Medoo v2: last() puede no existir; usa log()
    $lastSql = method_exists($database, 'last') ? $database->last() : null;
    if (!$lastSql && method_exists($database, 'log')) {
        $log = $database->log();
        $lastSql = is_array($log) ? end($log) : $log;
    }

    $payload["debug"] = [
      "sql"       => $lastSql,
      "where"     => $where,
      "dateRange" => $dateRange,
      "filter"    => $filter,
      "inputs"    => [
        "day" => $dayStr, "month" => $month, "year" => $year,
        "from" => $from, "to" => $to
      ]
    ];
  }

  out($payload);

} catch (Throwable $e) {
  // También intentamos sacar el último SQL si hay error
  $lastSql = method_exists($database, 'last') ? $database->last() : null;
  if (!$lastSql && method_exists($database, 'log')) {
      $log = $database->log();
      $lastSql = is_array($log) ? end($log) : $log;
  }

  error_log("[ordenes] ".$e->getMessage());

  $err = ["ok"=>false,"message"=>"Error al obtener órdenes"];
  if ($DEBUG) {
    $err["debug"] = [
      "sql"       => $lastSql,
      "error"     => $e->getMessage(),
      "where"     => $where,
      "dateRange" => $dateRange,
      "filter"    => $filter
    ];
  }
  out($err, 500);
}