<?
namespace ATSModule {
	$MODULE_PATH = substr(__FILE__, 0, strlen(__FILE__) - strlen('/'.basename(__FILE__)));
	class Info
	{
		static $MODULE_PATH;
		static $MODULE_ID = 'automated_testing_system'; //todo-sem нужно будет получить его по нормальному
		static $VERSION = '1.0.0';
		static $VERSION_DATE = '2016-03-09 22:44:00';
		static $MODULE_NAME = 'Автоматизированная система тестирования';
		static $MODULE_DESCRIPTION = 'Автоматизированная система тестирования';
	}
	Info::$MODULE_PATH = $MODULE_PATH;
}
