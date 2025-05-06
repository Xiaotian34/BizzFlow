<?php
    echo '
        <footer class="footer">
            <div class="footer-content">
                <p>&copy; <?php echo date("Y"); ?> BizzFlow. Todos los derechos reservados.</p>
                <ul class="footer-links">
                    <li><a href="index.php?controlador=politicas&action=privacidad">Política de Privacidad</a></li>
                    <li><a href="index.php?controlador=politicas&action=terminos">Términos</a></li>
                    <li><a href="index.php?controlador=contactar&action=home">Contacto</a></li>
                </ul>
            </div>
        </footer>
    ';
?>