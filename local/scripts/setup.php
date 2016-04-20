<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
use AutomatedTestingSystem\ORM\Setup;

Setup::resetDataBases();

$setup = new Setup();
$setup->setTests([
	['code' => 'select'				, 'name' => 'SELECT ... FROM ... WHERE',],
	['code' => 'select-functions'	, 'name' => 'SELECT AVG(...), MIN(...), MAX(...), COUNT(...), SUM(...)',],
	['code' => 'group-by'			, 'name' => 'GROUP BY',],
	['code' => 'join'				, 'name' => 'JOIN',],
	['code' => 'sub-queries'		, 'name' => 'sub queries',],
]);

$setup->setConditions([
	['code' => 'MORE',			'name' => 'Больше'],
	['code' => 'LESS',			'name' => 'Меньше'],
	['code' => 'EQUALLY',		'name' => 'Равно'],
	
	['code' => 'PARTIAL_MATCH',	'name' => 'Частичное совпадение'],
	['code' => 'FULL_MATCH',	'name' => 'Полное совпадение'],
]);

$setup->setExercises([
	'select' => [
		['TASKS' => [
			['table' => 'dept_emp', 'column' => 'emp_no', 'condition' => 'MORE',		'value' => '10014']
		]],
		['TASKS' => [
			['table' => 'dept_emp', 'column' => 'emp_no', 'condition' => 'LESS',		'value' => '10014']
		]],
		['TASKS' => [
			['table' => 'dept_emp', 'column' => 'emp_no', 'condition' => 'EQUALLY',	'value' => '10014']
		]],
		['TASKS' => [
			['table' => 'dept_emp', 'column' => 'emp_no', 'condition' => 'EQUALLY',	'value' => '']
		]],
		['TASKS' => [
			['table' => 'dept_emp', 'column' => 'emp_no', 'condition' => 'EQUALLY',	'value' => ''],
			['table' => 'dept_emp', 'column' => 'emp_no', 'condition' => 'LESS',	'value' => '']
		]],
		//['TASKS' => [
		//	['TABLE' => 'salaries', 'COLUMN' => '', 'CONDITION' => '',	'VALUE' => ''],
		//	['TABLE' => 'salaries', 'COLUMN' => '', 'CONDITION' => '',	'VALUE' => '']
		//]],
	]
]);

$setup->setUser();
