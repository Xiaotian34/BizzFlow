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
    $usuario = $usuairoModel->obtenerIdUsuarioPorCorreo($correo);

    if ($usuario) {
        // Inicia sesión si el usuario ya existe
        $_SESSION['correo'] = $correo;
        echo json_encode(['success' => true]);
    } else {
        // Registra al usuario si no existe
        $usuarioModel->registroGoogle($nombre, $correo, null, $google_id);
        $_SESSION['correo'] = $correo;
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
            $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : '';
            $correo = isset($_POST["correo"]) ? $_POST["correo"] : '';
            $passwd = isset($_POST["passwd"]) ? $_POST["passwd"] : '';
            $confirm = isset($_POST["confpasswd"]) ? $_POST["confpasswd"] : '';

            if ($passwd !== $confirm) {
                $message = "Las contraseñas no coinciden";
            } else {
                if ($user->insertar($nombre, $correo, $passwd, 'usuario')) {
                    $message = "Insertado correctamente";
                    $_SESSION["nombre"] = $nombre;
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

function perfil() {
    require_once("model/usuarios_model.php");
    require_once("model/documentos_model.php");
    $user = new Usuarios_Model();
    $documentosModel = new Documentos_Model();
    $message = "";

    // Handle profile update
    if (isset($_POST["actualizar"])) {
        $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : '';
        $correo = isset($_POST["correo"]) ? $_POST["correo"] : '';
        $passwd = isset($_POST["passwd"]) ? $_POST["passwd"] : '';

        // Update user information
        if ($user->actualizarUsuario($_SESSION["correo"], $nombre, $correo, $passwd)) {
            $_SESSION["nombre"] = $nombre; // Update session variable
            $_SESSION["correo"] = $correo; // Update session variable
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

    // Fetch user documents
    $id_usuario = $user->obtenerIdUsuarioPorCorreo($_SESSION["correo"]); // Function to get user ID
    $array_documentos = $documentosModel->get_documentos_por_usuario($id_usuario); // Fetch documents

    require_once("view/perfil_view.php"); // Load the profile view
}

// Routing logic
if ($_GET['action'] === 'googleSignIn') {
    googleSignIn();
}
?>
