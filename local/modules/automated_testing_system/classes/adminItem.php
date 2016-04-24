<?namespace ATSModule;

use ATSModule\Tools as ModuleTools;
use LearningDatabase\ORM\TestTable as Test;
use LearningDatabase\ORM\ExerciseTable as Exercise;
use Bitrix\Main\Entity;

class CAdminItem{
	/** @var Test | Exercise $class */
	var $class;
	var $arItem;
	var $arErrors;
	var $arNotes;
	function __construct($class){
		$this->class = $class;
		$this->arErrors = [];
		$this->arNotes = [];
	}
	public function getItem($id){
		$class = $this->class;
		$this->arItem = $class::getList([
			'filter' => ['ID' => $id],
			'limit' => 1,
		])->fetch();
		if(!$this->arItem)
			$this->arErrors[] = ModuleTools::GetMessage('ITEM_NOT_FOUND') ?: 'Item not Found.';
	}
	public function getFields(){
		$class = $this->class;
		$arFields = [];
		foreach($class::getEntity()->getFields() as $obField){
			if(!method_exists($obField, 'isRequired'))
				continue;
			
			/** @var Entity\IntegerField | Entity\StringField | Entity\BooleanField | Entity\TextField $obField */
			$fieldName = $obField->getName();
			$arFields[] = [
				'name' => $fieldName,
				'can_edit' => !$obField->isAutocomplete(),
				'required' => $obField->isRequired(),
				'value' => isset($_REQUEST['data'][$fieldName]) ? $_REQUEST['data'][$fieldName] : $this->arItem[$fieldName],
			];
		}
		return $arFields;
	}
}
