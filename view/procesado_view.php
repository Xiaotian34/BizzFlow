<?php require_once("menu_view.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Procesando Conversión</title>
    <style>
        body {
            background: var(--light-color);
        }
        .main-content {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-section {
            max-width: 500px;
            margin: 100px auto 0;
            padding: 40px 30px;
            background: var(--background-color, #fff);
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(10,150,166,0.10);
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .success-section h2 {
            color: var(--primary-dark, #076c77);
            margin-bottom: 18px;
            margin-top: 0;
        }
        .success-section p {
            color: var(--dark-color, #0D0D0D);
            font-size: 1.1rem;
        }
        .success-section .btn-primary {
            display: inline-block;
            margin-top: 22px;
            padding: 10px 28px;
            background: var(--primary-color, #0A96A6);
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.2s;
        }
        .success-section .btn-primary:hover {
            background: var(--accent-color, #21BFBF);
        }
    </style>
</head>
<body>
    <main class="main-content">
        <section class="success-section">
            <h2>¡Conversión exitosa!</h2>
            <p>Tu archivo ha sido convertido y guardado correctamente.<br>
            Puedes encontrarlo en tu área de documentos.</p>
            <a href="index.php?controller=documentos&action=gestionarDocumentos" class="btn-primary">Ver mis documentos</a>
        </section>
    </main>

<?php require_once("footer_view.php"); ?>
</body>
</html>