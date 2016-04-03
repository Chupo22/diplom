<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
use LearningDatabase\ORM\Setup;

Setup::resetDataBases();

$setup = new Setup();
$setup->setTests([
	['CODE' => 'select'				, 'NAME' => 'SELECT ... FROM ... WHERE',],
	['CODE' => 'select-functions'	, 'NAME' => 'SELECT AVG(...), MIN(...), MAX(...), COUNT(...), SUM(...)',],
	['CODE' => 'group-by'			, 'NAME' => 'GROUP BY',],
	['CODE' => 'join'				, 'NAME' => 'JOIN',],
	['CODE' => 'sub-queries'		, 'NAME' => 'sub queries',],
]);

$setup->setConditions([
	['CODE' => 'MORE',			'NAME' => 'Больше'],
	['CODE' => 'LESS',			'NAME' => 'Меньше'],
	['CODE' => 'EQUALLY',		'NAME' => 'Равно'],
	
	['CODE' => 'PARTIAL_MATCH',	'NAME' => 'Частичное совпадение'],
	['CODE' => 'FULL_MATCH',	'NAME' => 'Полное совпадение'],
]);

$setup->setExercises([
	'select' => [
		['TASKS' => [
			['TABLE' => 'dept_emp', 'COLUMN' => 'emp_no', 'CONDITION' => 'MORE',		'VALUE' => '10014']
		]],
		['TASKS' => [
			['TABLE' => 'dept_emp', 'COLUMN' => 'emp_no', 'CONDITION' => 'LESS',		'VALUE' => '10014']
		]],
		['TASKS' => [
			['TABLE' => 'dept_emp', 'COLUMN' => 'emp_no', 'CONDITION' => 'EQUALLY',	'VALUE' => '10014']
		]],
		['TASKS' => [
			['TABLE' => 'dept_emp', 'COLUMN' => 'emp_no', 'CONDITION' => 'EQUALLY',	'VALUE' => '']
		]],
		['TASKS' => [
			['TABLE' => 'dept_emp', 'COLUMN' => 'emp_no', 'CONDITION' => 'EQUALLY',	'VALUE' => ''],
			['TABLE' => 'dept_emp', 'COLUMN' => 'emp_no', 'CONDITION' => 'LESS',	'VALUE' => '']
		]],
		//['TASKS' => [
		//	['TABLE' => 'salaries', 'COLUMN' => '', 'CONDITION' => '',	'VALUE' => ''],
		//	['TABLE' => 'salaries', 'COLUMN' => '', 'CONDITION' => '',	'VALUE' => '']
		//]],
	]
]);

