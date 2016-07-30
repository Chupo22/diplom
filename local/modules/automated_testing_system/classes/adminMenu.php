<?namespace ATSModule;

use AutomatedTestingSystem\ORM\TestTable as Test;
use AutomatedTestingSystem\ORM\ExerciseTable as Exercise;
use AutomatedTestingSystem\ORM\UserExerciseTable as UserExercise;
use Bitrix\Main\UserTable as User;

class AdminMenu{
//	var $class;
//	function __construct($class, $arSort = ['id' => 'asc']) {
//		$this->class = $class;
//		$sortBy = reset(array_keys($arSort));
//		$sortOrder = reset(array_values($arSort));
//		parent::CAdminList(ADMIN_MODULE_NAME, (new \CAdminSorting(ADMIN_MODULE_NAME, $sortBy, $sortOrder)));
//	}
	public static function getMenu() {
		//todo-sem выборки нужно переделать и оптимизировать
		$arTestsMenuItems = [];
		$arExercisesMenuItems = [];
		$dbExercises = Exercise::getList(['select' => ['id', 'testId', 'name']]);
		while($arExercise = $dbExercises->fetch()) {
			$arExercisesMenuItems[$arExercise['testId']][] = [
				'url'		=> Info::$MODULE_ID.'_exercise.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arExercise['id']]),
				'text'		=> $arExercise['name'],
				'title'		=> $arExercise['name'],
			];
		}
		
		$dbTests = Test::getList(['select' => ['id', 'name']]);
		while($arTest = $dbTests->fetch()) {
			$arTestsMenuItems[] = [
				'url'		=> Info::$MODULE_ID.'_exercises.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['id']]),
				'text'		=> $arTest['name'],
				'title'		=> $arTest['name'],
				'more_url'	=> [
					Info::$MODULE_ID.'_test.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['id']]),
					Info::$MODULE_ID.'_exercises.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['id']]),
				],
				'items' => $arExercisesMenuItems[$arTest['id']],
			];
		}
		
		$arUserMenuItems = [];
		$arUserExercisesMenuItems = [];
		$dbExercises = UserExercise::getList(['select' => ['*', 'number' => 'exercise.number', 'name' => 'exercise.name']]);
		while($arExercise = $dbExercises->fetch()) {
			$arUserExercisesMenuItems[$arExercise['userId']][] = [
				'url'		=> Info::$MODULE_ID.'_user_exercise.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arExercise['id']]),
				'text'		=> $arExercise['name'],
				'title'		=> $arExercise['name'],
			];
		}
			
		$dbUsers = User::getList(['select' => [
			
		]]);
		while($arUser = $dbUsers->fetch()) {
			$name = $arUser['NAME'] || $arUser['LAST_NAME'] ? $arUser['NAME'].' '.$arUser['LAST_NAME'].' '.$arUser['SECOND_NAME'] : $arUser['EMAIL'];
			$name = trim($name);
			$name = str_replace('  ', ' ', $name);
			$arUserMenuItems[] = [
				'url'		=> Info::$MODULE_ID.'_user_exercises.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arUser['ID']]),
				'text'		=> $name,
				'title'		=> $name,
				'more_url'	=> [
				],
				'items' => $arExercisesMenuItems[$arUser['ID']],
			];
		}
		
		$aruserTestsMenuItems = [];
		$dbTests = Test::getList(['select' => ['id', 'name']]);
		while($arTest = $dbTests->fetch()) {
			$aruserTestsMenuItems[] = [
				'url'		=> Info::$MODULE_ID.'_user_exercises.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['id']]),
				'text'		=> $arTest['name'],
				'title'		=> $arTest['name'],
				'more_url'	=> [
					Info::$MODULE_ID.'_test.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['id']]),
					Info::$MODULE_ID.'_exercises.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['id']]),
				],
				'items' => $arUserMenuItems,
			];
		}
		
		$aMenu = [
			'parent_menu'	=> 'global_menu_content',
			'sort'			=> 1000, //вес пункта меню
			'url'			=> Info::$MODULE_ID.'_tests.php?'.http_build_query(['lang' => LANGUAGE_ID]), //ссылка на пункте меню
			'text'			=> 'Автоматизированная система проверки знаний', //текст пункта меню
			'title'			=> Info::$MODULE_ID,	//текст всплывающей подсказки
			'items_id'		=> 'exercises', //идентификатор ветви
			'items'			=> [[
				'text'		=> 'Тесты',
				'title'		=> 'Тесты',
				'more_url'	=> [
				],
				'items' => $arTestsMenuItems,
			],[
	//			'url'		=> Info::$MODULE_ID.'_exercises.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['id']]),
				'text'		=> 'Результаты тестов',
				'title'		=> 'Результаты тестов',
				'more_url'	=> [
				],
				'items' => $aruserTestsMenuItems,
			]], 
			'more_url'		=> [
				Info::$MODULE_ID.'_test.php',
			],
		];
		
		return $aMenu;
	}
//	public function prepareList();
}
