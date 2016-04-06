<?
define('ADMIN_MODULE_NAME', 'automated_testing_system');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

use LearningDatabase\ORM\ExerciseTable as Exercise;

IncludeModuleLangFile(__FILE__);

$elementPage = ADMIN_MODULE_NAME.'_exercise.php';

if (!$USER->IsAdmin())
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
if (!CModule::IncludeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));

$APPLICATION->SetTitle('Список упражнений');

$sTableID = ADMIN_MODULE_NAME;
$oSort = new CAdminSorting($sTableID, 'NAME', 'asc');
$lAdmin = new CAdminList($sTableID, $oSort);

$arHeaders = [];
foreach(Exercise::getEntity()->getFields() as $obField) {
	if(!method_exists($obField, 'isRequired'))
		continue; //todo-sem для referencefield нужно будет отдельное условие
	
	$fieldName = $obField->getName();
	$arHeaders[] = ['id' => $fieldName, 'content' => $fieldName, 'sort' => $fieldName, 'default' => true];
}
$lAdmin->AddHeaders($arHeaders);



$arElements = [];
$dbElements = Exercise::getList(['filter' => ['TEST_ID' => $_REQUEST['id']]]);
while($arElement = $dbElements->fetch()) {
	$arElements[] = $arElement;
}

foreach($arElements as $arElement) {
	$row = $lAdmin->AddRow($arElement['ID'], $arElement);
	
	$row->AddActions([
		[
			'ICON' => 'edit',
			'TEXT' => GetMessage('MAIN_ADMIN_MENU_EDIT'),
			'ACTION' => $lAdmin->ActionRedirect($elementPage.'?'.http_build_query([
				'id' => $arElement['ID'],
				'action' => 'edit',
			])),
			'DEFAULT' => true,
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

(new CAdminContextMenu([
	[
		'TEXT'	=> 'Добавить упражнение',
		'TITLE'	=> 'Добавить упражнение',
		'LINK'	=> ADMIN_MODULE_NAME.'_test.php?lang='.LANGUAGE_ID,
		'ICON'	=> 'btn_new',
	]
]))->Show();

$lAdmin->CheckListMode();

$lAdmin->DisplayList();


require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');

