<? use Bitrix\Main\Page\Asset;
use Helpers\Tools;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? /** @var Bitrix\Main\Application|CMain $APPLICATION */?>

<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<?
		$APPLICATION->ShowHead();
		$APPLICATION->ShowPanel();
		$asset = Asset::getInstance();
		$asset->addJs(Tools::getTemplateAsset('js'));
		$asset->addCss(Tools::getTemplateAsset('css'));
//		$APPLICATION->SetAdditionalCSS('/node_modules/highlight.js/styles/default.css');
		?>
		<title><?$APPLICATION->ShowTitle()?></title>
	</head>
	<body>
		<div class="site w100">
			<div class="header posRel">
				<div class="main_width">
					<a href="/" class="logo">Logotip</a>
					<div class="nav">
						<ul>
							<li><a href="">Documentation</a></li>
							<li><a href="">Laracasts</a></li>
							<li><a href="">Lumen</a></li>
							<li><a class="dropdown">Services<span></span></a>
								<ul>
									<li><a href="">Forge</a></li>
									<li><a href="">Forge</a></li>
								</ul>
							</li>
							<li><a href="">Conference</a></li>
							<li><a href="">Community</a></li>
						</ul>
						<?$APPLICATION->IncludeComponent('bitrix:system.auth.form', '',array(),false);?>
					</div>
				</div>
			</div>
			<div class="page">
				<div class="main_width">
					<?$APPLICATION->IncludeComponent(
						"bitrix:menu", 
						"left", 
						array(
							"COMPONENT_TEMPLATE" => "left",
							"ROOT_MENU_TYPE" => "left",
							"MENU_CACHE_TYPE" => "N",
							"MENU_CACHE_TIME" => "3600",
							"MENU_CACHE_USE_GROUPS" => "Y",
							"MENU_CACHE_GET_VARS" => array(
							),
							"MAX_LEVEL" => "1",
							"CHILD_MENU_TYPE" => "left",
							"USE_EXT" => "Y",
							"DELAY" => "N",
							"ALLOW_MULTI_SELECT" => "N"
						),
						false
					);?>
					<div id="content">
