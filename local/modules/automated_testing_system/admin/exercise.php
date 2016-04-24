<?require_once 'prolog_before.php';
IncludeModuleLangFile(__FILE__);

use ATSModule\CAdminItem;
use ATSModule\Tools as ModuleTools;
use LearningDatabase\ORM\ExerciseTable as Exercise;

$obAdminItem = new CAdminItem(new Exercise);

$backUrl = ADMIN_MODULE_NAME.'_tests.php?lang='.LANGUAGE_ID;

$obAdminItem->getItem($_REQUEST['id']);

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
	$result = Exercise::update($obAdminItem->arItem['ID'], $arData);
	if($result->isSuccess())
		if($_REQUEST['apply'])
			$obAdminItem->arNotes[] = ModuleTools::GetMessage('ITEM_UPDATED');
		else
			LocalRedirect($backUrl);
	else
		$obAdminItem->arErrors = array_merge($obAdminItem->arErrors, $result->getErrorMessages());
}

require_once 'prolog_after.php';

$dbSchema = LearningDatabase\CDatabase::getDbSchema();

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
			<td width='40%'>
				Задание:<span style="color:red">*</span>
			</td>
			<td>
				<select>
					<option value="">Выберите таблицу</option>
					<?foreach(array_keys($dbSchema) as $tableName):?>
						<option value=""><?=$tableName?></option>
					<?endforeach?>
				</select>
				<?$table = reset($dbSchema)?>
				<select>
					<option value="">Выберите колонку</option>
					<?foreach($table['COLUMNS'] as $columnName => $arCol):?>
						<option value=""><?=$columnName?></option>
					<?endforeach?>
				</select>
				<select>
					<option value="">Выберите условие</option>
					<option value="">Больше</option>
					<option value="">Меньше</option>
					<option value="">Равен</option>
				</select>
				<input type="text" placeholder="Укажите значение" />
			</td>
		</tr>	
		<tr>
			<td></td>
			<td>
				<textarea name="" id="123" cols="30" rows="10">131312</textarea>		
			</td>
		</tr>
		<?
		$tabControl->Buttons(['disabled' => false, 'back_url' => $backUrl]);
		$tabControl->End();
		?>
	</form>
<?endif?>
<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');

