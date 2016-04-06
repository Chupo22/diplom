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

use LearningDatabase\ORM\TestTable;

$dbRes = TestTable::getList([
	'select' => ['NAME', 'CODE']
]);
while($arItem = $dbRes->fetch()){
	$aMenuLinks[] = [
		$arItem['NAME'],
		str_replace('#TEST_CODE#', $arItem['CODE'], TEST_URL),
		[],
		[
			'FROM_IBLOCK' => true,
			'DEPTH_LEVEL' => 2
		],
		''
	];
}

