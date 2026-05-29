<?php
// Definición de la clase Database (Base de datos)
class Database
{
	/**
	 * Método estático para establecer la conexión con la base de datos.
	 * Al ser 'public static', se puede invocar directamente sin necesidad 
	 * de crear un objeto de la clase (ej: Database::connect()).
	 */
	public static function connect()
	{
		// Crea una nueva instancia de la clase 'mysqli' para conectar con MySQL.
		// Parámetros: ('servidor', 'usuario', 'contraseña', 'nombre_base_datos')
		$db = new mysqli('localhost', 'root', '', 'tienda_master');
		// Ejecuta una consulta para asegurar que los datos se transmitan en UTF-8.
		// Esto evita problemas con eñes, acentos o caracteres especiales en la web.
		$db->query("SET NAMES 'utf8'");
		// Devuelve el objeto de la conexión para que pueda ser utilizado en otras partes del proyecto.
		return $db;
	}
}
