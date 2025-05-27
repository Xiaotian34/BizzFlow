<?php
require_once("model/usuarios_model.php");
$user = new Usuarios_Model();

if (!isset($_SESSION["correo"])) {
    echo '
        <header>
            <div class="navbar">
                <a href="index.php" class="logo-link">
                    <img src="img/logoBF.png" alt="Logo BizzFlow">
                    <h2>BizzFlow</h2>
                </a>
                <div class="navbar-actions">
                    <a class="sign-up" href="index.php?controlador=usuarios&action=registro">Regístrate</a>
                    <a class="sign-in" href="index.php?controlador=usuarios&action=login">Iniciar Sesión</a>
                </div>
            </div>
        </header>
        ';
} else {
    if ($_SESSION["tipo"] !== "admin") {
        echo '
            <header>
                <div class="navbar">
                    <a href="index.php" class="logo-link">
                        <img src="img/logoBF.png" alt="Logo BizzFlow">
                        <h2>BizzFlow</h2>
                    </a>
                    <div class="navbar-actions">
                        <div class="user-info" id="userInfo" onclick="toggleSlider()">
                            <img src="'; ?><?php echo $user->obtenerImagenPerfil($_SESSION['correo']); ?><?php echo '" alt="User Icon">
                            <div class="user-details">
                                <span class="username">'; ?><?php echo $_SESSION["nombre"]; ?><?php echo '</span>
                                <span class="subtitle">'; ?><?php echo $_SESSION["tipo"]; ?><?php echo '</span>
                            </div>
                            <span class="arrow" id="arrow">☰</span>
                        </div>
                    </div>
                </div>
                <div id="menuSlider" class="slider-menu">
                    <a href="index.php?controlador=usuarios&action=perfil">Mi perfil</a>
                    <a href="index.php?controlador=usuarios&action=logout">Cerrar sesión</a>
                    <hr style="border: 0; border-top: 1px solid #444; margin: 10px 0;">
                    <a href="index.php?controlador=documentos&action=gestionarDocumentos">Mis documentos</a>
                </div>
            </header>
        ';
    } else {
        echo '
            <header>
                <div class="navbar">
                    <a href="index.php" class="logo-link">
                        <img src="img/logoBF.png" alt="Logo BizzFlow">
                        <h2>BizzFlow</h2>
                    </a>
                    <div class="navbar-actions">
                        <div class="user-info" id="userInfo" onclick="toggleSlider()">
                            <img src="'; ?><?php echo $user->obtenerImagenPerfil($_SESSION['correo']); ?><?php echo '" alt="User Icon">
                            <div class="user-details">
                                <span class="username">'; ?><?php echo $_SESSION["nombre"]; ?><?php echo '</span>
                                <span class="subtitle">'; ?><?php echo $_SESSION["tipo"]; ?><?php echo '</span>
                            </div>
                            <span class="arrow" id="arrow">☰</span>
                        </div>
                    </div>
                </div>
                <div id="menuSlider" class="slider-menu">
                    <a href="index.php?controlador=usuarios&action=perfil">Mi perfil</a>
                    <a href="index.php?controlador=usuarios&action=logout">Cerrar sesión</a>
                    <hr style="border: 0; border-top: 1px solid #444; margin: 10px 0;">
                    <a href="index.php?controlador=usuarios&action=gestionarUsuarios">Gestionar Usuarios</a>
                </div>
            </header>
        ';
    }    
}
?>