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
			// Si hay productos, los asigna a la variable local $carrito
			$carrito = $_SESSION['carrito'];
		} else {
			// Si no existe o está vacío, inicializa $carrito como un arreglo vacío
			$carrito = array();
		}
		// Carga la vista del carrito para mostrar los datos al usuario
		require_once 'views/carrito/index.php';
	}

	/**
	 * Añade un producto al carrito de compras.
	 */
	public function add()
	{
		// Verifica si se ha enviado el ID del producto por la URL (método GET)
		if (isset($_GET['id'])) {
			$producto_id = $_GET['id'];
		} else {
			// Si no hay ID, redirige al usuario a la página de inicio
			header('Location:' . base_url);
		}

		// Si el carrito ya tiene productos guardados...
		if (isset($_SESSION['carrito'])) {
			$counter = 0;	// Bandera para comprobar si el producto ya existía en el carrito

			// Recorre los elementos actuales del carrito buscando coincidencias
			foreach ($_SESSION['carrito'] as $indice => $elemento) {
				// Si el ID del producto que se quiere añadir ya está en el carrito...
				if ($elemento['id_producto'] == $producto_id) {
					// Simplemente incrementa en 1 la cantidad (unidades) de ese producto
					$_SESSION['carrito'][$indice]['unidades']++;
					$counter++; 	// Indica que el producto ya existía y fue actualizado
				}
			}
		}

		// Si el producto NO existía previamente en el carrito (o el carrito estaba totalmente vacío)
		if (!isset($counter) || $counter == 0) {
			// Conseguir producto
			// Instancia el modelo Producto para buscar sus datos en la base de datos
			$producto = new Producto();
			$producto->setId($producto_id);
			$producto = $producto->getOne();	// Obtiene la información del producto (objeto)

			// Añadir al carrito
			// Si se encontró el producto correctamente, lo añade como un nuevo arreglo al carrito
			if (is_object($producto)) {
				$_SESSION['carrito'][] = array(
					"id_producto" => $producto->id,
					"precio" => $producto->precio,
					"unidades" => 1,				// Inicializa la primera unidad
					"producto" => $producto			// Guarda el objeto completo con sus detalles (nombre, imagen, etc.)
				);
			}
		}

		// Redirige al usuario de vuelta a la vista principal del carrito
		header("Location:" . base_url . "carrito/index");
	}

	/**
	 * Elimina un producto específico del carrito usando su índice.
	 */
	public function delete()
	{
		// Verifica si se envió el índice (posición en el array) por la URL
		if (isset($_GET['index'])) {
			$index = $_GET['index'];
			// Destruye/elimina la posición correspondiente en el arreglo de sesión
			unset($_SESSION['carrito'][$index]);
		}
		// Redirige a la vista del carrito
		header("Location:" . base_url . "carrito/index");
	}


	/**
	 * Incrementa en 1 la cantidad de un producto desde la vista del carrito.
	 */
	public function up()
	{
		if (isset($_GET['index'])) {
			$index = $_GET['index'];
			// Suma una unidad al producto en el índice indicado
			$_SESSION['carrito'][$index]['unidades']++;
		}
		header("Location:" . base_url . "carrito/index");
	}

	/**
	 * Decrementa en 1 la cantidad de un producto desde la vista del carrito.
	 */
	public function down()
	{
		if (isset($_GET['index'])) {
			$index = $_GET['index'];
			// Resta una unidad al producto en el índice indicado
			$_SESSION['carrito'][$index]['unidades']--;

			// Si las unidades llegan a 0, elimina el producto por completo del carrito
			if ($_SESSION['carrito'][$index]['unidades'] == 0) {
				unset($_SESSION['carrito'][$index]);
			}
		}
		header("Location:" . base_url . "carrito/index");
	}

	/**
	 * Vacía el carrito por completo (borra toda la sesión del carrito).
	 */
	public function delete_all()
	{
		// Elimina la variable de sesión completa del carrito
		unset($_SESSION['carrito']);
		// Redirige al carrito de compras, el cual ahora se mostrará vacío
		header("Location:" . base_url . "carrito/index");
	}
}