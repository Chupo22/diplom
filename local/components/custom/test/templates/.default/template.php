<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="test-container"></div>
<script>
	var params = JSON.parse(`<?=json_encode($arResult)?>`);
	components.initTest(params);
</script>

