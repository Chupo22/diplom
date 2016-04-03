<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); ?>

<?$APPLICATION->IncludeComponent('custom:test', '',[
	'TEST_CODE' => $_REQUEST['TEST_CODE'],
	'EXERCISE_NUMBER' => $_REQUEST['EXERCISE_NUMBER'],
	'USER_QUERY' => $_REQUEST['USER_QUERY'],
],false);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
