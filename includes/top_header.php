<section class="bg-black xl:py-1.5 py-0.5 overflow-hidden" id="top-header">
    <div class="marquee-container">
        <?php
        // Asumiendo que ya tienes $database (Medoo) inicializado
        
        // Trae 1 registro activo (el más reciente)
        $marquesina = $database->get("marquesina", ["mq_tit", "mq_url"], [
            "mq_est" => "activo",
            "ORDER" => ["mq_id" => "DESC"]
        ]);

        if ($marquesina && !empty($marquesina["mq_tit"])) {
            $titulo = htmlspecialchars($marquesina["mq_tit"], ENT_QUOTES, 'UTF-8');
            $href = !empty($marquesina["mq_url"]) ? $marquesina["mq_url"] : '#';
            $href = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');
            ?>
            <p class="text-white font-semibold marquee-text xl:text-base text-xs">
                <a href="<?= $href ?>" class="hover:underline"><?= $titulo ?></a>
            </p>
            <?php
        } else {
            // Sin registros: no mostramos nada (o pon un fallback si quieres)
            // echo '<p class="text-white font-semibold marquee-text xl:text-base text-xs">No hay promociones activas por ahora.</p>';
        }
        ?>
    </div>
</section>