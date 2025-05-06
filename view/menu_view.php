<?php
if (!isset($_SESSION["correo"])) {
    echo '
        <header>
            <div class="navbar">
                <div class="navbar-left">
                    <a href="index.php" class="logo-link">
                        <img src="img/logoBF.png" alt="Logo BizzFlow">
                        <h2>BizzFlow</h2>
                    </a>
                </div>
                <div class="navbar-right">
                    <a class="sign-up" href="index.php?controlador=usuarios&action=registro">Regístrate</a>
                    <a class="sign-in" href="index.php?controlador=usuarios&action=login">Iniciar Sesión</a>
                </div>
            </div>
        </header>
        ';
} else {
    echo '
        <header>
            <div class="navbar">
                <div class="navbar-left">
                    <a href="index.php" class="logo-link">
                        <img src="img/logoBF.png" alt="Logo BizzFlow">
                        <h2>BizzFlow</h2>
                    </a>
                </div>
                <label class="menu-toggle">
                    <input type="checkbox" id="menuCheckbox" onclick="toggleSlider()" />
                    <svg viewBox="0 0 32 32">
                        <path
                            class="line line-top-bottom"
                            d="M27 10 13 10C10.8 10 9 8.2 9 6 9 3.5 10.8 2 13 2 15.2 2 17 3.8 17 6L17 26C17 28.2 18.8 30 21 30 23.2 30 25 28.2 25 26 25 23.8 23.2 22 21 22L7 22"
                        ></path>
                        <path class="line" d="M7 16 27 16"></path>
                    </svg>
                </label>
            </div>
        </header>
        <div id="menuSlider" class="slider-menu">
        <a href="index.php?controlador=usuarios&action=perfil">Mi perfil</a>
        <a href="index.php?controlador=documentos&action=gestionarDocumentos">Mis documentos</a>
            <a href="index.php?controlador=contactar&action=home">Contactar</a>
            <a href="index.php?controlador=usuarios&action=logout">Cerrar sesión</a>
        </div>
        ';
}