<?
require_once $_SERVER['DOCUMENT_ROOT'].'/local/classes/cpl.php';
spl_autoload_register(function ($class){
    $baseDir = $_SERVER['DOCUMENT_ROOT']."/local";
    $className = mb_strtolower($class, SITE_CHARSET);
    $fileName = $baseDir . "/classes/" . str_replace("\\", "/", $className) . ".php";

    if (file_exists($fileName)) {
		/** @noinspection PhpIncludeInspection */
		include_once $fileName;
    }
});

file_exists($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/defines.php') && include('defines.php');
