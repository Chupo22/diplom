<?
namespace AutomatedTestingSystem\ORM;

use Bitrix\Main\Entity;
use AutomatedTestingSystem\CDatabase;
use Helpers\ORM\IntegerField;

class ConditionTable extends Entity\DataManager
{
	const CONDITION_MORE = 'MORE';
	const CONDITION_MORE_OR_EQUALLY = 'MORE_OR_EQUALLY';
	const CONDITION_LESS = 'LESS';
	const CONDITION_LESS_OR_EQUALLY = 'LESS_OR_EQUALLY';
	const CONDITION_EQUALLY = 'EQUALLY';
	const CONDITION_PARTIAL_MATCH = 'PARTIAL_MATCH';
	
    public static function getTableName()
    {
        return 'orm_conditions';
    }
    
    public static function getMap()
    {
        return [
            new IntegerField(
				'id',
				[
					'primary' => true,
					'autocomplete' => true
				]
			),
            new Entity\StringField('name'),
            new Entity\StringField('code'),
        ];
    }
	
	public static function getConditions($table, $column){
		$columnType = CDatabase::getColumn($table, $column)['DATA_TYPE'];
		
		$arResult = [];
		switch($columnType){
			case 'varchar':
			case 'char':
				//$arResult = [
				//	self::CONDITION_MORE,
				//	self::CONDITION_LESS,
				//	self::CONDITION_EQUALLY,
				//];
				//break;
			case 'int':
				//$arResult = [
				//	self::CONDITION_MORE,
				//	self::CONDITION_LESS,
				//	self::CONDITION_EQUALLY,
				//];
			//break;
			default:
				
				//todo: пока както так. Нужно переделать.
				$arResult = [
					self::CONDITION_MORE,
					self::CONDITION_LESS,
					self::CONDITION_EQUALLY,
				];
		}
		return $arResult;
	}
	
	//public static function checkResult($condition, $taskValue, $resultValue){
	//	switch($condition){
	//		case self::CONDITION_MORE:
	//			return $resultValue > $taskValue;
	//			break;
	//		case self::CONDITION_LESS:
	//			return $resultValue < $taskValue;
	//			break;
	//		case self::CONDITION_EQUALLY:
	//			return $resultValue === $taskValue;
	//			break;
	//	}			
	//	return false;
	//}
	
	static function getSqlCondition($condition){
		$result = false;
		switch($condition){
			case self::CONDITION_EQUALLY:
				$result = '=';
				break;
			case self::CONDITION_MORE:
				$result = '>';
				break;
			case self::CONDITION_MORE_OR_EQUALLY:
				$result = '>=';
				break;
			case self::CONDITION_LESS:
				$result = '<';
				break;
			case self::CONDITION_LESS_OR_EQUALLY:
				$result = '<=';
				break;
			case self::CONDITION_PARTIAL_MATCH:
				$result = 'LIKE';
				break;
		}
		return $result;
	}
}
