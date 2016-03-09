<?
namespace LearningDatabase\ORM;

use Bitrix\Main\Entity;

class ExerciseTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'orm_exercises';
    }
    
    public static function getMap()
    {
        return [
            new Entity\IntegerField('ID', [
				'primary' => true,
				'autocomplete' => true
			]),
            new Entity\IntegerField('NUMBER',['required' => true]),
            new Entity\StringField('NAME'),
            new Entity\IntegerField('TEST_ID', ['required' => true]),
            new Entity\ReferenceField('TEST', 'LearningDatabase\ORM\Test', ['=this.TEST_ID' => 'ref.ID']),
        ];
    }
}
