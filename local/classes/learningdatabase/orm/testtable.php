<?
namespace LearningDatabase\ORM;

use Bitrix\Main\Entity;

class TestTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'orm_tests';
    }
    
    public static function getMap()
    {
        return [
            new Entity\IntegerField('ID', [
				'primary' => true,
				'autocomplete' => true
			]),
            new Entity\StringField('NAME'),
        ];
    }
}
