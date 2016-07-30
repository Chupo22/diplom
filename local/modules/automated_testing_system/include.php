<?

include_once 'info.php'; //todo-sem файл подключается два раза, нужно посмотреть почему

CModule::AddAutoloadClasses(\ATSModule\Info::$MODULE_ID, [
	'ATSModule\AdminList' => 'classes/adminList.php',
	'ATSModule\AdminItem' => 'classes/adminItem.php',
	'ATSModule\Tools' => 'classes/tools.php',
	'ATSModule\AdminMenu' => 'classes/adminMenu.php',
]);
