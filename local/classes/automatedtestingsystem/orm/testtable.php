<?
namespace AutomatedTestingSystem\ORM;

use Bitrix\Main\Entity;
use Helpers\ORM\IntegerField;

class TestTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'orm_tests';
    }
    
    public static function getMap()
    {
        return [
            new IntegerField('id', [
				'primary' => true,
				'autocomplete' => true
			]),
            new Entity\StringField('name'),
            new Entity\StringField('code', [
				'required' => true,
				'unique' => true
			]),
        ];
    }
}
