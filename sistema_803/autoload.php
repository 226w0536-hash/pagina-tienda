<?php

function controllers_autoload($classname) {
    // 1. Definir los directorios donde guardas tus clases
    $directories = [
        'controllers/',
        'models/',
		'config/',
		'helpers',
		'views'
    ];

    // 2. Recorrer los directorios para encontrar el archivo
    foreach ($directories as $dir) {
        $file = $dir . $classname . '.php';
        
        // 3. Validar si el archivo existe antes de intentar incluirlo
        if (file_exists($file)) {
            include_once $file;
            return; // Salir apenas encuentre el archivo
        }
    }
}

// 4. Registrar la función en el autoloader de PHP
spl_autoload_register('controllers_autoload');
