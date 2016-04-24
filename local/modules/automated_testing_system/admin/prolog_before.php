<?
define('ADMIN_MODULE_NAME', 'automated_testing_system');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

CModule::IncludeModule(ADMIN_MODULE_NAME);

if(!$USER->IsAdmin())
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
if(!CModule::IncludeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
