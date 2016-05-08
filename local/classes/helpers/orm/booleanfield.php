<?namespace Helpers\ORM;

use Bitrix\Main\Entity;

class BooleanField extends Entity\BooleanField{
	function __construct($name, array $parameters = []) {
		$parameters['fetch_data_modification'] = function(){
			return [function($value){
				return (bool)$value;
			}];
		};
		parent::__construct($name, $parameters);
	}
}
