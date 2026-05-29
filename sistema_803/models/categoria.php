<?php

// Definición de la clase Categoria.
// En la arquitectura MVC, esto representa un "Modelo", encargado exclusivamente de gestionar los datos de la tabla 'categorias'.
class Categoria
{
	// Propiedades privadas (encapsulamiento): corresponden a las columnas de la tabla en la base de datos
	private $id;
	private $nombre;
	// Propiedad para almacenar la instancia de la conexión a la base de datos
	private $db;

	/**
	 * Constructor de la clase.
	 * Se ejecuta automáticamente cada vez que se hace un 'new Categoria()'.
	 * Su única tarea aquí es inicializar y heredar la conexión activa a la base de datos.
	 */
	public function __construct()
	{
		// Utiliza el método estático de la clase Database para conectar con MySQL
		$this->db = Database::connect();
	}

	// ==========================================
	//            GETTERS Y SETTERS
	// ==========================================

	// Retorna el valor del ID de la categoría
	function getId()
	{
		return $this->id;
	}

	// Retorna el nombre de la categoría
	function getNombre()
	{
		return $this->nombre;
	}

	// Asigna un valor al ID de la categoría
	function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * Asigna el nombre de la categoría aplicando un filtro de seguridad.
	 * 'real_escape_string' escapa caracteres especiales (como comillas) para evitar 
	 * ataques de Inyección SQL (SQL Injection) antes de insertar el texto en la base de datos.
	 */
	function setNombre($nombre)
	{
		$this->nombre = $this->db->real_escape_string($nombre);
	}

	// ==========================================
    //         MÉTODOS DE CONSULTA (CRUD)
    // ==========================================

	/**
	 * Obtiene todas las categorías registradas en la base de datos.
	 * Las ordena desde la más nueva hasta la más vieja (ID descendente).
	 */
	public function getAll()
	{
		$categorias = $this->db->query("SELECT * FROM categorias ORDER BY id DESC;");
		// Devuelve el objeto de resultado de mysqli (un conjunto de filas) para ser iterado en la vista	
		return $categorias;
	}

	/**
	 * Obtiene los datos de una sola categoría basándose en su ID actual.
	 */
	public function getOne()
	{
		// Lanza la consulta filtrando por el ID obtenido mediante el getter
		$categoria = $this->db->query("SELECT * FROM categorias WHERE id={$this->getId()}");
		// 'fetch_object()' transforma la fila de la base de datos en un objeto limpio de PHP 
		// para acceder a sus propiedades directamente (ej: $categoria->nombre)
		return $categoria->fetch_object();
	}

	/**
	 * Inserta una nueva categoría en la base de datos.
	 */
	public function save()
	{
		// Define la consulta SQL. El ID se pasa como NULL porque es autoincremental en MySQL
		$sql = "INSERT INTO categorias VALUES(NULL, '{$this->getNombre()}');";
		$save = $this->db->query($sql);	// Ejecuta la inserción

		$result = false;
		// Si la consulta fue exitosa sintácticamente, cambia el resultado a true
		if ($save) {
			$result = true;
		}
		// Retorna un booleano (true/false) indicando el éxito de la operación al controlador
		return $result;
	}
}
