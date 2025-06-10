<?php 
//db_con.php
$db_con = new mysqli("localhost", "root", "*WFDFDQ45DSFLKRITIUECMLKKAIKDSDSDSDWESASASASastyhdfdff", "evaluacion");

if ($db_con->connect_error) {
    die("Error de conexión: " . $db_con->connect_error);
}
$db_con->set_charset("utf8");

?>