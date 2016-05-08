<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<?if($_POST['AUTH_FORM'] == 'Y' && $_POST['TYPE'] == 'AUTH'){
	LocalRedirect($APPLICATION->GetCurPageParam('', array()));
	die;
}?>
