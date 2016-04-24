<?require_once 'prolog_before.php';
IncludeModuleLangFile(__FILE__);

use ATSModule\CAdminList;
use ATSModule\Tools as ModuleTools;
use LearningDatabase\ORM\ExerciseTable as Exercise;

$elementPage = ADMIN_MODULE_NAME.'_exercise.php';

$obAdminList = new CAdminList(new Exercise);

$obAdminList->AddHeaders();

$dbElements = Exercise::getList(['filter' => ['TEST_ID' => $_REQUEST['id']]]);
while($arElement = $dbElements->fetch()) {
	$row = $obAdminList->AddRow($arElement['ID'], $arElement);
	
	$row->AddActions([
		[
			'ICON' => 'edit',
			'TEXT' => GetMessage('MAIN_ADMIN_MENU_EDIT'),
			'ACTION' => $obAdminList->ActionRedirect($elementPage.'?'.http_build_query([
				'id' => $arElement['ID'],
				'action' => 'edit',
			])),
			'DEFAULT' => true,
		],
		[
			'ICON' => 'delete',
			'TEXT' => GetMessage('MAIN_ADMIN_MENU_DELETE'),
			'ACTION' => $obAdminList->ActionRedirect($elementPage.'?'.http_build_query([
				'id' => $arElement['ID'],
				'sessid' => bitrix_sessid(),
				'action' => 'delete',
			]))
			
		]
	]);
}

require_once 'prolog_after.php';

(new CAdminContextMenu([
	[
		'TEXT'	=> ModuleTools::GetMessage('ITEM_ADD'),
		'TITLE'	=> ModuleTools::GetMessage('ITEM_ADD'),
		'LINK'	=> ADMIN_MODULE_NAME.'_test.php?lang='.LANGUAGE_ID,
		'ICON'	=> 'btn_new',
	]
]))->Show();

$obAdminList->CheckListMode();

$obAdminList->DisplayList();


require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');

