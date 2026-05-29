<?php
// Importa el modelo Pedido para interactuar con las tablas 'pedidos' y 'lineas_pedidos' en la BD
require_once 'models/pedido.php';

// Definición del controlador que gestiona todo el ciclo de vida de las compras
class pedidoController
{

	/**
	 * Muestra la vista con el formulario para ingresar los datos de envío (Dirección, Localidad, etc.)
	 */
	public function hacer()
	{
		// Carga la vista para que el usuario proceda a llenar los datos de su compra
		require_once 'views/pedido/hacer.php';
	}

	/**
	 * Procesa el formulario de envío, calcula el total y guarda el pedido en la base de datos.
	 */
	public function add()
	{

		// Verifica si el usuario ha iniciado sesión (existe identidad en la sesión)
		if (isset($_SESSION['identity'])) {
			$usuario_id = $_SESSION['identity']->id;	// Recupera el ID del usuario logueado

			// Recoge los datos del formulario POST sanitizándolos de forma básica (si no existen, asigna false)
			$provincia = isset($_POST['provincia']) ? $_POST['provincia'] : false;
			$localidad = isset($_POST['localidad']) ? $_POST['localidad'] : false;
			$direccion = isset($_POST['direccion']) ? $_POST['direccion'] : false;

			// Llama a un método auxiliar de utilidades para obtener el costo total acumulado del carrito
			$stats = Utils::statsCarrito();
			$coste = $stats['total'];

			if ($provincia && $localidad && $direccion) {

				// --- 1. Guardar la cabecera del pedido en la BD ---
				$pedido = new Pedido();
				$pedido->setUsuario_id($usuario_id);
				$pedido->setProvincia($provincia);
				$pedido->setLocalidad($localidad);
				$pedido->setDireccion($direccion);
				$pedido->setCoste($coste);

				$save = $pedido->save();	// Inserta el registro principal en la tabla 'pedidos'

				// --- 2. Guardar los productos individuales del pedido ---
				// Toma los productos del carrito actual en la sesión y los desglosa en la tabla de detalles/líneas
				$save_linea = $pedido->save_linea();

				// Comprueba que tanto el pedido general como sus líneas se hayan guardado con éxito
				if ($save && $save_linea) {
					$_SESSION['pedido'] = "complete";	// Define estado de éxito para la vista
				} else {
					$_SESSION['pedido'] = "failed";		// Error en la inserción de datos
				}
			} else {
				$_SESSION['pedido'] = "failed";		// Error por campos del formulario incompletos
			}

			// Redirige a la pantalla de confirmación/resumen del pedido
			header("Location:" . base_url . 'pedido/confirmado');
		} else {

			// Si el usuario intenta comprar sin estar logueado, lo expulsa a la página de inicio
			header("Location:" . base_url);
		}
	}

	/**
	 * Muestra la pantalla de agradecimiento y el último pedido procesado para el usuario actual.
	 */
	public function confirmado()
	{
		if (isset($_SESSION['identity'])) {
			$identity = $_SESSION['identity'];

			// Consigue el pedido más reciente que acaba de registrar este usuario
			$pedido = new Pedido();
			$pedido->setUsuario_id($identity->id);
			$pedido = $pedido->getOneByUser();

			// Recupera el desglose de productos asociados a ese pedido específico para listarlos
			$pedido_productos = new Pedido();
			$productos = $pedido_productos->getProductosByPedido($pedido->id);
		}

		// Carga la vista de éxito de la compra
		require_once 'views/pedido/confirmado.php';
	}

	/**
	 * Muestra el historial completo de compras del usuario autenticado.
	 */
	public function mis_pedidos()
	{
		// Restringe el acceso: verifica que el usuario haya iniciado sesión obligatoriamente
		Utils::isIdentity();
		$usuario_id = $_SESSION['identity']->id;

		$pedido = new Pedido();
		$pedido->setUsuario_id($usuario_id);

		// Extrae de la base de datos todos los pedidos históricos del cliente
		$pedidos = $pedido->getAllByUser();

		// Carga la vista del historial de compras del cliente
		require_once 'views/pedido/mis_pedidos.php';
	}

	/**
	 * Muestra el detalle minucioso de un único pedido (Dirección, costo total y productos incluidos).
	 */
	public function detalle()
	{
		Utils::isIdentity();

		// Verifica que se pase el ID del pedido por la URL (método GET)
		if (isset($_GET['id'])) {
			$id = $_GET['id'];

			// Obtiene los datos generales del pedido (quién compró, a dónde se envía, estado)
			$pedido = new Pedido();
			$pedido->setId($id);
			$pedido = $pedido->getOne();

			// Obtiene la lista completa de artículos y cantidades que integran dicho pedido
			$pedido_productos = new Pedido();
			$productos = $pedido_productos->getProductosByPedido($id);

			// Carga la plantilla de visualización del detalle
			require_once 'views/pedido/detalle.php';
		} else {
			// Si no se especifica un ID válido, regresa al listado general
			header('Location:' . base_url . 'pedido/mis_pedidos');
		}
	}

	/**
	 * Permite al administrador ver absolutamente todos los pedidos de la tienda para gestionarlos.
	 * Vista exclusiva para administradores.
	 */
	public function gestion()
	{
		// Filtro de seguridad estricto
		Utils::isAdmin();
		$gestion = true;	// Bandera usada en la vista para habilitar opciones de edición (ej. cambiar estado)

		$pedido = new Pedido();
		// Obtiene el universo completo de pedidos registrados en el sistema
		$pedidos = $pedido->getAll();

		// Reutiliza la vista 'mis_pedidos.php' pero con privilegios de administración activos
		require_once 'views/pedido/mis_pedidos.php';
	}

	/**
	 * Modifica el estado del pedido (ej: De 'Pendiente' a 'Preparado' o 'Enviado').
	 * Acción exclusiva para administradores.
	 */
	public function estado()
	{
		Utils::isAdmin();

		// Valida que los datos provengan de un formulario de actualización (ID del pedido y nuevo estado)
		if (isset($_POST['pedido_id']) && isset($_POST['estado'])) {
			// Recoger datos form
			$id = $_POST['pedido_id'];
			$estado = $_POST['estado'];

			// Actualiza el campo 'estado' en la fila correspondiente de la base de datos
			$pedido = new Pedido();
			$pedido->setId($id);
			$pedido->setEstado($estado);
			$pedido->edit();	// Lanza el query UPDATE

			// Redirige de vuelta a la ficha del pedido para visualizar el cambio aplicado
			header("Location:" . base_url . 'pedido/detalle&id=' . $id);
		} else {
			header("Location:" . base_url);
		}
	}
}
