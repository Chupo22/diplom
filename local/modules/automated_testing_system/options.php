<?
define('ADMIN_MODULE_NAME', 'automated_testing_system');
CModule::IncludeModule(ADMIN_MODULE_NAME);
$RIGHT = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);

if($RIGHT >= "R"):
	IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
	IncludeModuleLangFile(__FILE__);

	
	$arAllOptions = [
		'permitted_emails' => ['TYPE' => 'int', 'LABEL' => 'text'],
	];
	
	$aTabs = [
		["DIV" => "edit1", "TAB" => "text", "TITLE" => "text"],
	];
	$tabControl = new CAdminTabControl("tabControl", $aTabs);
	if($REQUEST_METHOD=="POST" && ($Update || $Apply) && check_bitrix_sessid()) {
		$strErrors = '';
		foreach($arAllOptions as $optionID => $arOption)
			if($arOption['TYPE'] == 'int') {
				if(intval($$optionID) <= 0 && $$optionID !== '0')
					$strErrors .= GetMessage('FIELD_MUST_BE_INT', ['#FIELD#' => $arAllOptions[$optionID]['LABEL']]).'<br />';
			}
		
		if(!$strErrors) {
			foreach($arAllOptions as $optionID => $arOption) {
				COption::SetOptionString(ADMIN_MODULE_NAME, $optionID, ${$optionID}, $arOption['LABEL']);
			}
		}
		
		if(!$strErrors) {
			$backUrl = $APPLICATION->GetCurPage()."?mid=".urlencode(ADMIN_MODULE_NAME)."&lang=".urlencode(LANGUAGE_ID)."&".$tabControl->ActiveTabParam();
			
			if(strlen($_REQUEST["back_url_settings"]) > 0)
				if($Apply)
					LocalRedirect($backUrl."&back_url_settings=".urlencode($_REQUEST["back_url_settings"]));
				else
					LocalRedirect($_REQUEST["back_url_settings"]);
			else
				LocalRedirect($backUrl);
		}
		else
			CAdminMessage::ShowMessage($strErrors);
	}
	?>
	<form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode(ADMIN_MODULE_NAME)?>&amp;lang=<?=LANGUAGE_ID?>">
		<?=bitrix_sessid_post()?>
		<?$tabControl->Begin()?>
		<?$tabControl->BeginNextTab()?>
		<tr class="heading">
			<td colspan="2">text</td>
		</tr>
		<tr>
			<td width="50%">
				text
			</td>
			<td width="50%">
				<input type="text" maxlength="255" size="55" value="text" name="<?=$optionID?>" id="<?=$optionID?>">
			</td>
		</tr>
		<?$tabControl->Buttons()?>
		<input <?if ($RIGHT<"W") echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" class="adm-btn-save">
		<input <?if ($RIGHT<"W") echo "disabled" ?> type="submit" name="Apply" value="Применить" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
		<?if(strlen($_REQUEST["back_url_settings"])>0):?>
			<input <?if ($RIGHT<"W") echo "disabled" ?> type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
			<input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>">
		<?endif?>
		
		<?$tabControl->End()?>
	</form>
<?endif?>
