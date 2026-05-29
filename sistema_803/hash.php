<?php
$password = "contraseña"; // Pon aquí la contraseña que quieras para el admin
$hash = password_hash($password, PASSWORD_BCRYPT);
echo "El hash es: " . $hash;
?>
