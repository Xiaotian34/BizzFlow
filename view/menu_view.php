<?php
if (!isset($_SESSION["correo"])) {
    echo '
        <header>
            <h1 href="index.php>Aqui irá el logo</h1>
            <ul>
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
            <h1>Aqui irá el logo
                <a href="index.php"></a>
            </h1>
            <ul>
                <div class="dropdown">
                <button onclick="myFunction()" class="dropbtn">Dropdown</button>
                    <div id="myDropdown" class="dropdown-content">
                        <input type="text" placeholder="Search.." id="myInput" onkeyup="filterFunction()">
                        <a href="index.php?controlador=contactar&action=home">Contactar</a>
                        <a href="index.php?controlador=documentos&action=gestionarDocumentos">Gestionar documentos</a>
                        <a href="index.php?controlador=usuarios&action=gestionarUsuarios">Gestionar usuarios</a>
                    </div>
                </div>
                <div class="dropdown">
                <button onclick="myFunction()" class="dropbtn">Aqui se veria el usuario</button>
                    <div id="myDropdown" class="dropdown-content">
                        <a href="index.php?controlador=usuarios&action=logout">Cerrar sesion</a>
                    </div>
                </div>
            </ul>
        </header>
        ';
}
