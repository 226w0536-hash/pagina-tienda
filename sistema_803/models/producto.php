<?php

// Definición de la clase Producto.
// En el patrón MVC, este archivo es el "Modelo" encargado de interactuar directamente con la tabla 'productos' en la BD.
class Producto
{
	// Propiedades privadas que mapean directamente las columnas de la tabla de productos
	private $id;
	private $categoria_id;	// Llave foránea (FK) que vincula el producto con una categoría
	private $nombre;
	private $descripcion;
	private $precio;
	private $stock;
	private $oferta;
	private $fecha;
	private $imagen;	// Guarda el nombre de la ruta o archivo de la imagen cargada

	// Propiedad interna para el manejo de la conexión a la base de datos
	private $db;

	/**
	 * Constructor de la clase.
	 * Invoca la conexión activa a la base de datos mediante el método estático de la clase Database.
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

	function getCategoria_id()
	{
		return $this->categoria_id;
	}

	function getNombre()
	{
		return $this->nombre;
	}

	function getDescripcion()
	{
		return $this->descripcion;
	}

	function getPrecio()
	{
		return $this->precio;
	}

	function getStock()
	{
		return $this->stock;
	}

	function getOferta()
	{
		return $this->oferta;
	}

	function getFecha()
	{
		return $this->fecha;
	}

	function getImagen()
	{
		return $this->imagen;
	}

	function setId($id)
	{
		$this->id = $id;
	}

	function setCategoria_id($categoria_id)
	{
		$this->categoria_id = $categoria_id;
	}

	// Filtra el nombre para evitar caracteres maliciosos (Inyección SQL)
	function setNombre($nombre)
	{
		$this->nombre = $this->db->real_escape_string($nombre);
	}

	// Filtra la descripción para escapar comillas o caracteres especiales
	function setDescripcion($descripcion)
	{
		$this->descripcion = $this->db->real_escape_string($descripcion);
	}

	// Sanitiza el precio recibido antes de asignarlo
	function setPrecio($precio)
	{
		$this->precio = $this->db->real_escape_string($precio);
	}

	// Sanitiza el stock recibido antes de asignarlo
	function setStock($stock)
	{
		$this->stock = $this->db->real_escape_string($stock);
	}

	// Sanitiza el campo oferta antes de asignarlo
	function setOferta($oferta)
	{
		$this->oferta = $this->db->real_escape_string($oferta);
	}

	function setFecha($fecha)
	{
		$this->fecha = $fecha;
	}

	function setImagen($imagen)
	{
		$this->imagen = $imagen;
	}

	   // ==========================================
    //       MÉTODOS DE CONSULTA Y OPERACIONES (CRUD)
    // ==========================================

	/**
	 * Obtiene todos los productos de la tienda organizados desde el último agregado.
	 * Se usa comúnmente en el listado del panel de administración.
	 */
	public function getAll()
	{
		$productos = $this->db->query("SELECT * FROM productos ORDER BY id DESC");
		return $productos;
	}

	/**
	 * Obtiene los productos que pertenecen a una categoría específica usando un INNER JOIN.
	 * Trae también el nombre de la categoría mapeado bajo el alias 'catnombre'.
	 */
	public function getAllCategory()
	{
		$sql = "SELECT p.*, c.nombre AS 'catnombre' FROM productos p "
			. "INNER JOIN categorias c ON c.id = p.categoria_id "
			. "WHERE p.categoria_id = {$this->getCategoria_id()} "
			. "ORDER BY id DESC";
		$productos = $this->db->query($sql);
		return $productos;
	}

	/**
	 * Recupera un conjunto de productos de forma aleatoria limitando la cantidad.
	 * Ideal para secciones de la página de inicio como "Productos destacados" o "Sugerencias".
	 */
	public function getRandom($limit)
	{
		$productos = $this->db->query("SELECT * FROM productos ORDER BY RAND() LIMIT $limit");
		return $productos;
	}

	/**
	 * Obtiene la información detallada de un solo producto basándose en su ID.
	 */
	public function getOne()
	{
		$producto = $this->db->query("SELECT * FROM productos WHERE id = {$this->getId()}");
		return $producto->fetch_object();
	}

	/**
	 * Guarda un producto nuevo en la base de datos de la tienda.
	 * Registra la fecha de creación de forma automática mediante la función CURDATE() de MySQL.
	 */
	public function save()
	{
		$sql = "INSERT INTO productos VALUES(NULL, {$this->getCategoria_id()}, '{$this->getNombre()}', '{$this->getDescripcion()}', {$this->getPrecio()}, {$this->getStock()}, null, CURDATE(), '{$this->getImagen()}');";
		$save = $this->db->query($sql);

		$result = false;
		if ($save) {
			$result = true;
		}
		return $result;
	}

	/**
	 * Actualiza las propiedades de un producto existente.
	 * Cuenta con lógica condicional para modificar la imagen en la BD únicamente si se subió un archivo nuevo.
	 */
	public function edit()
	{
		$sql = "UPDATE productos SET nombre='{$this->getNombre()}', descripcion='{$this->getDescripcion()}', precio={$this->getPrecio()}, stock={$this->getStock()}, categoria_id={$this->getCategoria_id()}  ";

		// Si el getter de la imagen no es nulo, significa que el usuario actualizó el archivo de imagen
		if ($this->getImagen() != null) {
			$sql .= ", imagen='{$this->getImagen()}'";
		}

		$sql .= " WHERE id={$this->id};";


		$save = $this->db->query($sql);

		$result = false;
		if ($save) {
			$result = true;
		}
		return $result;
	}

	/**
	 * Elimina permanentemente un producto de la base de datos utilizando su ID.
	 */
	public function delete()
	{
		$sql = "DELETE FROM productos WHERE id={$this->id}";
		$delete = $this->db->query($sql);

		$result = false;
		if ($delete) {
			$result = true;
		}
		return $result;
	}
}
