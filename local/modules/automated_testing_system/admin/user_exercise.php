<?require_once 'prolog_before.php';
IncludeModuleLangFile(__FILE__);

use ATSModule\AdminItem;
use ATSModule\Tools as ModuleTools;
use AutomatedTestingSystem\ORM\TestTable as Test;

$obAdminItem = new AdminItem(new Test);

$backUrl = ADMIN_MODULE_NAME.'_user_exercises.php?lang='.LANGUAGE_ID;


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
	$result = Test::update($obAdminItem->arItem['id'], $arData);
	if($result->isSuccess())
		if($_REQUEST['apply'])
			$obAdminItem->arNotes[] = ModuleTools::GetMessage('ITEM_UPDATED');
		else
			LocalRedirect($backUrl);
	else
		$obAdminItem->arErrors = array_merge($obAdminItem->arErrors, $result->getErrorMessages());
}

require_once 'prolog_after.php';

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
		['DIV' => 'edit1', 'TAB' => 'Редактирование', 'TITLE'=> 'Редактирование']
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
		<?
		$tabControl->Buttons(['disabled' => false, 'back_url' => $backUrl]);
		$tabControl->End();
		?>
	</form>
<?endif?>
<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');

