<?
namespace LearningDatabase\ORM;

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
            new Entity\IntegerField('ID', [
				'primary' => true,
				'autocomplete' => true
			]),
            new Entity\IntegerField('EXERCISE_ID'),
            new Entity\ReferenceField('EXERCISE', get_class(new ExerciseTable), ['=this.EXERCISE_ID' => 'ref.ID']),
            new Entity\ReferenceField('TEST', get_class(new TestTable), ['=this.EXERCISE.TEST_ID' => 'ref.ID']),
			
			
			
            new Entity\StringField('TABLE'),
            new Entity\StringField('COLUMN'),
            new Entity\StringField('CONDITION'),
            new Entity\StringField('VALUE'),
        ];
    }
}
