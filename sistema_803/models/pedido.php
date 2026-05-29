<?php


// Definición de la clase Pedido (Modelo)
// Se encarga exclusivamente de la lógica de datos y consultas de las tablas 'pedidos' y 'lineas_pedidos'.
class Pedido
{
	// Propiedades privadas correspondientes a los campos de la tabla 'pedidos'
	private $id;
	private $usuario_id;
	private $provincia;
	private $localidad;
	private $direccion;
	private $coste;
	private $estado;
	private $fecha;
	private $hora;

	// Propiedad para la instancia de la conexión a la base de datos
	private $db;

	/**
	 * Constructor de la clase.
	 * Conecta automáticamente con el gestor MySQL al instanciar el objeto.
	 */
	public function __construct()
	{
		$this->db = Database::connect();
	}

	// ==========================================
	//            GETTERS Y SETTERS
	// ==========================================

	function getId()
	{
		return $this->id;
	}

	function getUsuario_id()
	{
		return $this->usuario_id;
	}

	function getProvincia()
	{
		return $this->provincia;
	}

	function getLocalidad()
	{
		return $this->localidad;
	}

	function getDireccion()
	{
		return $this->direccion;
	}

	function getCoste()
	{
		return $this->coste;
	}

	function getEstado()
	{
		return $this->estado;
	}

	function getFecha()
	{
		return $this->fecha;
	}

	function getHora()
	{
		return $this->hora;
	}

	function setId($id)
	{
		$this->id = $id;
	}

	function setUsuario_id($usuario_id)
	{
		$this->usuario_id = $usuario_id;
	}

	// Sanitiza la provincia contra Inyección SQL antes de asignarla
	function setProvincia($provincia)
	{
		$this->provincia = $this->db->real_escape_string($provincia);
	}

	// Sanitiza la localidad contra Inyección SQL antes de asignarla
	function setLocalidad($localidad)
	{
		$this->localidad = $this->db->real_escape_string($localidad);
	}

	// Sanitiza la dirección física contra Inyección SQL antes de asignarla
	function setDireccion($direccion)
	{
		$this->direccion = $this->db->real_escape_string($direccion);
	}

	function setCoste($coste)
	{
		$this->coste = $coste;
	}

	function setEstado($estado)
	{
		$this->estado = $estado;
	}

	function setFecha($fecha)
	{
		$this->fecha = $fecha;
	}

	function setHora($hora)
	{
		$this->hora = $hora;
	}

	 // ==========================================
    //       MÉTODOS DE CONSULTA Y OPERACIONES
    // ==========================================

	/**
	 * Obtiene absolutamente todos los pedidos de la tienda.
	 * Útil para el panel de gestión del administrador.
	 */
	public function getAll()
	{
		$productos = $this->db->query("SELECT * FROM pedidos ORDER BY id DESC");
		return $productos;
	}

	/**
	 * Obtiene un único pedido filtrado por su ID.
	 */
	public function getOne()
	{
		$producto = $this->db->query("SELECT * FROM pedidos WHERE id = {$this->getId()}");
		return $producto->fetch_object();
	}

	/**
	 * Obtiene el último pedido realizado por el usuario actual.
	 * Se usa comúnmente para mostrar el resumen justo después de terminar una compra.
	 */
	public function getOneByUser()
	{
		$sql = "SELECT p.id, p.coste FROM pedidos p "
			//. "INNER JOIN lineas_pedidos lp ON lp.pedido_id = p.id "
			. "WHERE p.usuario_id = {$this->getUsuario_id()} ORDER BY id DESC LIMIT 1";

		$pedido = $this->db->query($sql);

		return $pedido->fetch_object();
	}

	/**
	 * Obtiene el historial completo de pedidos pertenecientes a un usuario específico.
	 */
	public function getAllByUser()
	{
		$sql = "SELECT p.* FROM pedidos p "
			. "WHERE p.usuario_id = {$this->getUsuario_id()} ORDER BY id DESC";

		$pedido = $this->db->query($sql);

		return $pedido;
	}

	/**
	 * Obtiene la lista de artículos físicos y cantidades asociados a un pedido.
	 * Realiza un INNER JOIN para cruzar la tabla intermedia con la de productos.
	 */
	public function getProductosByPedido($id)
	{
		//		$sql = "SELECT * FROM productos WHERE id IN "
		//				. "(SELECT producto_id FROM lineas_pedidos WHERE pedido_id={$id})";
		// La consulta asocia los productos con sus respectivas unidades guardadas en 'lineas_pedidos'
		$sql = "SELECT pr.*, lp.unidades FROM productos pr "
			. "INNER JOIN lineas_pedidos lp ON pr.id = lp.producto_id "
			. "WHERE lp.pedido_id={$id}";

		$productos = $this->db->query($sql);

		return $productos;
	}

	/**
	 * Inserta la cabecera del pedido en la tabla 'pedidos'.
	 * Usa las funciones de MySQL CURDATE() y CURTIME() para registrar la fecha y hora del servidor automáticamente.
	 */
	public function save()
	{
		$sql = "INSERT INTO pedidos VALUES(NULL, {$this->getUsuario_id()}, '{$this->getProvincia()}', '{$this->getLocalidad()}', '{$this->getDireccion()}', {$this->getCoste()}, 'confirm', CURDATE(), CURTIME());";
		$save = $this->db->query($sql);

		$result = false;
		if ($save) {
			$result = true;
		}
		return $result;
	}

	/**
	 * Registra de manera desglosada los artículos que se compraron.
	 * Recupera el ID del pedido recién creado y mapea el carrito de la sesión en la tabla intermedia.
	 */
	public function save_linea()
	{
		// Recupera el último ID autoincremental generado en la conexión actual (el ID del pedido que se acaba de guardar)
		$sql = "SELECT LAST_INSERT_ID() as 'pedido';";
		$query = $this->db->query($sql);
		$pedido_id = $query->fetch_object()->pedido;

		// Recorre los elementos del carrito de compras temporal almacenado en la sesión
		foreach ($_SESSION['carrito'] as $elemento) {
			$producto = $elemento['producto'];

			$insert = "INSERT INTO lineas_pedidos VALUES(NULL, {$pedido_id}, {$producto->id}, {$elemento['unidades']})";
			$save = $this->db->query($insert);

			//			var_dump($producto);
			//			var_dump($insert);
			//			echo $this->db->error;
			//			die();
		}

		$result = false;
		if ($save) {
			$result = true;
		}
		return $result;
	}

	/**
	 * Modifica únicamente el estado del pedido (ej. 'En preparación', 'Enviado').
	 * Orientado a las acciones del panel del administrador.
	 */
	public function edit()
	{
		$sql = "UPDATE pedidos SET estado='{$this->getEstado()}' ";
		$sql .= " WHERE id={$this->getId()};";

		$save = $this->db->query($sql);

		$result = false;
		if ($save) {
			$result = true;
		}
		return $result;
	}
}
