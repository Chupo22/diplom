<?
$arUrlRewrite = array(
	array(
		"CONDITION" => "#^/tests/([^/]+)/(([^/]+)/)?(.*)#",
		"RULE" => "TEST_CODE=\$1&EXERCISE_NUMBER=\$3",
		"ID" => "",
		"PATH" => "/tests/index.php",
	),
);

?>
