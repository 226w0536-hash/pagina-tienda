<?php
// Importa el modelo Producto para poder realizar operaciones CRUD en la base de datos
require_once 'models/producto.php';

class productoController
{
    /**
     * Página de inicio pública.
     */
    public function index()
    {
        $producto = new Producto();
        $productos = $producto->getRandom(6);
        require_once 'views/producto/destacados.php';
    }

    /**
     * Muestra los productos filtrados por una categoría específica.
     */
    public function categoria()
    {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            // 1. Conseguir la categoría para mostrar su nombre
            $categoria = new Categoria();
            $categoria->setId($id);
            $categoria = $categoria->getOne();

            // 2. Conseguir los productos usando el método de tu modelo
            $producto = new Producto();
            $producto->setCategoria_id($id);
            $productos = $producto->getAllCategory();
        }

        // 3. Cargar la vista de categoría
        require_once 'views/producto/categoria.php';
    }

    /**
     * Muestra la ficha detallada de un producto específico.
     */
    public function ver()
    {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $producto = new Producto();
            $producto->setId($id);
            $product = $producto->getOne();
        }
        require_once 'views/producto/ver.php';
    }

    /**
     * Panel de administración de productos.
     */
    public function gestion()
    {
        Utils::isAdmin();
        $producto = new Producto();
        $productos = $producto->getAll();
        require_once 'views/producto/gestion.php';
    }

    public function crear()
    {
        Utils::isAdmin();
        require_once 'views/producto/crear.php';
    }

    public function save()
    {
        Utils::isAdmin();
        if (isset($_POST)) {
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : false;
            $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : false;
            $precio = isset($_POST['precio']) ? $_POST['precio'] : false;
            $stock = isset($_POST['stock']) ? $_POST['stock'] : false;
            $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : false;

            if ($nombre && $descripcion && $precio && $stock && $categoria) {
                $producto = new Producto();
                $producto->setNombre($nombre);
                $producto->setDescripcion($descripcion);
                $producto->setPrecio($precio);
                $producto->setStock($stock);
                $producto->setCategoria_id($categoria);

                if (isset($_FILES['imagen'])) {
                    $file = $_FILES['imagen'];
                    $filename = $file['name'];
                    $mimetype = $file['type'];

                    if ($mimetype == "image/jpg" || $mimetype == 'image/jpeg' || $mimetype == 'image/png' || $mimetype == 'image/gif') {
                        if (!is_dir('uploads/images')) {
                            mkdir('uploads/images', 0777, true);
                        }
                        $producto->setImagen($filename);
                        move_uploaded_file($file['tmp_name'], 'uploads/images/' . $filename);
                    }
                }

                if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                    $producto->setId($id);
                    $save = $producto->edit();
                } else {
                    $save = $producto->save();
                }

                $_SESSION['producto'] = ($save) ? "complete" : "failed";
            } else {
                $_SESSION['producto'] = "failed";
            }
        } else {
            $_SESSION['producto'] = "failed";
        }
        header('Location:' . base_url . 'producto/gestion');
    }

    public function editar()
    {
        Utils::isAdmin();
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $edit = true;
            $producto = new Producto();
            $producto->setId($id);
            $pro = $producto->getOne();
            require_once 'views/producto/crear.php';
        } else {
            header('Location:' . base_url . 'producto/gestion');
        }
    }

    public function eliminar()
    {
        Utils::isAdmin();
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $producto = new Producto();
            $producto->setId($id);
            $delete = $producto->delete();
            $_SESSION['delete'] = ($delete) ? 'complete' : 'failed';
        } else {
            $_SESSION['delete'] = 'failed';
        }
        header('Location:' . base_url . 'producto/gestion');
    }
}
