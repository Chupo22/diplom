<?require_once 'prolog_before.php';
IncludeModuleLangFile(__FILE__);

//use ATSModule\CAdminList;
use ATSModule\Tools as ModuleTools;
use AutomatedTestingSystem\ORM\UserExerciseTable as UserExercise;

$elementPage = ADMIN_MODULE_NAME.'_user_exercise.php';

$obAdminList = new CAdminList('user_exercise');


$obAdminList->AddHeaders([
	['id' => 'number', 'content' => 'Номер', 'sort' => '100', 'default' => true],
	['id' => 'name', 'content' => 'Наименование', 'sort' => '100', 'default' => true],
	['id' => 'completed', 'content' => 'Выполнено', 'sort' => '100', 'default' => true],
	['id' => 'query', 'content' => 'Запрос', 'sort' => '100', 'default' => true],
	['id' => 'successQuery', 'content' => 'Успешный запрос', 'sort' => '100', 'default' => true],
]);

$dbElements = UserExercise::getList([
	'filter' => ['userId' => $_REQUEST['id']],
	'select' => [
		'*',
		'name' => 'exercise.name',
		'number' => 'exercise.number',
	],
]);
while($arElement = $dbElements->fetch()) {
	$arElement['completed'] = $arElement['completed'] == 1 ? 'да' : 'нет';
	$row = $obAdminList->AddRow($arElement['id'], $arElement);
	
	$row->AddActions([
		[
			'ICON' => 'list',
			'TEXT' => 'Список',
			'ACTION' => $obAdminList->ActionRedirect(ADMIN_MODULE_NAME.'user_exercises.php?'.http_build_query([
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

