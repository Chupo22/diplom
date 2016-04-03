<?
namespace LearningDatabase\ORM;

use Bitrix\Main\Entity;
use Bitrix\Main\UserTable;
use LearningDatabase\CDatabase;

class UserTaskTable extends Entity\DataManager
{
	
    public static function getTableName()
    {
        return 'orm_user_tasks';
    }
    
    public static function getMap()
    {
        return [
            new Entity\IntegerField('ID', [
				'primary' => true,
				'autocomplete' => true
			]),
            new Entity\IntegerField('USER_EXERCISE_ID', ['required' => true]),
            new Entity\ReferenceField('USER', get_class(new UserTable), ['=this.USER_EXERCISE.USER_ID' => 'ref.ID']),
            new Entity\ReferenceField('USER_EXERCISE', get_class(new UserExerciseTable), ['=this.USER_EXERCISE_ID' => 'ref.ID']),
            new Entity\ReferenceField('EXERCISE', get_class(new ExerciseTable), ['=this.USER_EXERCISE.EXERCISE_ID' => 'ref.ID']),
            new Entity\ReferenceField('TEST', get_class(new TestTable), ['=this.USER_EXERCISE.EXERCISE.TEST_ID' => 'ref.ID']),
			
			
            new Entity\StringField('TABLE'),
            new Entity\StringField('COLUMN'),
            new Entity\StringField('CONDITION'),
            new Entity\StringField('VALUE'),
        ];
    }
	
	public static function getTable($taskValue){
		$result = $taskValue;
		if(!$taskValue){
			$arSchema = CDatabase::getDbSchema();
			$result = array_rand($arSchema);
		}
		return $result;
	}
	
	public static function getColumn($tableName, $taskValue = ''){
		$result = $taskValue;
		if(!$taskValue){
			$arSchema = CDatabase::getDbSchema();
			$result = array_rand($arSchema[$tableName]['COLUMNS']);
		}
		return $result;
	}
	
	public static function getCondition($tableName, $columnName, $taskValue = ''){
		$result = $taskValue;
		if(!$taskValue){
			$arConditions = ConditionTable::getConditions($tableName, $columnName);
			$result = $arConditions[array_rand($arConditions)];
		}
		return $result;
	}
	
	public static function getValue($tableName, $columnName, $condition, $taskValue = ''){
		$result = $taskValue;
		if(!$taskValue)
		{
			switch($condition)
			{
				case ConditionTable::CONDITION_LESS;
				case ConditionTable::CONDITION_MORE;
				case ConditionTable::CONDITION_EQUALLY;
					/** @noinspection SqlDialectInspection */
					/** @noinspection SqlNoDataSourceInspection */
					$query = "SELECT $columnName FROM $tableName GROUP BY $columnName
				ORDER BY RAND()
				LIMIT 1
				";
					$dbRes = CDatabase::getConnection()->Query($query);

					$arItems = [];
					//while($arItem = $dbRes->Fetch()){
					//	$arItems[] = $arItem[$columnName];
					//}
					//$result = $arItems[array_rand($arItems)];
					$result = $dbRes->Fetch()[$columnName];
					break;
			}
		}
		return $result;
	}
}
