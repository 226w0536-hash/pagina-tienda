<?php
// Importa el modelo Producto para poder realizar operaciones CRUD en la base de datos
require_once 'models/producto.php';

// Definición del controlador que gestiona todo el catálogo de productos (público y administrativo)
class productoController
{

	/**
	 * Página de inicio pública de la tienda.
	 * Consigue unos cuantos productos aleatorios para mostrarlos como destacados.
	 */
	public function index()
	{
		$producto = new Producto();
		// Obtiene 6 productos al azar desde la base de datos
		$productos = $producto->getRandom(6);

		// Carga la vista encargada de renderizar los productos destacados en la Home
		require_once 'views/producto/destacados.php';
	}

	/**
	 * Muestra la ficha detallada de un producto específico seleccionado por el cliente.
	 */
	public function ver()
	{
		// Verifica si se recibe el ID del producto por la URL (parámetro GET)
		if (isset($_GET['id'])) {
			$id = $_GET['id'];

			$producto = new Producto();
			$producto->setId($id);

			// Obtiene los datos de ese producto único (nombre, precio, stock, descripción, etc.)
			$product = $producto->getOne();
		}

		// Carga la vista para mostrar la ficha técnica y botón de compra del producto
		require_once 'views/producto/ver.php';
	}

	/**
	 * Lista todos los productos en una tabla para su administración.
	 * Vista exclusiva para administradores.
	 */
	public function gestion()
	{
		// Control de seguridad: bloquea el acceso si el usuario no es administrador
		Utils::isAdmin();

		$producto = new Producto();
		// Extrae el universo completo de productos guardados en la tienda
		$productos = $producto->getAll();

		// Carga la vista del panel de control/inventario
		require_once 'views/producto/gestion.php';
	}

	/**
	 * Muestra el formulario para registrar un nuevo producto en la tienda.
	 * Vista exclusiva para administradores.
	 */
	public function crear()
	{
		Utils::isAdmin();
		// Carga la vista con el formulario vacío de creación
		require_once 'views/producto/crear.php';
	}

	/**
	 * Procesa los datos de un formulario (POST) para CREAR un producto nuevo o EDITAR uno existente.
	 * Acción exclusiva para administradores.
	 */
	public function save()
	{
		Utils::isAdmin();
		// Verifica si llegaron datos mediante el método POST
		if (isset($_POST)) {
			// Recoge y valida la existencia de cada campo del formulario (asigna false si faltan)
			$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : false;
			$descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : false;
			$precio = isset($_POST['precio']) ? $_POST['precio'] : false;
			$stock = isset($_POST['stock']) ? $_POST['stock'] : false;
			$categoria = isset($_POST['categoria']) ? $_POST['categoria'] : false;
			// $imagen = isset($_POST['imagen']) ? $_POST['imagen'] : false;

			// Valida de forma estricta que ningún campo obligatorio sea falso/vacío
			if ($nombre && $descripcion && $precio && $stock && $categoria) {
				// Instancia el modelo y mapea las variables recolectadas usando los Setters
				$producto = new Producto();
				$producto->setNombre($nombre);
				$producto->setDescripcion($descripcion);
				$producto->setPrecio($precio);
				$producto->setStock($stock);
				$producto->setCategoria_id($categoria);

				// --- PROCESAMIENTO Y SUBIDA DE LA IMAGEN ---
				if (isset($_FILES['imagen'])) {
					$file = $_FILES['imagen'];
					$filename = $file['name'];	// Nombre original del archivo (ej: zapato.png)
					$mimetype = $file['type'];	// Formato del archivo (ej: image/png)

					// Filtro de seguridad: Solo permite formatos de imagen estándar
					if ($mimetype == "image/jpg" || $mimetype == 'image/jpeg' || $mimetype == 'image/png' || $mimetype == 'image/gif') {

						// Si la carpeta de destino 'uploads/images' no existe, la crea con permisos de escritura
						if (!is_dir('uploads/images')) {
							mkdir('uploads/images', 0777, true);
						}

						// Asigna el nombre del archivo al objeto producto
						$producto->setImagen($filename);
						// Mueve físicamente la imagen desde la carpeta temporal del servidor a la ruta final de tu proyecto
						move_uploaded_file($file['tmp_name'], 'uploads/images/' . $filename);
					}
				}

				// --- DETERMINAR SI ES ACTUALIZACIÓN O REGISTRO NUEVO ---
				// Si la URL contiene un ID (?id=X), significa que el administrador está editando un registro viejo
				if (isset($_GET['id'])) {
					$id = $_GET['id'];
					$producto->setId($id);

					$save = $producto->edit();	// Ejecuta una consulta UPDATE en la BD
				} else {
					$save = $producto->save();	// Ejecuta una consulta INSERT INTO en la BD
				}

				// Crea variables de sesión informativas para lanzar alertas de éxito o error en la vista
				if ($save) {
					$_SESSION['producto'] = "complete";
				} else {
					$_SESSION['producto'] = "failed";
				}
			} else {
				$_SESSION['producto'] = "failed";
			}
		} else {
			$_SESSION['producto'] = "failed";
		}
		// Redirige al panel de administración de productos para ver los cambios reflejados
		header('Location:' . base_url . 'producto/gestion');
	}

	/**
	 * Recupera la información de un producto específico y abre el formulario de creación
	 * pero precargado con los datos listos para ser modificados.
	 */
	public function editar()
	{
		Utils::isAdmin();
		// Verifica que se especifique qué producto se desea editar mediante la URL
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$edit = true;		// Variable bandera que le dice a la vista: "Cambia el título a 'Editar' y llena los inputs"

			$producto = new Producto();
			$producto->setId($id);

			// Consigue la información actual del producto antes de modificarlo
			$pro = $producto->getOne();

			// Reutiliza la vista 'crear.php' (sirve tanto para crear como para editar)
			require_once 'views/producto/crear.php';
		} else {
			header('Location:' . base_url . 'producto/gestion');
		}
	}

	/**
	 * Elimina de forma física un producto del catálogo mediante su ID.
	 * Acción exclusiva para administradores.
	 */
	public function eliminar()
	{
		Utils::isAdmin();

		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$producto = new Producto();
			$producto->setId($id);

			// Ejecuta el query DELETE en la base de datos
			$delete = $producto->delete();

			// Almacena el resultado en sesión para mostrar una notificación (Éxito/Error)
			if ($delete) {
				$_SESSION['delete'] = 'complete';
			} else {
				$_SESSION['delete'] = 'failed';
			}
		} else {
			$_SESSION['delete'] = 'failed';
		}

		// Redirige al inventario general
		header('Location:' . base_url . 'producto/gestion');
	}
}
