<?
namespace LearningDatabase\ORM;

use Bitrix\Main\DB\Exception;
use Bitrix\Main\Entity;

class UserExerciseTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'orm_user_exercises_table';
    }
	
	public static function onBeforeAdd(Entity\Event $event)
    {
        $arItem = $event->getParameter("fields");
		$result = new Entity\EventResult;
		if(UserExerciseTable::getList([
			'filter' => [
				'USER_ID' => $arItem['USER_ID'],
				'EXERCISE_ID' => $arItem['EXERCISE_ID']
			],
			'select' => ['ID'],
			'limit' => 1
		])->getSelectedRowsCount())
			$result->addError(new Entity\EntityError(
				"UserExercise with this USER_ID ({$arItem['USER_ID']}) and EXERCISE_ID ({$arItem['EXERCISE_ID']}) already exists!",
				'ADD_ERROR'
			));
		return $result;
    }
    
    public static function getMap()
    {
        return [
            new Entity\IntegerField('ID', [
				'primary' => true,
				'autocomplete' => true
			]),
            new Entity\BooleanField('COMPLETED'),
            new Entity\TextField('QUERY'),
			
			
            new Entity\IntegerField('EXERCISE_ID', ['required' => true]),
            new Entity\IntegerField('USER_ID', ['required' => true]),
			
			new Entity\ReferenceField('EXERCISE', get_class(new ExerciseTable), ['=this.EXERCISE_ID' => 'ref.ID']),
			new Entity\ReferenceField('TEST', get_class(new TestTable), ['=this.EXERCISE.TEST_ID' => 'ref.ID']),
            new Entity\ReferenceField('USER', '\Bitrix\Main\UserTable', ['=this.USER_ID' => 'ref.ID']),
        ];
    }
}
