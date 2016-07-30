<?
use ATSModule\Tools as ModuleTools;

$APPLICATION->SetTitle(ModuleTools::GetMessage('PAGE_TITLE'));

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

if(is_a($obAdminItem, 'ATSModule\AdminItem')){
	if($obAdminItem->arErrors)
		(new CAdminMessage(''))->ShowMessage(join('\n', $obAdminItem->arErrors));
	if($obAdminItem->arNotes)
		(new CAdminMessage(''))->ShowNote(join('\n', $obAdminItem->arNotes));
}
