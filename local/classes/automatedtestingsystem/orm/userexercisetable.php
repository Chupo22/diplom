<?
namespace AutomatedTestingSystem\ORM;

use Bitrix\Main\DB\Exception;
use Bitrix\Main\Entity;
use \Bitrix\Main\UserTable;
use Helpers\ORM\BooleanField;
use Helpers\ORM\IntegerField;

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
				'userId' => $arItem['userId'],
				'exerciseId' => $arItem['exerciseId']
			],
			'select' => ['id'],
			'limit' => 1
		])->getSelectedRowsCount())
			$result->addError(new Entity\EntityError(
				"UserExercise with this USER_ID ({$arItem['userId']}) and EXERCISE_ID ({$arItem['exerciseId']}) already exists!",
				'ADD_ERROR'
			));
		return $result;
    }
    
    public static function getMap()
    {
        return [
            new IntegerField('id', [
				'primary' => true,
				'autocomplete' => true
			]),
            new BooleanField('completed'),
            new Entity\TextField('query', [
				'save_data_modification' => function(){
					return [
						function($value){
							return base64_encode($value);
						}
					];
				},
				'fetch_data_modification' => function(){
					return[
						function($value){
							return base64_decode($value);
						}
					];
				}
			]),
			
			
            new IntegerField('exerciseId', ['required' => true]),
            new IntegerField('userId', ['required' => true]),
			
			new Entity\ReferenceField('exercise', get_class(new ExerciseTable), ['=this.exerciseId' => 'ref.id']),
			new Entity\ReferenceField('test', get_class(new TestTable), ['=this.exercise.testId' => 'ref.id']),
            new Entity\ReferenceField('user', get_class(new UserTable), ['=this.userId' => 'ref.ID']),
        ];
    }
}
