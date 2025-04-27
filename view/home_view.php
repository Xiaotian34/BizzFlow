<?php
require_once("view/menu_view.php");

require_once("model/documentos_model.php");
$documentos = new Documentos_Model();
$array = $documentos->get_documentos();
?>

    <div class="slideshow-container" id="slideshow-container">
        <?php foreach ($array as $documento) {
        ?>
            <div class="mySlides fade">
                <img src="<?php echo $documento['nombre_fichero'] ?>" style="width:100%">
                <div class="text">
                    <strong>Nombre del archivo:</strong> <?php echo htmlspecialchars($documento['nombre_archivo']); ?><br>
                    <strong>Tipo:</strong> <?php echo htmlspecialchars($documento['autor']); ?><br>
                    <strong>Ruta del archivo:</strong> <?php echo htmlspecialchars($documento['ruta_archivo']); ?><br>
                    <strong>Fecha de subida:</strong> <?php echo htmlspecialchars($documento['fecha_subida']); ?>
                </div>
            </div>
        <?php
        }
        ?>

        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>
    </div>

    <div style="text-align:center" id="dots-container">
        <?php for ($i = 0; $i < count($array); $i++): ?>
            <span class="dot" onclick="currentSlide(<?= $i + 1 ?>)"></span>
        <?php endfor; ?>
    </div>

