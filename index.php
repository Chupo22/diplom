<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Главная");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle("Главная страница");
?>

<?$APPLICATION->IncludeComponent('custom:exercise', '',array(),false);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
