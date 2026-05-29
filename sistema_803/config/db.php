<?php

class Database {
    public static function connect() {
        /**
         * Obtenemos las variables de entorno inyectadas por Railway.
         * Si no existen (por ejemplo en un entorno local), 
         * usamos valores por defecto.
         */
        $host = getenv('MYSQLHOST') ?: 'localhost';
        $user = getenv('MYSQLUSER') ?: 'root';
        $pass = getenv('MYSQLPASSWORD') ?: '';
        $db   = getenv('MYSQLDATABASE') ?: 'tienda_master';
        $port = getenv('MYSQLPORT') ?: 3306;

        /**
         * Intentamos establecer la conexión.
         * Desactivamos temporalmente los errores de mysqli para manejar 
         * el error de forma personalizada si falla la conexión.
         */
        mysqli_report(MYSQLI_REPORT_OFF);
        
        $db = new mysqli($host, $user, $pass, $db, $port);

        // Si hay error en la conexión, mostramos un mensaje útil
        if ($db->connect_error) {
            die("Error de conexión a la base de datos: " . $db->connect_error . " (Host: $host)");
        }
        
        // Configuramos la codificación de caracteres
        $db->query("SET NAMES 'utf8'");
        
        return $db;
    }
}
