<?php
define('WEBROOT', str_replace("index.php", "", $_SERVER['SCRIPT_NAME']));
define('ROOT', str_replace("index.php", "", $_SERVER['SCRIPT_FILENAME']));

if (!isset($_SERVER['PATH_INFO'])) {
    $_SERVER['PATH_INFO'] = 'index.php';
}

require ROOT . 'Config/model.php';
require ROOT . 'Config/controller.php';

preg_match('/^(.*)$/', $_SERVER["PATH_INFO"], $matches);
$params = array_slice(explode('/', $matches[1]), 1);

foreach ($params as $param) {
    $param = str_replace("../", "protect", $param);
    $param = str_replace(";", "protect", $param);
    $param = str_replace("%", "protect", $param);
}

$controller = isset($params[0]) && !empty($params[0]) ? $params[0] : 'home';
$method = isset($params[1]) && !empty($params[1]) ? $params[1] : 'index';
$argv = isset($params[2]) && !empty($params[2]) ? $params[2] : '';

if ($controller == 'profile') {
    $argv = isset($params[1]) && !empty($params[1]) ? $params[1] : '';
    if ($argv == "edit" || $argv == "activity" || $argv == "settings") {
        $method = $argv;
        $argv = "";
    } else {
        $method = "index";
    }
}
if (file_exists('controllers/' . $controller . '.php')) {
    require 'controllers/' . $controller . '.php';
    $controller = new $controller();
    if (method_exists($controller, $method)) {
        if (!empty($argv)) {
            $controller->$method($argv);
        } else {
            $controller->$method();
        }

    } else {
        include '404.php';
    }
} else {
    include '404.php';
}