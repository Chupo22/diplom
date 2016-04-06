<?
define('ADMIN_MODULE_NAME', 'automated_testing_system');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

use LearningDatabase\ORM\TestTable as Test;

IncludeModuleLangFile(__FILE__);

if (!$USER->IsAdmin())
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
if (!CModule::IncludeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));

$APPLICATION->SetTitle('Редактирование теста');

$backUrl = ADMIN_MODULE_NAME.'_tests.php?lang='.LANGUAGE_ID;

$arMessages = [
	'errors' => [],
	'notes' => [],
];
$bShowForm = true;

$arTest = Test::getList([
	'filter' => ['ID' => $_REQUEST['id']],
	'limit' => 1,
])->fetch();
if(!$arTest['ID']) {
	$arMessages['errors'][] = 'Элемент не найден';
	$bShowForm = false;
}

$arFields = [];
foreach(Test::getEntity()->getFields() as $obField){
//foreach(\LearningDatabase\ORM\UserExerciseTable::getEntity()->getFields() as $obTestField){
	if(!method_exists($obField, 'isRequired'))
		continue; //todo-sem для referencefield нужно будет отдельное условие
	
	/**
	 * 
	 * @var Bitrix\Main\Entity\IntegerField | Bitrix\Main\Entity\StringField | Bitrix\Main\Entity\BooleanField | Bitrix\Main\Entity\TextField $obField */
	$fieldName = $obField->getName();
	$arFields[] = [
		'name' => $fieldName,
		'can_edit' => !$obField->isAutocomplete(),
		'required' => $obField->isRequired(),
		'value' => isset($_REQUEST['data'][$fieldName]) ? $_REQUEST['data'][$fieldName] : $arTest[$fieldName],
	];
}


// delete action
if($_REQUEST['action'] === 'delete' && check_bitrix_sessid()){
//	HL\Hiable::delete($hlblock['ID']);

//	LocalRedirect('highloadblock_index.php?lang='.LANGUAGE_ID);
}


// save action
if (($_REQUEST['save'] || $_REQUEST['apply']) && check_bitrix_sessid()){
	$arData = [];
	foreach($arFields as $arField) {
		$fieldName = $arField['name'];
		if($arField['can_edit'] && isset($_REQUEST['data'][$fieldName]))
			$arData[$fieldName] = $_REQUEST['data'][$fieldName];
	}
	$result = Test::update($arTest['ID'], $arData);
	if($result->isSuccess())
		if($_REQUEST['apply'])
			$arMessages['notes'][] = 'Тест успешно обновлён';
		else
			LocalRedirect($backUrl);
	else
		$arMessages['errors'] = array_merge($arMessages['errors'], $result->getErrorMessages());
}


require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

if($arMessages['errors'])
	(new CAdminMessage(''))->ShowMessage(join('\n', $arMessages['errors']));
if($arMessages['notes'])
	(new CAdminMessage(''))->ShowNote(join('\n', $arMessages['notes']));

if($bShowForm):
	(new CAdminContextMenu([
		[
			'TEXT'	=> 'Вернуться в список',
			'TITLE'	=> 'Вернуться в список',
			'LINK'	=> $backUrl,
			'ICON'	=> 'btn_list',
		]
	]))->Show();
	$tabControl = new CAdminTabControl('tabControl', [
		['DIV' => 'edit1', 'TAB' => 'Редактирование', 'TITLE'=> 'Редактирование "'.$arTest['NAME'].'"']
	]);
	?>
	<form name='form1' method='POST' action='<?=$APPLICATION->GetCurPage()?>'>
		<?=bitrix_sessid_post()?>
		<input type='hidden' name='id' value='<?=$_REQUEST['id']?>'>
		<input type='hidden' name='lang' value='<?=LANGUAGE_ID?>'>
		<?
		$tabControl->Begin();
		$tabControl->BeginNextTab();
		?>
		<?foreach($arFields as $arField):?>
			<tr>
				<td width='40%'>
					<?if($arField['required']):?>
						<?=$arField['name']?>:<span style="color:red"">*</div>
					<?else:?>
						<?=$arField['name']?>:
					<?endif?>
				</td>
				<td>
					<?if($arField['can_edit']):?>
						<input type="text" name="data[<?=$arField['name']?>]" size="30" value="<?=$arField['value']?>">
					<?else:?>
						<?=$arField['value']?>
					<?endif?>
				</td>
			</tr>
		<?endforeach?>
		<?
		$tabControl->Buttons(['disabled' => false, 'back_url' => $backUrl]);
		$tabControl->End();
		?>
	</form>
<?endif?>
<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');

