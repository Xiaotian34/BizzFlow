<?php
if (!isset($_SESSION["correo"])) {
    echo '
        <header>
            <h1>Galeria</h1>
            <ul>
                <div>
                    <a href="index.php">Inicio</a>
                </div>
                <div>
                    <a href="index.php?controlador=usuarios&action=login">Iniciar Sesion</a>
                    <a href="index.php?controlador=usuarios&action=registro">Registrate</a>
                </div>
            </ul>
        </header>
        ';
} else {
    echo '
        <header>
            <h1>Galeria</h1>
            <ul>
                <div>
                    <a href="index.php">Inicio</a>
                </div>
                <div>
                    <a href="index.php?controlador=contactar&action=home">Contactar</a>
                    <a href="index.php?controlador=documentos&action=gestionarDocumentos">Gestionar documentos</a>
                    <a href="index.php?controlador=usuarios&action=gestionarUsuarios">Gestionar usuarios</a>
                    <a href="index.php?controlador=usuarios&action=logout">Cerrar sesion</a>
                </div>
            </ul>
        </header>
        ';
}
