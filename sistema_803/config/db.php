<?php
class Database
{
    public static function connect()
    {
        // Obtenemos los valores de las variables de entorno de Railway
        // Si no existen (estás en local), usará los valores por defecto que tenías antes
        $host = getenv('MYSQLHOST') ?: 'localhost';
        $user = getenv('MYSQLUSER') ?: 'root';
        $pass = getenv('MYSQLPASSWORD') ?: '';
        $db   = getenv('MYSQLDATABASE') ?: 'tienda_master';
        $port = getenv('MYSQLPORT') ?: 3306;

        // Creamos la conexión pasando el puerto también
        $db = new mysqli($host, $user, $pass, $db, $port);
        
        // Verificamos si hubo error de conexión
        if ($db->connect_error) {
            die("Error de conexión: " . $db->connect_error);
        }

        $db->query("SET NAMES 'utf8'");
        return $db;
    }
}
