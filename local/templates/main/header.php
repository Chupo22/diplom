<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<?
		$APPLICATION->ShowHead();
		$APPLICATION->ShowPanel();
		
		$APPLICATION->SetAdditionalCSS('/local/src/css/bootstrap.min.css');
		$APPLICATION->SetAdditionalCSS('/local/src/css/bootstrap-theme.min.css');
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/font.SourceSansProRegular.css');
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/inline.images.css');
		$APPLICATION->SetAdditionalCSS('/local/src/css/animate.css');
		
		$APPLICATION->AddHeadScript('/local/src/js/jquery-2.2.0.min.js');
		$APPLICATION->AddHeadScript('/local/src/js/react.min.js');
		$APPLICATION->AddHeadScript('/local/src/js/react-dom.min.js');
		$APPLICATION->AddHeadScript('/local/src/js/react-kostil.js');

		?>
		<script type="text/javascript">
			//$(function(){
			//	$(".dropdown").click(function() {
			//		$(this).parent().toggleClass("open");
			//	 })
			//});
		</script>
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
					<?$APPLICATION->IncludeComponent('bitrix:menu', 'left',array(),false);?>
					<div id="content">
