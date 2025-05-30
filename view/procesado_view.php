<?php require_once("menu_view.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Procesando Conversión</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .procesando-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            text-align: center;
        }
        .loader {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            animation: spin 1.2s linear infinite;
            margin-bottom: 30px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg);}
            100% { transform: rotate(360deg);}
        }
        .procesando-title {
            font-size: 2rem;
            color: var(--primary, #3498db);
            margin-bottom: 10px;
        }
        .procesando-text {
            color: #555;
            font-size: 1.1rem;
        }
        .success-section {
            max-width: 500px;
            margin: 60px auto;
            padding: 40px 30px;
            background: #f8fafd;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(52,152,219,0.08);
            text-align: center;
        }
        .success-section h2 {
            color: #27ae60;
            margin-bottom: 18px;
        }
        .success-section .btn-primary {
            display: inline-block;
            margin-top: 22px;
            padding: 10px 28px;
            background: #3498db;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.2s;
        }
        .success-section .btn-primary:hover {
            background: #217dbb;
        }
    </style>
</head>
<body>
    <main class="main-content">
        <section class="success-section">
            <h2>¡Conversión exitosa!</h2>
            <p>Tu archivo ha sido convertido y guardado correctamente.<br>
            Puedes encontrarlo en tu área de documentos.</p>
            <a href="index.php?controller=documentos&action=listar" class="btn-primary">Ver mis documentos</a>
        </section>
    </main>

<?php require_once("footer_view.php"); ?>
</body>
</html>