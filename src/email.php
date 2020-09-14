<?php
$para = 'residente007@yahoo.es';
$asunto = 'Esta es una prueba';
$contenido = 'Esto sigue siendo una prueba';
$header = 'From: julio.herazo@admintaxi.com';
mail($para, $asunto, $contenido,$header);
?>