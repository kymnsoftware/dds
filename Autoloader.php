<?php
// core/Autoloader.php

class Autoloader {
    public static function register() {
        spl_autoload_register(function ($class) {
            // Sınıf adından dosya yolunu tahmin et
            // Namespace kullanmıyorsak, sınıf adını direkt dosya adına çevir.
            // Eğer namespace kullanılıyorsa, daha karmaşık bir mantık gerekir (PSR-4 gibi).

            // Modeller, Controllerlar ve Core sınıfları için pathler
            $paths = [
                APP_ROOT . '/models/',
                APP_ROOT . '/controllers/',
                APP_ROOT . '/core/',
                // Diğer klasörleriniz varsa buraya ekleyin (örneğin /lib/, /helpers/)
            ];

            foreach ($paths as $path) {
                $file = $path . $class . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        });
    }
}

// Autoloader'ı kaydet
Autoloader::register();
?>