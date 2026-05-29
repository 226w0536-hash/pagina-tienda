<?php
// Importa el modelo de Producto para poder consultar la base de datos cuando se añada un artículo
require_once 'models/producto.php';

// Definición del controlador que gestiona todas las acciones del carrito de compras
class carritoController
{

    /**
     * Muestra la vista del carrito con los productos agregados.
     */
    public function index()
    {
        // Verifica si la sesión del carrito existe y si tiene al menos un producto
        if (isset($_SESSION['carrito']) && count($_SESSION['carrito']) >= 1) {
            $carrito = $_SESSION['carrito'];
        } else {
            $carrito = array();
        }
        // Carga la vista del carrito
        require_once 'views/carrito/index.php';
    }

    /**
     * Añade un producto al carrito de compras.
     */
    public function add()
    {
        // 1. Verificación segura del ID
        if (isset($_GET['id'])) {
            $producto_id = $_GET['id'];
        } else {
            // Redirige al inicio y detiene la ejecución inmediatamente
            header('Location:' . base_url);
            exit(); 
        }

        // 2. Lógica para buscar el producto y actualizar cantidades
        if (isset($_SESSION['carrito'])) {
            $counter = 0;

            foreach ($_SESSION['carrito'] as $indice => $elemento) {
                if ($elemento['id_producto'] == $producto_id) {
                    $_SESSION['carrito'][$indice]['unidades']++;
                    $counter++;
                }
            }
        }

        // 3. Si el producto NO existía en el carrito, lo agregamos
        if (!isset($counter) || $counter == 0) {
            $producto = new Producto();
            $producto->setId($producto_id);
            $producto = $producto->getOne();

            if (is_object($producto)) {
                $_SESSION['carrito'][] = array(
                    "id_producto" => $producto->id,
                    "precio" => $producto->precio,
                    "unidades" => 1,
                    "producto" => $producto
                );
            }
        }

        // 4. Redirección final al carrito con exit() para evitar conflictos
        header("Location:" . base_url . "carrito/index");
        exit();
    }

    /**
     * Elimina un producto específico del carrito.
     */
    public function delete()
    {
        if (isset($_GET['index'])) {
            $index = $_GET['index'];
            unset($_SESSION['carrito'][$index]);
        }
        header("Location:" . base_url . "carrito/index");
        exit();
    }

    /**
     * Incrementa la cantidad de un producto.
     */
    public function up()
    {
        if (isset($_GET['index'])) {
            $index = $_GET['index'];
            $_SESSION['carrito'][$index]['unidades']++;
        }
        header("Location:" . base_url . "carrito/index");
        exit();
    }

    /**
     * Decrementa la cantidad de un producto.
     */
    public function down()
    {
        if (isset($_GET['index'])) {
            $index = $_GET['index'];
            $_SESSION['carrito'][$index]['unidades']--;

            if ($_SESSION['carrito'][$index]['unidades'] == 0) {
                unset($_SESSION['carrito'][$index]);
            }
        }
        header("Location:" . base_url . "carrito/index");
        exit();
    }

    /**
     * Vacía el carrito por completo.
     */
    public function delete_all()
    {
        unset($_SESSION['carrito']);
        header("Location:" . base_url . "carrito/index");
        exit();
    }
}
