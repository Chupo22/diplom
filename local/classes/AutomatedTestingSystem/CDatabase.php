<?
namespace AutomatedTestingSystem;


class CDatabase extends \CDatabase{
	const host = 'localhost';
	const login = 'learning_db';
	const dbName = 'employees';
	const password = 'eZrRLbLqT6rwaeRS';
	
	static $connection = false;
	static $arTables = [];
	
	static function getConnection(){
		if(!self::$connection){
			$DB = self::$connection = new self;
			$DB->debug = DB_DEBUG;
			$DB->Connect(self::host, self::dbName, self::login, self::password);
		}

		return $result =  self::$connection;
	}
	
	public static function setDbSchema(){
		$dbName = self::dbName;
		$dbRes = CDatabase::getConnection()->Query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='$dbName'");
		while($arTable = $dbRes->Fetch())
			self::$arTables[$arTable['TABLE_NAME']] = $arTable;
		$dbRes = CDatabase::getConnection()->Query("SELECT TABLE_NAME, COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='$dbName'");
		while($arColumn = $dbRes->Fetch())
			self::$arTables[$arColumn['TABLE_NAME']]['COLUMNS'][$arColumn['COLUMN_NAME']] = $arColumn;
	}
	
	public static function getDbSchema(){
		if(!self::$arTables)
			self::setDbSchema();
		return self::$arTables;
	}
	
	public static function getColumn($tableName, $columnName){
		if(!$tableName || !$columnName)
			return false;
		
		if(!self::$arTables)
			self::setDbSchema();
		return self::$arTables[$tableName]['COLUMNS'][$columnName];
	}
}
