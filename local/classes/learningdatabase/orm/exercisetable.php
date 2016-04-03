<?
namespace LearningDatabase\ORM;

use Bitrix\Main\Entity;
use LearningDatabase\ORM\TestTable as Test;
use LearningDatabase\ORM\TaskTable as Task;

class ExerciseTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'orm_exercises';
    }
    
    public static function getMap()
    {
		global $USER;
        return [
            new Entity\IntegerField('ID', [
				'primary' => true,
				'autocomplete' => true
			]),
            new Entity\IntegerField('NUMBER',['required' => true,'unique' => true]),
            new Entity\StringField('NAME'),
            new Entity\IntegerField('TEST_ID', ['required' => true]),
            new Entity\ReferenceField('TEST', get_class(new Test), ['=this.TEST_ID' => 'ref.ID']),
            new Entity\ReferenceField('TASK', get_class(new Task), ['=this.TEST_ID' => 'ref.ID'], ['multiple' => true]),
            new Entity\ReferenceField('USER_EXERCISE', get_class(new UserExerciseTable), ['=this.ID' => 'ref.EXERCISE_ID', $USER->GetId() => 'ref.USER_ID']),
        ];
    }
}
