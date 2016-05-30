<?require_once 'prolog_before.php';
IncludeModuleLangFile(__FILE__);

use ATSModule\CAdminList;
use ATSModule\Tools as ModuleTools;
use AutomatedTestingSystem\ORM\TestTable as Test;

$elementPage = ADMIN_MODULE_NAME.'_test.php';

$obAdminList = new CAdminList(new Test);

$obAdminList->AddHeaders();

$dbElements = Test::getList();
while($arElement = $dbElements->fetch()) {
	$row = $obAdminList->AddRow($arElement['id'], $arElement);
	
	$row->AddActions([
		[
			'ICON' => 'list',
			'TEXT' => 'Список упражнений',
			'ACTION' => $obAdminList->ActionRedirect(ADMIN_MODULE_NAME.'_exercises.php?'.http_build_query([
				'id' => $arElement['id'],
			])),
			'DEFAULT' => true,
		],
		[
			'ICON' => 'edit',
			'TEXT' => GetMessage('MAIN_ADMIN_MENU_EDIT'),
			'ACTION' => $obAdminList->ActionRedirect($elementPage.'?'.http_build_query([
				'id' => $arElement['id'],
				'action' => 'edit',
			]))
		],
		[
			'ICON' => 'delete',
			'TEXT' => GetMessage('MAIN_ADMIN_MENU_DELETE'),
			'ACTION' => $obAdminList->ActionRedirect($elementPage.'?'.http_build_query([
				'id' => $arElement['id'],
				'sessid' => bitrix_sessid(),
				'action' => 'delete',
			]))
			
		]
	]);
}

require_once 'prolog_after.php';

(new CAdminContextMenu([[
		'TEXT'	=> ModuleTools::GetMessage('ITEM_ADD'),
		'TITLE'	=> ModuleTools::GetMessage('ITEM_ADD'),
		'LINK'	=> ADMIN_MODULE_NAME.'_test.php?lang='.LANGUAGE_ID,
		'ICON'	=> 'btn_new',
]]))->Show();

$obAdminList->CheckListMode();

$obAdminList->DisplayList();

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');

