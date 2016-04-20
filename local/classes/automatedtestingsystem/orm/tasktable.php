<?
namespace AutomatedTestingSystem\ORM;

use Bitrix\Main\Entity;

class TaskTable extends Entity\DataManager
{
	
    public static function getTableName()
    {
        return 'orm_tasks';
    }
    
    public static function getMap()
    {
        return [
            new Entity\IntegerField('id', [
				'primary' => true,
				'autocomplete' => true
			]),
            new Entity\IntegerField('exerciseId'),
            new Entity\ReferenceField('exercise', get_class(new ExerciseTable), ['=this.exerciseId' => 'ref.id']),
            new Entity\ReferenceField('test', get_class(new TestTable), ['=this.exercise.testId' => 'ref.id']),
			
			
			
            new Entity\StringField('table'),
            new Entity\StringField('column'),
            new Entity\StringField('condition'),
            new Entity\StringField('value'),
        ];
    }
}
