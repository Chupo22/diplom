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
	const CONDITION_LIKE = 'LIKE';
	const CONDITION_IN = 'IN';
	const CONDITION_BETWEEN = 'BETWEEN';
	
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
		
		$arResult = [
			self::CONDITION_EQUALLY,
			self::CONDITION_IN
		];
		
		//$arResult = [];
		switch($columnType){
			case 'int':
				$arResult[] = self::CONDITION_BETWEEN;
				$arResult[] = self::CONDITION_MORE;
				$arResult[] = self::CONDITION_MORE_OR_EQUALLY;
				$arResult[] = self::CONDITION_LESS_OR_EQUALLY;
				break;
			case 'varchar':
			case 'char':
				$arResult[] = self::CONDITION_LIKE;
				break;
		}
		return $arResult;
	}
	
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
			case self::CONDITION_LIKE:
				$result = 'LIKE';
				break;
		}
		return $result;
	}
}
