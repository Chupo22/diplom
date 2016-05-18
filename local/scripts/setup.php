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

$setup->setTaskTypes([
	['code' => 'FILTER',		'name' => 'Фильтрация'],
	['code' => 'SELECT',		'name' => 'Получение колонок'],
]);

$setup->setExercises([
	'select' => [
		['name' => 'Все значения статичны, условие "больше"',				'tasks' => [['type' => 'FILTER', 'table' => 'dept_emp',	'column' => 'emp_no',	'condition' => 'MORE',		'value' => '10014']]],
		['name' => 'Все значения статичны, условие "меньше"',				'tasks' => [['type' => 'FILTER', 'table' => 'dept_emp',	'column' => 'emp_no',	'condition' => 'LESS',		'value' => '10014']]],
		['name' => 'Все значения статичны, условие "равно"',				'tasks' => [['type' => 'FILTER', 'table' => 'dept_emp',	'column' => 'emp_no',	'condition' => 'EQUALLY',	'value' => '10014']]],
		['name' => 'Все значения статичны, условие "частичное совпадение"',	'tasks' => [['type' => 'FILTER', 'table' => 'employees','column' => 'last_name','condition' => 'LIKE',		'value' => 'oic']]],
		['name' => 'Все значения статичны, условие "Входит в"',				'tasks' => [['type' => 'FILTER', 'table' => 'dept_emp',	'column' => 'emp_no',	'condition' => 'IN',		'value' => '10014,10015,10016,10020']]],
		['name' => 'Все значения статичны, условие "между"',				'tasks' => [['type' => 'FILTER', 'table' => 'dept_emp',	'column' => 'emp_no',	'condition' => 'BETWEEN',	'value' => '10014,10020']]],
		['name' => 'условие "частичное совпадение" , случайное значение',	'tasks' => [['type' => 'FILTER', 'table' => 'employees','column' => 'last_name','condition' => 'LIKE',		'value' => '']]],
		['name' => 'Случайное значение',									'tasks' => [['type' => 'FILTER', 'table' => 'dept_emp',	'column' => 'emp_no',	'condition' => 'EQUALLY',	'value' => '']]],
		['name' => 'Случайное значение, условие',							'tasks' => [['type' => 'FILTER', 'table' => 'dept_emp',	'column' => 'emp_no',	'condition' => '',			'value' => '']]],
		['name' => 'Случайное значение, условие, поле',						'tasks' => [['type' => 'FILTER', 'table' => 'dept_emp',	'column' => '',			'condition' => '',			'value' => '']]],
		['name' => 'Случайное значение, условие, поле, таблица',			'tasks' => [['type' => 'FILTER', 'table' => '',			'column' => '',			'condition' => '',			'value' => '']]],
		['name' => 'Несколько условий',
			'tasks' => [
				['type' => 'FILTER', 'table' => 'dept_emp', 'column' => 'emp_no',	'condition' => 'LESS',	'value' => ''],
				['type' => 'SELECT', 'table' => 'dept_emp', 'column' => 'dept_no',	'condition' => '',		'value' => ''],
				['type' => 'SELECT', 'table' => 'dept_emp', 'column' => 'from_date','condition' => '',		'value' => ''],
			]
		],
	]
]);

$setup->setUser();

