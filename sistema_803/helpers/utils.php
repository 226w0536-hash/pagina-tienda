<?php
// Definición de la clase Utils (Utilidades)
// Al contener solo métodos estáticos, funciona como una caja de herramientas global para todo el proyecto.
class Utils
{

	/**
	 * Elimina de forma segura una variable de sesión específica.
	 * Sirve para limpiar mensajes de error, alertas de formularios, etc., tras ser mostrados.
	 */
	public static function deleteSession($name)
	{
		// Verifica si la sesión con ese nombre en específico existe
		if (isset($_SESSION[$name])) {
			$_SESSION[$name] = null;	// Coloca su valor en nulo
			unset($_SESSION[$name]);	// Destruye por completo la variable de la memoria
		}

		// Retorna el nombre de la sesión procesada
		return $name;
	}

	/**
	 * Filtro de seguridad: Comprueba si el usuario actual tiene rol de Administrador.
	 * Si no lo es, detiene la carga de la página y lo expulsa.
	 */
	public static function isAdmin()
	{
		// Si NO existe la variable de sesión que valida al administrador...
		if (!isset($_SESSION['admin'])) {
			// Redirige inmediatamente a la página de inicio
			header("Location:" . base_url);
		} else {
			// Si existe, permite continuar devolviendo verdadero
			return true;
		}
	}

	/**
	 * Filtro de seguridad: Comprueba si el usuario ha iniciado sesión (sin importar su rol).
	 * Evita que un invitado/visitante acceda a zonas privadas como el historial de pedidos.
	 */
	public static function isIdentity()
	{
		// Si NO existe la variable de sesión 'identity' (el objeto del usuario logueado)...
		if (!isset($_SESSION['identity'])) {
			// Redirige de inmediato a la raíz de la web
			header("Location:" . base_url);
		} else {
			// Si inició sesión correctamente, devuelve verdadero
			return true;
		}
	}

	/**
	 * Recupera todas las categorías registradas para poder pintarlas en los menús laterales o de navegación.
	 */
	public static function showCategorias()
	{
		// Importa el modelo Categoria para consultar la base de datos
		require_once 'models/categoria.php';
		$categoria = new Categoria();
		// Llama al método encargado de lanzar el query SELECT de todas las categorías
		$categorias = $categoria->getAll();
		// Devuelve la lista de categorías para que la vista pueda iterarla (con un foreach)
		return $categorias;
	}

	/**
	 * Calcula en tiempo real las estadísticas del carrito: número total de productos y el costo acumulado.
	 */
	public static function statsCarrito()
	{
		// Inicializa un arreglo con valores en cero por defecto
		$stats = array(
			'count' => 0,
			'total' => 0
		);

		// Si el arreglo del carrito existe en la sesión y contiene productos...
		if (isset($_SESSION['carrito'])) {
			// Cuenta cuántos tipos de productos diferentes hay en el carrito
			$stats['count'] = count($_SESSION['carrito']);

			// Recorre cada producto guardado en el carrito de compras
			foreach ($_SESSION['carrito'] as $producto) {
				// Multiplica el precio de cada artículo por sus unidades y lo va sumando al acumulador total
				$stats['total'] += $producto['precio'] * $producto['unidades'];
			}
		}

		// Devuelve el arreglo con los cálculos finales listos para pintarse en la barra superior o lateral
		return $stats;
	}

	/**
	 * Traduce los nombres técnicos de los estados de un pedido almacenados en la BD
	 * a etiquetas legibles y estéticas para el usuario final.
	 */
	public static function showStatus($status)
	{
		$value = 'Pendiente';	// Estado por defecto

		// Estructura condicional que evalúa la clave que proviene de la base de datos
		if ($status == 'confirm') {
			$value = 'Pendiente';
		} elseif ($status == 'preparation') {
			$value = 'En preparación';
		} elseif ($status == 'ready') {
			$value = 'Preparado para enviar';
		} elseif ($status = 'sended') {
			// OJO: Aquí hay un detalle técnico en tu código original, se usó '=' (asignación) en lugar de '==' (comparación)
			$value = 'Enviado';
		}

		// Retorna el texto traducido listo para la interfaz
		return $value;
	}
}
