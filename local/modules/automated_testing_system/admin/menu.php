<?
IncludeModuleLangFile(__FILE__);
$module_id = 'automated_testing_system';

use AutomatedTestingSystem\ORM\TestTable as Test;
use AutomatedTestingSystem\ORM\ExerciseTable as Exercise;

if($APPLICATION->GetGroupRight($module_id)>'D') { //проверка уровня доступа к модулю
	$MODULE_PATH = $_SERVER['DOCUMENT_ROOT'].getLocalPath('modules/'.$module_id);
	
	$aMenu = [
		'parent_menu'	=> 'global_menu_content', //поместим в раздел 'Сервис'
		'sort'			=> 1000, //вес пункта меню
		'url'			=> $module_id.'_tests.php?'.http_build_query(['lang' => LANGUAGE_ID]), //ссылка на пункте меню
		'text'			=> 'Тесты', //текст пункта меню
		'title'			=> $module_id,	//текст всплывающей подсказки
		'items_id'		=> 'exercises', //идентификатор ветви
		'items'			=> [], // остальные уровни меню сформируем ниже.
		'more_url'		=> [
			$module_id.'_test.php',
		],
	];
	
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
		$aMenu['items'][] = [
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
	
	return $aMenu;
}
return false;
