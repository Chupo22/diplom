<?
namespace AutomatedTestingSystem\ORM;

use Bitrix\Main\Entity;
use Helpers\ORM\IntegerField;

class TaskTable extends Entity\DataManager
{
	
    public static function getTableName()
    {
        return 'orm_tasks';
    }
    
    public static function getMap()
    {
        return [
            new IntegerField('id', [
				'primary' => true,
				'autocomplete' => true
			]),
            new IntegerField('exerciseId'),
            new Entity\ReferenceField('exercise', get_class(new ExerciseTable), ['=this.exerciseId' => 'ref.id']),
            new Entity\ReferenceField('test', get_class(new TestTable), ['=this.exercise.testId' => 'ref.id']),
			
			
			
            new Entity\StringField('type'),
            new Entity\StringField('table'),
            new Entity\StringField('column'),
            new Entity\StringField('condition'),
            new Entity\StringField('value'),
        ];
    }
}
