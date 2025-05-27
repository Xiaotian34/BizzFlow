<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylesHome.css">
    <title>Document</title>
</head>
<body>
    <?php
    require_once("menu_view.php");

    if (!isset($_SESSION['correo'])):
    ?>
    <main class="presentation-main">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <h1>Convierte tus archivos en facturas XML de forma rápida y profesional</h1>
                <p>Optimiza tu tiempo y obtén resultados precisos con nuestra herramienta diseñada para autónomos y pymes.</p>
                <a href="index.php?controlador=usuarios&action=registro" class="cta-button">Empezar Ahora</a>
            </div>
            <div class="hero-img">
                <img src="img/pajaroMascotaHD.png" alt="nombre del pajaro">
            </div>
        </section>

        <!-- Características -->
        <section class="features">
            <h2>Características Principales</h2>
            <div class="features-grid">
                <div class="card">
                    <div class="card-details">
                        <p class="text-title">Convierte desde Excel, Word y más</p>
                        <p class="text-body">Soporte para múltiples formatos de archivo.</p>
                    </div>
                    <button class="card-button">Más info</button>
                </div>
                <div class="card">
                    <div class="card-details">
                        <p class="text-title">Generación automática de XML</p>
                        <p class="text-body">Genera facturas en formato XML de manera instantánea.</p>
                    </div>
                    <button class="card-button">Más info</button>
                </div>
                <div class="card">
                    <div class="card-details">
                        <p class="text-title">Vista previa de factura</p>
                        <p class="text-body">Revisa tus facturas antes de exportarlas.</p>
                    </div>
                    <button class="card-button">Más info</button>
                </div>
                <div class="card">
                    <div class="card-details">
                        <p class="text-title">Exportación a PDF</p>
                        <p class="text-body">Descarga tus facturas en formato PDF con un solo clic.</p>
                    </div>
                    <button class="card-button">Más info</button>
                </div>
            </div>
        </section>

        <!-- Beneficios -->
        <section class="testimonials">
            <h2>Beneficios de Usar BizzFlow</h2>
            <div class="testimonials-grid">
                <div class="card">
                    <div class="card-details">
                        <p class="text-title">Ahorra tiempo</p>
                        <p class="text-body">Automatiza procesos y reduce el tiempo dedicado a tareas repetitivas.</p>
                    </div>
                    <button class="card-button">Más info</button>
                </div>
                <div class="card">
                    <div class="card-details">
                        <p class="text-title">Ideal para autónomos y pymes</p>
                        <p class="text-body">Diseñado para satisfacer las necesidades de pequeñas empresas.</p>
                    </div>
                    <button class="card-button">Más info</button>
                </div>
                <div class="card">
                    <div class="card-details">
                        <p class="text-title">Resultados profesionales</p>
                        <p class="text-body">Obtén facturas con un formato profesional y validación fiscal.</p>
                    </div>
                    <button class="card-button">Más info</button>
                </div>
            </div>
        </section>
    </main>

    <?php
    else:
    ?>

    <main>
        <h1>Elige lo que quieres hacer</h1>

        <div class="feature-grid">
            <!-- Conversión a XML -->
            <div class="feature-card">
                <i class="fas fa-file-excel"></i>
                <h3>Excel a XML</h3>
                <p>Convierte tus hojas de cálculo Excel a un formato XML estructurado.</p>
                <button class="feature-button" onclick="window.location.href='index.php?controlador=documentos&action=excelToXmlForm'">Seleccionar</button>
            </div>

            <div class="feature-card">
                <i class="fas fa-file-word"></i>
                <h3>Word a XML</h3>
                <p>Convierte documentos Word a XML para integrarlos en tus sistemas.</p>
                <button class="feature-button">Seleccionar</button>
            </div>
            <div class="feature-card">
                <i class="fas fa-file-pdf"></i>
                <h3>PDF a XML</h3>
                <p>Convierte documentos PDF a XML para integrarlos en tus sistemas.</p>
                <button class="feature-button">Seleccionar</button>
            </div>

            <!-- Conversión inversa -->
            <div class="feature-card">
                <i class="fas fa-file-alt"></i>
                <h3>XML a Excel</h3>
                <p>Convierte facturas XML a hojas de cálculo Excel para editarlas fácilmente.</p>
                <button class="feature-button">Seleccionar</button>
            </div>
            <div class="feature-card">
                <i class="fas fa-file-word"></i>
                <h3>XML a Word</h3>
                <p>Transforma facturas XML en documentos Word reutilizables.</p>
                <button class="feature-button">Seleccionar</button>
            </div>
            
            <!-- Otras funcionalidades -->
            <div class="feature-card">
                <i class="fas fa-file-csv"></i>
                <h3>CSV a XML</h3>
                <p>Transforma tus archivos CSV en XML para un manejo más avanzado.</p>
                <button class="feature-button">Seleccionar</button>
            </div>
            <div class="feature-card">
                <i class="fas fa-file-code"></i>
                <h3>JSON a XML</h3>
                <p>Convierte tus datos JSON a XML para integraciones más flexibles.</p>
                <button class="feature-button">Seleccionar</button>
            </div>
            <div class="feature-card">
                <i class="fas fa-file-google"></i>
                <h3>Google Sheets a XML</h3>
                <p>Exporta tus datos desde Google Sheets a XML con facilidad.</p>
                <button class="feature-button">Seleccionar</button>
            </div>
        </div>
    </main>
    <?php
    endif;
    require_once("footer_view.php");
    ?>

</body>
</html>