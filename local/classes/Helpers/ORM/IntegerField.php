<?namespace Helpers\ORM;

use Bitrix\Main\Entity;

class IntegerField extends Entity\IntegerField{
	function __construct($name, array $parameters = []) {
		$parameters['fetch_data_modification'] = function(){
			return [function($value){
				return (int)$value;
			}];
		};
		parent::__construct($name, $parameters);
	}
}
