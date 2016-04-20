<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="test-container"></div>
<?//$APPLICATION->RestartBuffer();\CPL::pr($arResult);die;?>
<?//$APPLICATION->RestartBuffer();\CPL::pr(json_encode($arResult));die;?>
<script>
	var params = JSON.parse(`<?=json_encode($arResult)?>`);
	//var params = JSON.parse(`<?//=json_encode(['test' => '"query":"\"function($value){\\n\\t\\t\\t\\t\\treturn htmlspecialchars_decode($value);\\n\\t\\t\\t\\t}\""'])?>//`);
	components.initTest(params);
</script>

