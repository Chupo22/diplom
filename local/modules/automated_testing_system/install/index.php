<?
global $MESS;
use ATSModule\Info;

$PathInstall = str_replace("\\", "/", __FILE__);
include_once substr(__FILE__, 0, strlen(__FILE__) - strlen("/install/index.php")).'/info.php';
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
IncludeModuleLangFile($PathInstall.'/install.php');
 
if(class_exists(Info::$MODULE_ID)) return;
class automated_testing_system extends CModule {
	var $MODULE_ID;
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
		
		
		
		/** @noinspection PhpIncludeInspection */
		include_once(Info::$MODULE_PATH.'/defines.php');
		
		$this->MODULE_INSTALL_PATH = Info::$MODULE_PATH.'/install';
		$this->BX_INSTALL_PATH = $DOCUMENT_ROOT.'/'.Bitrix\Main\Loader::BITRIX_HOLDER;
		
		$this->MODULE_ID = Info::$MODULE_ID;
		$this->MODULE_VERSION = Info::$VERSION;
		$this->MODULE_VERSION_DATE = Info::$VERSION_DATE;
		$this->MODULE_NAME = Info::$MODULE_NAME;
		$this->MODULE_DESCRIPTION = Info::$MODULE_DESCRIPTION;
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
