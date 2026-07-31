<?php
/**
 * Path Helper Functions
 * 
 * Provides centralized path management for the application
 * Use these functions to ensure correct paths regardless of file location
 */

// Get the base directory of the project
define('BASE_PATH', dirname(__DIR__, 2) . DIRECTORY_SEPARATOR);

// Define path constants
define('CONFIG_PATH', BASE_PATH . 'config' . DIRECTORY_SEPARATOR);
define('INCLUDES_PATH', BASE_PATH . 'includes' . DIRECTORY_SEPARATOR);
define('ASSETS_PATH', BASE_PATH . 'assets' . DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', BASE_PATH . 'public' . DIRECTORY_SEPARATOR);
define('MODULES_PATH', BASE_PATH . 'modules' . DIRECTORY_SEPARATOR);

/**
 * Get path to config directory
 * @return string
 */
function getConfigPath() {
    return CONFIG_PATH;
}

/**
 * Get path to includes directory
 * @return string
 */
function getIncludesPath() {
    return INCLUDES_PATH;
}

/**
 * Get path to assets directory
 * @return string
 */
function getAssetsPath() {
    return ASSETS_PATH;
}

/**
 * Get path to public directory
 * @return string
 */
function getPublicPath() {
    return PUBLIC_PATH;
}

/**
 * Get path to modules directory
 * @return string
 */
function getModulesPath() {
    return MODULES_PATH;
}

/**
 * Include database configuration
 */
function includeDatabase() {
    require_once CONFIG_PATH . 'database.php';
}

/**
 * Include sidebar component
 */
function includeSidebar() {
    require_once INCLUDES_PATH . 'sidebar.php';
}

/**
 * Get relative path from current file to target
 * @param string $target Target file or directory
 * @return string Relative path
 */
function getRelativePath($target) {
    $currentDir = dirname(debug_backtrace()[0]['file']);
    $targetPath = BASE_PATH . $target;
    
    $relative = str_replace($currentDir . DIRECTORY_SEPARATOR, '', $targetPath);
    return $relative;
}
