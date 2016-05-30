<?namespace ATSModule;

use AutomatedTestingSystem\ORM\TestTable as Test;
use AutomatedTestingSystem\ORM\ExerciseTable as Exercise;

class CAdminList extends \CAdminList{
	/** @var Test | Exercise $class */
	var $class;
	function __construct($class, $arSort = ['id' => 'asc']) {
		$this->class = $class;
		$sortBy = reset(array_keys($arSort));
		$sortOrder = reset(array_values($arSort));
		parent::CAdminList(ADMIN_MODULE_NAME, (new \CAdminSorting(ADMIN_MODULE_NAME, $sortBy, $sortOrder)));
	}
	public function AddHeaders() {
		$class = $this->class;
		$arHeaders = [];
		foreach($class::getEntity()->getFields() as $obField) {
			if(!method_exists($obField, 'isRequired'))
				continue;
			
			$fieldName = $obField->getName();
			$arHeaders[] = ['id' => $fieldName, 'content' => $fieldName, 'sort' => $fieldName, 'default' => true];
		}
		parent::AddHeaders($arHeaders);
	}
//	public function prepareList();
}
