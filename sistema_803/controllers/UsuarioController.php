<?php
// Importa el modelo Usuario para poder realizar consultas e inserciones en la tabla de usuarios
require_once 'models/usuario.php';

// Definición del controlador que gestiona el acceso, registro y salida de los usuarios
class usuarioController
{

    /**
     * Método index por defecto. En este caso solo muestra un texto de prueba.
     */
    public function index()
    {
        echo "Controlador Usuarios, Acción index";
    }

    /**
     * Muestra la vista con el formulario de registro para nuevos clientes.
     */
    public function registro()
    {
        // Carga el archivo HTML/PHP del formulario de registro
        require_once 'views/usuario/registro.php';
    }

    /**
     * Recibe los datos del formulario de registro (POST) y los almacena en la base de datos.
     */
    public function save()
    {
        // Verifica si la petición se realizó a través del método POST
        if (isset($_POST)) {

            // Recoge los parámetros del formulario; limpiamos espacios y si no existen asignamos false
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : false;
            $apellidos = isset($_POST['apellidos']) ? trim($_POST['apellidos']) : false;
            $email = isset($_POST['email']) ? trim($_POST['email']) : false;
            $password = isset($_POST['password']) ? $_POST['password'] : false;
            $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : false;

            // Array para capturar errores de validación
            $errors = array();

            // 1. Validaciones estrictas de los datos (Backend)
            if (empty($nombre) || !preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚ ]*$/", $nombre)) {
                $errors['nombre'] = "El nombre no es válido";
            }
            if (empty($apellidos) || !preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚ ]*$/", $apellidos)) {
                $errors['apellidos'] = "Los apellidos no son válidos";
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "El email no es válido";
            }
            if (empty($password) || strlen($password) < 6) {
                $errors['password'] = "La contraseña debe tener al menos 6 caracteres";
            }
            if ($password !== $confirm_password) {
                $errors['confirm_password'] = "Las contraseñas no coinciden";
            }

            // Si no hay errores, procedemos a guardar
            if (count($errors) == 0) {
                // Instancia el modelo e introduce los valores validados usando los Setters
                $usuario = new Usuario();
                $usuario->setNombre($nombre);
                $usuario->setApellidos($apellidos);
                $usuario->setEmail($email);
                $usuario->setPassword($password);

                // Ejecuta el query de inserción (INSERT INTO) en la base de datos
                $save = $usuario->save();

                // Genera una variable de sesión informativa para mostrar alertas de éxito o fracaso
                if ($save) {
                    $_SESSION['register'] = "complete";
                } else {
                    $_SESSION['register'] = "failed";
                }
            } else {
                // Si hay errores, guardamos el array de errores en sesión para la vista
                $_SESSION['errors'] = $errors;
                $_SESSION['register'] = "failed";
            }
        } else {
            $_SESSION['register'] = "failed";
        }
        // Redirige al usuario de vuelta al formulario de registro
        header("Location:" . base_url . 'usuario/registro');
    }

    /**
     * Procesa las credenciales de inicio de sesión (POST) y autentica al usuario.
     */
    public function login()
    {
        // Verifica si llegaron datos desde el formulario de inicio de sesión
        if (isset($_POST)) {
            // Identificar al usuario y realizar consulta a la base de datos
            $usuario = new Usuario();
            $usuario->setEmail($_POST['email']);
            $usuario->setPassword($_POST['password']);

            // Llama al método login del modelo
            $identity = $usuario->login();

            // Si las credenciales coinciden y el servidor devuelve los datos como un objeto válido...
            if ($identity && is_object($identity)) {
                // Guarda el objeto completo con los datos del usuario en la sesión global
                $_SESSION['identity'] = $identity;

                // Si es admin, crea una sesión especial de privilegios
                if ($identity->rol == 'admin') {
                    $_SESSION['admin'] = true;
                }
            } else {
                // Si los datos son incorrectos, guarda un mensaje de error
                $_SESSION['error_login'] = 'Identificación fallida !!';
            }
        }
        // Redirige a la página principal
        header("Location:" . base_url);
    }

    /**
     * Cierra la sesión activa borrando los datos de autenticación del servidor.
     */
    public function logout()
    {
        // Elimina registros de identidad y admin de la sesión
        if (isset($_SESSION['identity'])) {
            unset($_SESSION['identity']);
        }
        if (isset($_SESSION['admin'])) {
            unset($_SESSION['admin']);
        }

        // Redirige al usuario a la página de inicio
        header("Location:" . base_url);
    }
}