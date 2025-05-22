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
                $_SESSION["nombre"] = $user->obtenerNombreUsuarioPorCorreo($correo);
                $_SESSION["tipo"] = $user->obtenerTipoUsuario($correo);
                header("Location: index.php");
                exit;
            } else {
                $message = "Usuario o contraseña incorrectos";
            }
        }
    }

    require_once("view/login_view.php");
}

function googleSignIn() {
    require_once("model/usuarios_model.php");
    $usuarioModel = new Usuarios_Model();

    $input = json_decode(file_get_contents('php://input'), true);

    $nombre = $input['nombre'];
    $correo = $input['correo'];
    $google_id = $input['google_id'];

    // Verifica si el usuario ya existe en la base de datos
    $usuario = $usuarioModel->obtenerIdUsuarioPorCorreo($correo);

    if ($usuario) {
        $_SESSION['correo'] = $correo;
        $_SESSION["tipo"] = $usuarioModel->obtenerTipoUsuario($correo);
        echo json_encode(['success' => true]);
    } else {
        // Registra al usuario si no existe (por defecto como 'usuario')
        $usuarioModel->registroGoogle($nombre, $correo, null, $google_id);
        $_SESSION['correo'] = $correo;
        $_SESSION["tipo"] = 'usuario';
        echo json_encode(['success' => true]);
    }
    exit;
}

function registro(){
    require_once("model/usuarios_model.php");
    $user = new Usuarios_Model();
    $message = "";

    if (!isset($_SESSION["correo"])) {
        if (isset($_POST["regist"])) {
            $nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : '';
            $apellidos = isset($_POST["apellidos"]) ? trim($_POST["apellidos"]) : '';
            $edad = isset($_POST["edad"]) ? (int)$_POST["edad"] : 0;
            $correo = isset($_POST["correo"]) ? trim($_POST["correo"]) : '';
            $passwd = isset($_POST["passwd"]) ? $_POST["passwd"] : '';
            $confirm = isset($_POST["confpasswd"]) ? $_POST["confpasswd"] : '';
            $tipo = isset($_POST["tipo"]) ? $_POST["tipo"] : 'usuario';

            if (empty($nombre) || empty($apellidos) || empty($edad) || empty($correo) || empty($passwd) || empty($confirm) || empty($tipo)) {
                $message = "Todos los campos son obligatorios";
            } else if ($passwd !== $confirm) {
                $message = "Las contraseñas no coinciden";
            } else if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $message = "El correo electrónico no es válido";
            } else if ($edad < 18) {
                $message = "Debes ser mayor de edad para registrarte";
            } else {
                if ($user->insertar($nombre, $apellidos, $edad, $correo, $passwd, $tipo)) {
                    $_SESSION["correo"] = $correo;
                    $_SESSION["nombre"] = $nombre;
                    $_SESSION["tipo"] = $tipo;
                    header("Location: index.php");
                    exit;
                } else {
                    $message = "Error al registrar el usuario. El correo puede que ya esté en uso.";
                }
            }
        }
    } else {
        header("Location: index.php");
        exit;
    }

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
        $apellidos = isset($_POST["apellidos"]) ? $_POST["apellidos"] : '';
        $edad = isset($_POST["edad"]) ? (int)$_POST["edad"] : 0;
        $correo = isset($_POST["correo"]) ? $_POST["correo"] : '';
        $passwd = isset($_POST["passwd"]) ? $_POST["passwd"] : '';
        $tipo = isset($_POST["tipo"]) ? $_POST["tipo"] : 'usuario';

        if ($user->insertar($nombre, $apellidos, $edad, $correo, $passwd, $tipo)) {
            $message = "Insertado correctamente";
        } else {
            $message = "Error al insertar";
        }
    }

    $usuarios = $user->get_usuarios();
    require_once("view/admin_view.php");
}

function perfil() {
    require_once("model/usuarios_model.php");
    require_once("model/documentos_model.php");
    $user = new Usuarios_Model();
    $documentosModel = new Documentos_Model();
    $message = "";

    // Handle profile update
    if (isset($_POST["actualizar"])) {
        $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : '';
        $apellidos = isset($_POST["apellidos"]) ? $_POST["apellidos"] : '';
        $edad = isset($_POST["edad"]) ? (int)$_POST["edad"] : 0;
        $correo = isset($_POST["correo"]) ? $_POST["correo"] : '';
        $passwd = isset($_POST["passwd"]) ? $_POST["passwd"] : '';

        // Update user information
        if ($user->actualizarUsuario($_SESSION["correo"], $nombre, $apellidos, $edad, $correo, $passwd)) {
            $_SESSION["nombre"] = $nombre;
            $_SESSION["correo"] = $correo;
            $message = "Datos actualizados correctamente.";
        } else {
            $message = "Error al actualizar los datos.";
        }
    }

    // Handle password change
    if (isset($_POST["actualizar_passwd"])) {
        $nueva_passwd = isset($_POST["cambiar_passwd"]) ? $_POST["cambiar_passwd"] : '';

        if ($user->cambiarContrasena($_SESSION["correo"], $nueva_passwd)) {
            $message = "Contraseña actualizada correctamente.";
        } else {
            $message = "Error al cambiar la contraseña.";
        }
    }

    // Handle privacy settings
    if (isset($_POST["guardar_privacidad"])) {
        $privacidad = isset($_POST["privacidad"]) ? $_POST["privacidad"] : '';
        // Save privacy settings logic here (if applicable)
        $message = "Configuración de privacidad guardada.";
    }

    // Handle profile picture upload
    if (isset($_FILES["foto_perfil"]) && $_FILES["foto_perfil"]["error"] == 0) {
        $allowed = ["jpg" => "image/jpeg", "jpeg" => "image/jpeg", "png" => "image/png", "gif" => "image/gif"];
        $filename = $_FILES["foto_perfil"]["name"];
        $filetype = $_FILES["foto_perfil"]["type"];
        $filesize = $_FILES["foto_perfil"]["size"];

        // Verify file extension
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!array_key_exists($ext, $allowed)) {
            $message = "Error: Por favor selecciona un formato válido.";
        }

        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) {
            $message = "Error: El tamaño de la imagen excede el límite de 5MB.";
        }

        // Verify MYME type of the file
        if (in_array($filetype, $allowed)) {
            if (!file_exists("img/imgPerfil")) {
                mkdir("img/imgPerfil", 0777, true);
            }

            $new_filename = uniqid() . "." . $ext;
            $target = "img/imgPerfil/" . $new_filename;

            if (move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $target)) {
                if ($user->actualizarImagenPerfil($_SESSION["correo"], $target)) {
                    $_SESSION["imagen_perfil"] = $target;
                    $message = "La imagen de perfil se ha actualizado correctamente.";
                } else {
                    $message = "Error al actualizar la imagen en la base de datos.";
                }
            } else {
                $message = "Error al subir la imagen.";
            }
        }
    }

    // Fetch user documents
    $id_usuario = $user->obtenerIdUsuarioPorCorreo($_SESSION["correo"]);
    $array_documentos = $documentosModel->get_documentos_por_usuario($id_usuario);

    require_once("view/perfil_view.php");
}

// Routing logic
if (isset($_GET['action']) && $_GET['action'] === 'googleSignIn') {
    googleSignIn();
}
?>
