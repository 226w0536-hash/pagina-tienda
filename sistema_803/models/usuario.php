<?php
// Definición de la clase Usuario.
// En el patrón MVC, este archivo es el "Modelo" encargado de interactuar directamente con la tabla 'usuarios' en la BD.
class Usuario
{
	// Propiedades privadas (encapsulamiento) que representan las columnas de la tabla 'usuarios'
	private $id;
	private $nombre;
	private $apellidos;
	private $email;
	private $password;
	private $rol;	// Define los privilegios en la plataforma (ej. 'user', 'admin')
	private $imagen;	// Ruta o nombre del archivo de avatar del usuario
	// Propiedad interna para guardar la conexión activa a la base de datos
	private $db;

	/**
	 * Constructor de la clase.
	 * Invoca y hereda la conexión a MySQL usando el método estático de la clase Database.
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

	function getNombre()
	{
		return $this->nombre;
	}

	function getApellidos()
	{
		return $this->apellidos;
	}

	function getEmail()
	{
		return $this->email;
	}

	/**
	 * Método Getter especial para la contraseña.
	 * En lugar de devolver el texto en plano, aplica filtros de escape contra Inyección SQL
	 * y genera un Hash criptográfico usando el algoritmo seguro BCRYPT.
	 * El 'cost => 4' define el número de rondas de iteración para la encriptación.
	 */
	function getPassword()
	{
		return password_hash($this->db->real_escape_string($this->password), PASSWORD_BCRYPT, ['cost' => 4]);
	}

	function getRol()
	{
		return $this->rol;
	}

	function getImagen()
	{
		return $this->imagen;
	}

	function setId($id)
	{
		$this->id = $id;
	}

	// Escapa caracteres especiales en el nombre para proteger el query
	function setNombre($nombre)
	{
		$this->nombre = $this->db->real_escape_string($nombre);
	}
	// Escapa caracteres especiales en los apellidos para proteger el query
	function setApellidos($apellidos)
	{
		$this->apellidos = $this->db->real_escape_string($apellidos);
	}

	// Escapa caracteres especiales en el correo electrónico
	function setEmail($email)
	{
		$this->email = $this->db->real_escape_string($email);
	}

	/**
	 * Asigna la contraseña tal cual llega del formulario (en texto plano).
	 * Nota: Se mantiene en plano temporalmente en la propiedad de la clase y se encripta 
	 * únicamente al invocar el método getPassword() durante el flujo de inserción (save).
	 */
	function setPassword($password)
	{
		$this->password = $password;
	}

	function setRol($rol)
	{
		$this->rol = $rol;
	}

	function setImagen($imagen)
	{
		$this->imagen = $imagen;
	}

	// ==========================================
    //         MÉTODOS DE AUTENTICACIÓN Y CRUD
    // ==========================================

	/**
	 * Inserta un nuevo usuario en la base de datos (Proceso de Registro).
	 */
	public function save()
	{
		// Prepara el query. El ID va como NULL por ser autoincremental.
		// Por defecto, a todo registro nuevo se le asigna el string 'user' en la columna de rol.
		$sql = "INSERT INTO usuarios VALUES(NULL, '{$this->getNombre()}', '{$this->getApellidos()}', '{$this->getEmail()}', '{$this->getPassword()}', 'user', null);";
		$save = $this->db->query($sql);

		$result = false;
		if ($save) {
			$result = true; 	// Retorna true si la inserción fue exitosa
		}
		return $result;
	}

	/**
	 * Valida las credenciales de un usuario para permitirle el acceso (Proceso de Login).
	 */
	public function login()
	{
		$result = false;
		$email = $this->email;
		$password = $this->password;	// Contraseña en texto plano introducida en el formulario de login

		// Paso 1: Comprobar si existe un registro con el correo electrónico proporcionado
		$sql = "SELECT * FROM usuarios WHERE email = '$email'";
		$login = $this->db->query($sql);

		// Si el query tiene éxito y devuelve exactamente 1 fila coincidente...
		if ($login && $login->num_rows == 1) {
			// Transforma la fila de la BD en un objeto limpio de PHP
			$usuario = $login->fetch_object();

			// Paso 2: Verificar si la contraseña en texto plano coincide con el hash almacenado en la BD.
			// 'password_verify' descifra internamente el patrón y compara de manera segura.
			$verify = password_verify($password, $usuario->password);

			// Si la verificación de la contraseña es correcta...
			if ($verify) {
				// Retorna el objeto completo con los datos del usuario (id, nombre, email, rol, etc.)
				$result = $usuario;
			}
		}

		// Si el usuario no existe o la contraseña no coincide, retornará false
		return $result;
	}
}
