<?$aMenuLinks = [
	[
		'Tests',
		TESTS_URL,
		
		[
			'FROM_IBLOCK' => true,
			'IS_PARENT' => 1,
			'DEPTH_LEVEL' => 1
		],
		''
	]
];

use AutomatedTestingSystem\ORM\TestTable;

$dbRes = TestTable::getList([
	'select' => ['name', 'code']
]);
while($arItem = $dbRes->fetch()){
	$aMenuLinks[] = [
		$arItem['name'],
		str_replace('#TEST_CODE#', $arItem['code'], TEST_URL),
		[],
		[
			'FROM_IBLOCK' => true,
			'DEPTH_LEVEL' => 2
		],
		''
	];
}

