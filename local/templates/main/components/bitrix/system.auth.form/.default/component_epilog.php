<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $USER;
$params = json_encode([
	'login' => $arResult['USER_LOGIN'],
	'isAuthorized' => $USER->isAuthorized(),
	'userName' => $arResult['USER_NAME']
]);
$APPLICATION->AddHeadString("<script>
	var params = params || {};
	params.authForm = JSON.parse('$params');
</script>",true);
$APPLICATION->AddHeadScript($this->__template->__folder.'/build/script.js');
