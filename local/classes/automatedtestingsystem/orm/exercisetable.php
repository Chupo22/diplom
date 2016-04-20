<?
namespace AutomatedTestingSystem\ORM;


use Bitrix\Main\Entity;
use AutomatedTestingSystem\ORM\TestTable as Test;
use AutomatedTestingSystem\ORM\TaskTable as Task;
use Helpers\ORM\IntegerField;


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
            new IntegerField('id', [
				'primary' => true,
				'autocomplete' => true
			]),
            new IntegerField('number',['required' => true,'unique' => true]),
            new Entity\StringField('name'),
            new IntegerField('testId', ['required' => true]),
            new Entity\ReferenceField('test', get_class(new Test), ['=this.testId' => 'ref.id']),
            new Entity\ReferenceField('task', get_class(new Task), ['=this.taskId' => 'ref.id'], ['multiple' => true]),
            new Entity\ReferenceField('userExercise', get_class(new UserExerciseTable), ['=this.id' => 'ref.exerciseId'
				, $USER->GetID() => 'ref.userId']),
        ];
    }
}
