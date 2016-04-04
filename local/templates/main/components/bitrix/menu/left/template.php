<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
foreach ( $arResult as $key => $arItem ) {
    if ( $arItem["DEPTH_LEVEL"] == 1 ) {
        $prev1 = $key;
    } else {
        if ( $arItem["DEPTH_LEVEL"] == 2 ) {
            $prev2 = $key;
            $arResult[$prev1]["ITEMS"][$key] = $arItem;
            if ( $arItem["SELECTED"] ) {
                $arResult[$prev1]["SELECTED"] = true;
            }
        } elseif ( $arItem["DEPTH_LEVEL"] == 3 ) {
            $prev3 = $key;
            $arResult[$prev1]["ITEMS"][$prev2]["ITEMS"][$key] = $arItem;
            if ( $arItem["SELECTED"] ) {
                $arResult[$prev1]["ITEMS"][$prev2]["SELECTED"] = true;
            }
        } elseif ( $arItem["DEPTH_LEVEL"] == 4 ) {
            $prev4 = $key;
            $arResult[$prev1]["ITEMS"][$prev2]["ITEMS"][$prev3]["ITEMS"][$key] = $arItem;
            if ( $arItem["SELECTED"] ) {
                $arResult[$prev1]["ITEMS"][$prev2]["ITEMS"][$prev3]["SELECTED"] = true;
            }
        } elseif ( $arItem["DEPTH_LEVEL"] == 5 ) {
            $prev5 = $key;
            $arResult[$prev1]["ITEMS"][$prev2]["ITEMS"][$prev3]["ITEMS"][$prev4]["ITEMS"][$key] = $arItem;
            if ( $arItem["SELECTED"] ) {
                $arResult[$prev1]["ITEMS"][$prev2]["ITEMS"][$prev3]["ITEMS"][$prev4]["SELECTED"] = true;
            }
        } elseif ( $arItem["DEPTH_LEVEL"] == 6 ) {
            $arResult[$prev1]["ITEMS"][$prev2]["ITEMS"][$prev3]["ITEMS"][$prev4]["ITEMS"][$prev5]["ITEMS"][$key] = $arItem;
            if ( $arItem["SELECTED"] ) {
                $arResult[$prev1]["ITEMS"][$prev2]["ITEMS"][$prev3]["ITEMS"][$prev4]["ITEMS"][$prev5]["SELECTED"] = true;
            }
        }
        unset($arResult[$key]);
    }
}
?>
<section class="sidebar">
	<ul>
		<?foreach($arResult as $arItem):?>
			<li><?=$arItem['TEXT']?>
				<ul>
					<?foreach($arItem['ITEMS'] as $arSubItem):?>
						<li><a href="<?=$arSubItem['LINK']?>"><?=$arSubItem['TEXT']?></a></li>
					<?endforeach?>
				</ul>
			</li>
		<?endforeach?>
	</ul>
</section>
