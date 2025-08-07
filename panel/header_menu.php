<div class="sticky">
    <div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
    <div class="app-sidebar">
        <div class="side-header">
            <a class="header-brand1" href="panel.php">
                <img src="<?php echo $url; ?>/panel/assets/images/brand/logo.png" class="header-brand-img desktop-logo"
                    alt="logo">
                <img src="<?php echo $url; ?>/panel/assets/images/brand/logo-3.png" class="header-brand-img toggle-logo"
                    alt="logo">
                <img src="<?php echo $url; ?>/panel/assets/images/brand/logo-3.png" class="header-brand-img light-logo"
                    alt="logo">
                <img src="<?php echo $url; ?>/panel/assets/images/brand/logo-3.png" class="header-brand-img light-logo1"
                    alt="logo">
            </a>
            <!-- LOGO -->
        </div>
        <div class="main-sidemenu">
            <div class="slide-left disabled" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191"
                    width="24" height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
                </svg></div>
            <ul class="side-menu">
                <li class="sub-category">
                    <h3>Main</h3>
                </li>
                <li class="slide">
                    <a class="side-menu__item has-link" data-bs-toggle="slide" href="panel.php"><i
                            class="side-menu__icon fe fe-home"></i><span class="side-menu__label">Inicio</span></a>
                </li>
                <li class="sub-category">
                    <h3>UI Kit</h3>
                </li>
                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0)"><i
                            class="side-menu__icon fe fe-shopping-bag"></i><span
                            class="side-menu__label">Productos</span><i class="angle fe fe-chevron-right"></i></a>
                    <ul class="slide-menu">
                        <li class="side-menu-label1"><a href="javascript:void(0)">Productos</a></li>
                        <li><a href="add_productos.php" class="slide-item"> Agregar</a></li>
                        <li><a href="productos.php" class="slide-item"> Reporte</a></li>
                        <li><a href="categorias.php" class="slide-item"> Categorias</a></li>
                    </ul>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0)"><i
                            class="side-menu__icon fe fe-users"></i><span class="side-menu__label">Clientes</span><i
                            class="angle fe fe-chevron-right"></i></a>
                    <ul class="slide-menu">
                        <li class="side-menu-label1"><a href="javascript:void(0)">Clientes</a></li>
                        <li><a href="add_clientes.php" class="slide-item"> Agregar</a></li>
                        <li><a href="clientes.php" class="slide-item"> Reporte</a></li>
                    </ul>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0)"><i
                            class="side-menu__icon fe fe-layers"></i><span class="side-menu__label">Ventas</span><i
                            class="angle fe fe-chevron-right"></i></a>
                    <ul class="slide-menu">
                        <li class="side-menu-label1"><a href="javascript:void(0)">Ventas</a></li>
                        <li><a href="ventas.php" class="slide-item"> Reporte</a></li>
                    </ul>
                </li>


                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0)"><i
                            class="side-menu__icon fe fe-zap"></i><span
                            class="side-menu__label">Administradores</span><i class="angle fe fe-chevron-right"></i></a>
                    <ul class="slide-menu">
                        <li class="side-menu-label1"><a href="javascript:void(0)">Administradores</a></li>
                        <li><a href="#" class="slide-item"> Agregar</a></li>
                        <li><a href="#" class="slide-item"> Reporte</a></li>
                    </ul>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0)"><i
                            class="side-menu__icon fe fe-cpu"></i><span class="side-menu__label">Herramientas</span><i
                            class="angle fe fe-chevron-right"></i></a>
                    <ul class="slide-menu">
                        <li class="side-menu-label1"><a href="javascript:void(0)">Herramientas</a></li>
                        <li><a href="#" class="slide-item"> Slider Principal</a></li>
                    </ul>
                </li>

            </ul>
            <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24"
                    height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                </svg></div>
        </div>
    </div>
    <!--/APP-SIDEBAR-->
</div>