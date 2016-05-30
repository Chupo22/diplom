<?
IncludeModuleLangFile(__FILE__);
$module_id = 'automated_testing_system';

use AutomatedTestingSystem\ORM\TestTable as Test;
use AutomatedTestingSystem\ORM\ExerciseTable as Exercise;
use AutomatedTestingSystem\ORM\UserExerciseTable as UserExercise;

if($APPLICATION->GetGroupRight($module_id)>'D') { //проверка уровня доступа к модулю
	$MODULE_PATH = $_SERVER['DOCUMENT_ROOT'].getLocalPath('modules/'.$module_id);
	
	
	
	$arTestsMenuItems = [];
	$arExercisesMenuItems = [];
	$dbExercises = Exercise::getList(['select' => ['id', 'testId', 'name']]);
	while($arExercise = $dbExercises->fetch()) {
		$arExercisesMenuItems[$arExercise['testId']][] = [
			'url'		=> $module_id.'_exercise.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arExercise['id']]),
			'text'		=> $arExercise['name'],
			'title'		=> $arExercise['name'],
		];
	}
	
	$dbTests = Test::getList(['select' => ['id', 'name']]);
	while($arTest = $dbTests->fetch()) {
		$arTestsMenuItems[] = [
			'url'		=> $module_id.'_exercises.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['id']]),
			'text'		=> $arTest['name'],
			'title'		=> $arTest['name'],
			'more_url'	=> [
				$module_id.'_test.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['id']]),
				$module_id.'_exercises.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['id']]),
			],
			'items' => $arExercisesMenuItems[$arTest['id']],
		];
	}
	
	$arUserMenuItems = [];
	$arUserExercisesMenuItems = [];
	$dbExercises = UserExercise::getList(['select' => ['*', 'number' => 'exercise.number', 'name' => 'exercise.name']]);
	while($arExercise = $dbExercises->fetch()) {
		$arUserExercisesMenuItems[$arExercise['userId']][] = [
			'url'		=> $module_id.'_user_exercise.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arExercise['id']]),
			'text'		=> $arExercise['name'],
			'title'		=> $arExercise['name'],
		];
	}
	
	
	$dbTests = CUser::getlist($by, $order, []);
	while($arTest = $dbTests->fetch()) {
		$name = $arTest['NAME'] || $arTest['LAST_NAME'] ? $arTest['NAME'].' '.$arTest['LAST_NAME'].' '.$arTest['SECOND_NAME'] : $arTest['EMAIL'];
		$name = trim($name);
		$name = str_replace('  ', ' ', $name);
		$arUserMenuItems[] = [
			'url'		=> $module_id.'_user_exercises.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['ID']]),
			'text'		=> $name,
			'title'		=> $name,
			'more_url'	=> [
			],
			'items' => $arExercisesMenuItems[$arTest['ID']],
		];
	}
	
	$aruserTestsMenuItems = [];
	$dbTests = Test::getList(['select' => ['id', 'name']]);
	while($arTest = $dbTests->fetch()) {
		$aruserTestsMenuItems[] = [
			'url'		=> $module_id.'_user_exercises.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['id']]),
			'text'		=> $arTest['name'],
			'title'		=> $arTest['name'],
			'more_url'	=> [
				$module_id.'_test.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['id']]),
				$module_id.'_exercises.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['id']]),
			],
			'items' => $arUserMenuItems,
		];
	}
	
	$aMenu = [
		'parent_menu'	=> 'global_menu_content',
		'sort'			=> 1000, //вес пункта меню
		'url'			=> $module_id.'_tests.php?'.http_build_query(['lang' => LANGUAGE_ID]), //ссылка на пункте меню
		'text'			=> 'Автоматизированная система проверки знаний', //текст пункта меню
		'title'			=> $module_id,	//текст всплывающей подсказки
		'items_id'		=> 'exercises', //идентификатор ветви
		'items'			=> [[
			'text'		=> 'Тесты',
			'title'		=> 'Тесты',
			'more_url'	=> [
			],
			'items' => $arTestsMenuItems,
		],[
//			'url'		=> $module_id.'_exercises.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['id']]),
			'text'		=> 'Результаты тестов',
			'title'		=> 'Результаты тестов',
			'more_url'	=> [
			],
			'items' => $aruserTestsMenuItems,
		]], 
		'more_url'		=> [
			$module_id.'_test.php',
		],
	];
	
	return $aMenu;
}
return false;
