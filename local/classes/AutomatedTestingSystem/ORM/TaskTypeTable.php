<?
namespace AutomatedTestingSystem\ORM;

use Bitrix\Main\Entity;
use Helpers\ORM\IntegerField;

class TaskTypeTable extends Entity\DataManager
{
	const TYPE_FILTER = 'FILTER';
	const TYPE_SELECT = 'SELECT';
	
    public static function getTableName()
    {
        return 'orm_task_types';
    }
    
    public static function getMap()
    {
        return [
            new IntegerField(
				'id',
				[
					'primary' => true,
					'autocomplete' => true
				]
			),
            new Entity\StringField('name'),
            new Entity\StringField('code'),
        ];
    }
}
