<?php
// Importa los modelos necesarios para interactuar con las tablas de la base de datos
require_once 'models/categoria.php';
require_once 'models/producto.php';

// Definición del controlador que gestiona las categorías de la tienda online
class categoriaController
{

	/**
	 * Muestra el listado de todas las categorías disponibles.
	 * Vista exclusiva para administradores.
	 */
	public function index()
	{
		// Restringe el acceso: si el usuario no es administrador, este método detiene la ejecución o lo redirige
		Utils::isAdmin();

		// Instancia el modelo Categoria para consultar la base de datos
		$categoria = new Categoria();

		// Obtiene todos los registros de la tabla categorías y los guarda en $categorias
		$categorias = $categoria->getAll();

		// Carga la vista del panel de administración para listar las categorías
		require_once 'views/categoria/index.php';
	}

	/**
	 * Muestra una categoría específica vista desde el lado del cliente,
	 * listando todos los productos que pertenecen a ella.
	 */
	public function ver()
	{
		// Verifica si se ha enviado el ID de la categoría a través de la URL (método GET)
		if (isset($_GET['id'])) {
			$id = $_GET['id'];

			// --- Conseguir los datos de la categoría seleccionada ---
			$categoria = new Categoria();
			$categoria->setId($id);
			$categoria = $categoria->getOne();

			// --- Conseguir todos los productos asociados a esa categoría ---
			$producto = new Producto();
			$producto->setCategoria_id($id);
			$productos = $producto->getAllCategory();
		}

		// Carga la vista pública donde se muestran los productos de la categoría seleccionada
		require_once 'views/categoria/ver.php';
	}

	/**
	 * Muestra el formulario para crear una nueva categoría.
	 * Vista exclusiva para administradores.
	 */
	public function crear()
	{
		// Verifica seguridad: solo un administrador puede ver este formulario
		Utils::isAdmin();
		// Carga la vista con el formulario de creación
		require_once 'views/categoria/crear.php';
	}

	/**
	 * Procesa los datos del formulario de creación y los guarda en la base de datos.
	 * Acción exclusiva para administradores.
	 */
	public function save()
	{
		// Verifica seguridad: solo un administrador puede ejecutar el guardado
		Utils::isAdmin();

		// Valida que la petición venga por POST y que el campo 'nombre' no esté vacío
		if (isset($_POST) && isset($_POST['nombre'])) {
			// Instancia el modelo Categoria para interactuar con la BD
			$categoria = new Categoria();
			// Asigna el nombre recibido del formulario al objeto mediante el Setter
			$categoria->setNombre($_POST['nombre']);
			// Ejecuta el método que inserta el nuevo registro en la base de datos
			$save = $categoria->save();
		}

		// Redirige al administrador de vuelta al listado principal de categorías
		header("Location:" . base_url . "categoria/index");
	}
}