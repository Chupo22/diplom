<?namespace ATSModule;

class Tools{
	public static function GetMessage($mess) {
		return GetMessage(ADMIN_MODULE_NAME.$mess);
	}
}
