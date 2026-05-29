<?php
spl_autoload_register(function ($classname) {
    $paths = [
        'controllers/',
        'models/',
        'helpers/'
    ];

    foreach ($paths as $path) {
        $file = __DIR__ . '/' . $path . $classname . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
