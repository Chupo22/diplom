<?
IncludeModuleLangFile(__FILE__);
$module_id = 'automated_testing_system';

CModule::IncludeModule($module_id);

if($APPLICATION->GetGroupRight($module_id)>'D') { //проверка уровня доступа к модулю
	$MODULE_PATH = $_SERVER['DOCUMENT_ROOT'].getLocalPath('modules/'.$module_id);
	
	return \ATSModule\AdminMenu::getMenu();
}
return false;
