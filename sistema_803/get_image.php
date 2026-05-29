<?php
// Conecta a tu base de datos aquí (o incluye tu archivo de conexión)
require_once 'config/db.php'; 

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $db = Database::connect(); // Ajusta según tu clase de conexión
    
    // Consulta la imagen en la BD
    $sql = "SELECT imagen FROM productos WHERE id = $id";
    $query = $db->query($sql);
    $producto = $query->fetch_object();
    
    if ($producto && $producto->imagen) {
        // Enviar cabecera de imagen al navegador
        header("Content-type: image/jpeg"); // O image/png dependiendo del formato
        echo $producto->imagen; // Imprime los datos binarios
    }
}
?>
