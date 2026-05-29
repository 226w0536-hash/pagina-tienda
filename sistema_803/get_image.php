<?php
// Conexión a la base de datos
require_once 'config/db.php';

// Si no hay ID, no hacemos nada
if (!isset($_GET['id'])) exit;

$id = (int)$_GET['id'];
$db = Database::connect();

// Preparamos la consulta
$sql = "SELECT imagen FROM productos WHERE id = $id";
$query = $db->query($sql);

if ($query && $query->num_rows == 1) {
    $producto = $query->fetch_object();
    
    // Verificamos si realmente hay datos binarios
    if ($producto->imagen) {
        // IMPORTANTE: Limpiamos cualquier salida previa (evita que se imprima texto)
        if (ob_get_length()) ob_end_clean();
        
        // Enviamos las cabeceras para que el navegador sepa que es una imagen
        header("Content-Type: image/jpeg");
        header("Content-Length: " . strlen($producto->imagen));
        
        // Imprimimos el contenido binario
        echo $producto->imagen;
        exit();
    }
}
?>
