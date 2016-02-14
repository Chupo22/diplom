<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?global $USER;?>
<div id="auth-form-container" style="float: right;"></div>




<?/*if($arResult["FORM_TYPE"] == "login"):?>
	<form id="auth-form" class="navbar-form navbar-left" action="" method="post">
		<div class="form-group animated">
			<input name="login" type="hidden" value="yes"/>
			<input name="backurl" type="hidden" value="<?$APPLICATION->GetCurPage()?>"/>
			<input name="AUTH_FORM" type="hidden" value="Y"/>
			<input name="TYPE" type="hidden" value="AUTH"/>
			<input name="USER_LOGIN" class="form-control" type="text" value="<?=$arResult["USER_LOGIN"]?>" placeholder="login"/>
			<input name="USER_PASSWORD" class="form-control" type="password" autocomplete="off" placeholder="password"/>
			<button type="button" id="btn-close-auth" class="btn btn-default">close</button>
		</div>
		<button type="submit" id="btn-login" class="btn btn-default">login</button>
		<br>
		<label><input name="USER_REMEMBER" class="form-control" type="checkbox" value="Y"/>Запомнить меня</label>
	</form>
<?else:?>
	<form action="">
		<?=$arResult["USER_NAME"]?><br/>
		[<?=$arResult["USER_LOGIN"]?>]<br/>
		<a href="<?=$arResult["PROFILE_URL"]?>"
		   title="<?=GetMessage("AUTH_PROFILE")?>"><?=GetMessage("AUTH_PROFILE")?>
	   </a>
	<br>
		<? foreach($arResult["GET"] as $key => $value):?>
			<input type="hidden" name="<?=$key?>" value="<?=$value?>"/>
		<? endforeach ?>
		<input type="hidden" name="logout" value="yes"/>
		<input type="submit" name="logout_butt" value="<?=GetMessage("AUTH_LOGOUT_BUTTON")?>"/>
	</form>
<?endif?>
