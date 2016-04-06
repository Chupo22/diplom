<?
IncludeModuleLangFile(__FILE__);
$module_id = 'automated_testing_system';

use LearningDatabase\ORM\TestTable as Test;
use LearningDatabase\ORM\ExerciseTable as Exercise;

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
	$dbExercises = Exercise::getList(['select' => ['ID', 'TEST_ID', 'NAME']]);
	while($arExercise = $dbExercises->fetch()) {
		
		$arExercisesMenuItems[$arExercise['TEST_ID']][] = [
			'url'		=> $module_id.'_exercise.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arExercise['ID']]),
			'text'		=> $arExercise['NAME'],
			'title'		=> $arExercise['NAME'],
		];
	}
	
	$dbTests = Test::getList(['select' => ['ID', 'NAME']]);
	while($arTest = $dbTests->fetch()) {
		$aMenu['items'][] = [
			'url'		=> $module_id.'_exercises.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['ID']]),
			'text'		=> $arTest['NAME'],
			'title'		=> $arTest['NAME'],
			'more_url'	=> [
				$module_id.'_test.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['ID']]),
				$module_id.'_exercises.php?'.http_build_query(['lang' => LANGUAGE_ID, 'id' => $arTest['ID']]),
			],
			'items' => $arExercisesMenuItems[$arTest['ID']],
		];
	}
	
	return $aMenu;
}
return false;
