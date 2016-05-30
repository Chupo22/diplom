<?require_once 'prolog_before.php';
IncludeModuleLangFile(__FILE__);

use ATSModule\CAdminItem;
use ATSModule\Tools as ModuleTools;
use AutomatedTestingSystem\ORM\ConditionTable;
use AutomatedTestingSystem\ORM\ExerciseTable as Exercise;
use AutomatedTestingSystem\ORM\TaskTable as Task;

$obAdminItem = new CAdminItem(new Exercise);

$backUrl = ADMIN_MODULE_NAME.'_tests.php?lang='.LANGUAGE_ID;

//$obAdminItem->getItem($_REQUEST['id'], ['*', 'task']);
$obAdminItem->getItem($_REQUEST['id'], ['*']);

$arFields = $obAdminItem->getFields();

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
	$result = Exercise::update($obAdminItem->arItem['id'], $arData);
	if($result->isSuccess()){
//		$result = Task::update($obAdminItem->arItem['id'], $arData['task']);
		if($_REQUEST['apply'])
			$obAdminItem->arNotes[] = ModuleTools::GetMessage('ITEM_UPDATED');
		else
			LocalRedirect($backUrl);
	}
	else
		$obAdminItem->arErrors = array_merge($obAdminItem->arErrors, $result->getErrorMessages());
}

require_once 'prolog_after.php';
$APPLICATION->AddHeadScript('/local/modules/automated_testing_system/bundle.js');

if(!$obAdminItem->arErrors):
	(new CAdminContextMenu([
		[
			'TEXT'	=> 'Вернуться в список',
			'TITLE'	=> 'Вернуться в список',
			'LINK'	=> $backUrl,
			'ICON'	=> 'btn_list',
		]
	]))->Show();
	$tabControl = new CAdminTabControl('tabControl', [
		['DIV' => 'edit1', 'TAB' => 'Редактирование', 'TITLE'=> 'Редактирование "'.$obAdminItem->arItem['NAME'].'"']
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
						<?=$arField['name']?>:<span style="color:red">*</span>
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
		<tr>
			<td width='40%' style="vertical-align: top;">
				Задание:<span style="color:red">*</span>
			</td>
			<td id="tasks-container">
			</td>
		</tr>	
		<?
		$tabControl->Buttons(['disabled' => false, 'back_url' => $backUrl]);
		$tabControl->End();
		?>
	</form>
	<?
	$arJSParams = [];
	foreach(AutomatedTestingSystem\CDatabase::getDbSchema() as $tableName => $arTable){
		$arJSParams['tables'][] = [
			'name' => $tableName,
			'columns' => array_map(function($arColumn) use ($tableName){return [
				'name' => $arColumn['COLUMN_NAME'],
				'conditions' => ConditionTable::getConditions($tableName, $arColumn['COLUMN_NAME']),
			];}, array_values($arTable['COLUMNS'])),
		];
	}
	?>
	<script>
		var params = JSON.parse('<?=json_encode($arJSParams)?>');
		components.initTasksForm(params);
	</script>
<?endif?>
<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');

