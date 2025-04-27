<?php
session_start();

function home(){
    $to_email = isset($_POST["correo"])?$_POST["correo"]:'';
    $subject = isset($_POST["asunto"])?$_POST["asunto"]:'';
    $body = isset($_POST["mensaje"])?$_POST["mensaje"]:'';
    $headers = "From: prueba@localhost";
    if (isset($_POST["submit"])) {
        if (mail($to_email, $subject, $body, $headers)) {
        echo "Email successfully sent to $to_email...";
        } else {
        echo "Email sending failed...";
        }
    }
    require_once("view/contactar_view.php");
}
?>