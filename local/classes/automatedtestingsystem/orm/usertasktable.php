<?
namespace AutomatedTestingSystem\ORM;

use Bitrix\Main\Entity;
use Bitrix\Main\UserTable;
use AutomatedTestingSystem\CDatabase;

class UserTaskTable extends Entity\DataManager
{
	
    public static function getTableName()
    {
        return 'orm_user_tasks';
    }
    
    public static function getMap()
    {
        return [
            new Entity\IntegerField('id', [
				'primary' => true,
				'autocomplete' => true
			]),
            new Entity\IntegerField('userExerciseId', ['required' => true]),
            new Entity\ReferenceField('user', get_class(new UserTable), ['=this.userExercise.userId' => 'ref.ID']),
            new Entity\ReferenceField('userExercise', get_class(new UserExerciseTable), ['=this.userExerciseId' => 'ref.id']),
            new Entity\ReferenceField('exercise', get_class(new ExerciseTable), ['=this.userExercise.exerciseId' => 'ref.id']),
            new Entity\ReferenceField('test', get_class(new TestTable), ['=this.userExercise.exercise.testId' => 'ref.id']),
			
			
            new Entity\StringField('table'),
            new Entity\StringField('column'),
            new Entity\StringField('condition'),
            new Entity\StringField('value'),
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
					$query = "
						SELECT $columnName FROM $tableName GROUP BY $columnName
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
