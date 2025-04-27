<?php
session_start();

function login(){
    require_once("model/usuarios_model.php");
    $user = new Usuarios_Model();
    $message = "";

    if (!isset($_SESSION["correo"])) {
        if (isset($_POST["login"])) {
            $correo = isset($_POST["correo"]) ? $_POST["correo"] : '';
            $passwd = isset($_POST["passwd"]) ? $_POST["passwd"] : '';

            if ($user->login($correo, $passwd)) {
                $_SESSION["correo"] = $correo;
                header("Location: index.php");
                exit;
            } else {
                $message = "Usuario o contraseña incorrectos";
            }
        }
    }

    require_once("view/login_view.php");
}

function registro(){
    require_once("model/usuarios_model.php");
    $user = new Usuarios_Model();
    $message = "";

    if (!isset($_SESSION["correo"])) {
        if (isset($_POST["regist"])) {
            $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : '';
            $correo = isset($_POST["correo"]) ? $_POST["correo"] : '';
            $passwd = isset($_POST["passwd"]) ? $_POST["passwd"] : '';
            $confirm = isset($_POST["confpasswd"]) ? $_POST["confpasswd"] : '';

            if ($passwd !== $confirm) {
                $message = "Las contraseñas no coinciden";
            } else {
                if ($user->insertar($nombre, $correo, $passwd, 'usuario')) {
                    $message = "Insertado correctamente";
                    header("Location: index.php?controlador=usuarios&action=login");
                    exit;
                } else {
                    $message = "Error al insertar";
                }
            }
        }
    }

    $array_datos = $user->get_usuarios();
    require_once("view/register_view.php");
}

function logout() {
    session_destroy();
    header("Location: index.php");
    exit;
}

function gestionarUsuarios() {
    require_once("model/usuarios_model.php");
    $user = new Usuarios_Model();
    $message = "";

    if (isset($_POST["borrar"])) {
        $correo = isset($_POST["correo"]) ? $_POST["correo"] : '';
        if ($user->eliminarUsuario($correo)) {
            $message = "Borrado correctamente";
        } else {
            $message = "Error al borrar";
        }
    }

    if (isset($_POST["regist"])) {
        $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : '';
        $correo = isset($_POST["correo"]) ? $_POST["correo"] : '';
        $passwd = isset($_POST["passwd"]) ? $_POST["passwd"] : '';

        if ($user->insertar($nombre, $correo, $passwd, 'usuario')) {
            $message = "Insertado correctamente";
        } else {
            $message = "Error al insertar";
        }
    }

    $usuarios = $user->get_usuarios();
    require_once("view/admin_view.php");
}
?>
