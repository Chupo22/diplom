<?
define('ADMIN_MODULE_NAME', 'automated_testing_system');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

use LearningDatabase\ORM\TestTable as Test;

IncludeModuleLangFile(__FILE__);

$elementPage = ADMIN_MODULE_NAME.'_test.php';

if (!$USER->IsAdmin())
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
if (!CModule::IncludeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));

$APPLICATION->SetTitle('Список тестов');

$sTableID = ADMIN_MODULE_NAME;
$oSort = new CAdminSorting($sTableID, 'NAME', 'asc');
$lAdmin = new CAdminList($sTableID, $oSort);

$arHeaders = [];
foreach(Test::getEntity()->getFields() as $obField) {
	if(!method_exists($obField, 'isRequired'))
		continue; //todo-sem для referencefield нужно будет отдельное условие
	
	$fieldName = $obField->getName();
	$arHeaders[] = ['id' => $fieldName, 'content' => $fieldName, 'sort' => $fieldName, 'default' => true];
}

$lAdmin->AddHeaders($arHeaders);

// menu
if ($_REQUEST['mode'] !== 'list')
{
	$aMenu = [
		[
			'TEXT'	=> 'Добавить тест',
			'TITLE'	=> 'Добавить тест',
			'LINK'	=> ADMIN_MODULE_NAME.'_test.php?lang='.LANGUAGE_ID,
			'ICON'	=> 'btn_new',
		]
	];

	$context = new CAdminContextMenu($aMenu);
}

$arElements = [];
$dbElements = Test::getList();
while($arElement = $dbElements->fetch()) {
	$arElements[] = $arElement;
}

foreach($arElements as $arElement) {
	$row = $lAdmin->AddRow($arElement['ID'], $arElement);
		
	$row->AddActions([
		[
			'ICON' => 'list',
			'TEXT' => 'Список упражнений',
			'ACTION' => $lAdmin->ActionRedirect(ADMIN_MODULE_NAME.'_exercises.php?'.http_build_query([
				'id' => $arElement['ID'],
			])),
			'DEFAULT' => true,
		],
		[
			'ICON' => 'edit',
			'TEXT' => GetMessage('MAIN_ADMIN_MENU_EDIT'),
			'ACTION' => $lAdmin->ActionRedirect($elementPage.'?'.http_build_query([
				'id' => $arElement['ID'],
				'action' => 'edit',
			]))
		],
		[
			'ICON' => 'delete',
			'TEXT' => GetMessage('MAIN_ADMIN_MENU_DELETE'),
			'ACTION' => $lAdmin->ActionRedirect($elementPage.'?'.http_build_query([
				'id' => $arElement['ID'],
				'sessid' => bitrix_sessid(),
				'action' => 'delete',
			]))
			
		]
	]);
}


require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

$context->Show();


$lAdmin->CheckListMode();

$lAdmin->DisplayList();


require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');

