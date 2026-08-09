<?php
// cerreamos la sesion del usuario y volvemos a la pagina de inicio.
session_start();
session_destroy();
header("Location:./index.php");
exit;
?>
