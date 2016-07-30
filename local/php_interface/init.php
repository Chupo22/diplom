<?
require_once $_SERVER['DOCUMENT_ROOT'].'/local/classes/cpl.php';
spl_autoload_register(function ($class){
    $folder = $_SERVER['DOCUMENT_ROOT']."/local/classes";
	$convertedClass = str_replace("\\", "/", $class);
    $fileName = "$folder/$convertedClass.php";

    if (file_exists($fileName)) {
		/** @noinspection PhpIncludeInspection */
		include_once $fileName;
    }
});

file_exists($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/defines.php') && include('defines.php');
