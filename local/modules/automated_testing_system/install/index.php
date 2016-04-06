<?
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
IncludeModuleLangFile($PathInstall.'/install.php');
 
if(class_exists('automated_testing_system')) return;
class automated_testing_system extends CModule {
	var $MODULE_ID = 'automated_testing_system';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = 'Y';

	var $MODULE_INSTALL_PATH;
	var $BX_INSTALL_PATH;

	function automated_testing_system() {
		global $DOCUMENT_ROOT;
		
		$arModuleVersion = [];
		$MODULE_PATH = $DOCUMENT_ROOT.getLocalPath('modules/'.$this->MODULE_ID);
		
		include_once($MODULE_PATH.'/defines.php');
		
		$this->MODULE_INSTALL_PATH = $MODULE_PATH.'/install';
		$this->BX_INSTALL_PATH = $DOCUMENT_ROOT."/".Bitrix\Main\Loader::BITRIX_HOLDER;
        include($this->MODULE_INSTALL_PATH . '/version.php');

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
            $this->MODULE_NAME = $arModuleVersion['MODULE_NAME'];
            $this->MODULE_DESCRIPTION = $arModuleVersion['MODULE_DESCRIPTION'];
        }
	}
	function DoInstall() {
		global $APPLICATION;
		$this->InstallFiles();
		RegisterModule($this->MODULE_ID);
		$APPLICATION->IncludeAdminFile('Установка модуля '.$this->MODULE_NAME, $this->MODULE_INSTALL_PATH.'/step1.php');
	}
	function InstallFiles($arParams = []) {
		
		CopyDirFiles($this->MODULE_INSTALL_PATH.'/admin', $this->BX_INSTALL_PATH.'/admin', true, true);
		CopyDirFiles($this->MODULE_INSTALL_PATH.'/panel', $this->BX_INSTALL_PATH.'/panel', true, true);
		return true;
	}
	function UnInstallFiles() {
		DeleteDirFiles($this->MODULE_INSTALL_PATH.'/admin', $this->BX_INSTALL_PATH.'/admin');
		DeleteDirFilesEx(str_replace($_SERVER['DOCUMENT_ROOT'],'', $this->BX_INSTALL_PATH.'/panel/'.$this->MODULE_ID));
		return true;
	}
	function DoUninstall() {
		global $APPLICATION;
		$this->UnInstallFiles();
		UnRegisterModule($this->MODULE_ID);
		$APPLICATION->IncludeAdminFile('Деинсталляция модуля '.$this->MODULE_NAME, $this->MODULE_INSTALL_PATH.'/unstep1.php');
	}
}
