<?php
declare(strict_types=1);

if (!defined('ABSPATH')) { exit; }

/**
 * Very small PSR-4-like autoloader for the GZO namespace (no Composer).
 */
spl_autoload_register(static function (string $class): void {
    $prefix = 'GZO\\';
    if (strpos($class, $prefix) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $relative_path = str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
    $file = GZO_PLUGIN_DIR . 'includes' . DIRECTORY_SEPARATOR . $relative_path;

    if (is_readable($file)) {
        require_once $file;
    }
});
