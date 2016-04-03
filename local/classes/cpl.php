<?
class CPL extends CAllPL
{
	/**
	 *	Генерирует случайную строку из букв и цифр заданной длины
	 * @param int $length длина

	 * @return string
	 */
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	
	/**
	 *	Генерирует случайную строку из цифр заданной длины
	 * @param int $length длина

	 * @return string
	 */
	function generateRandomNumericString($length = 10) {
		$characters = '0123456789';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	
	/**
	 * Склонение русских слов после порядковых числительных
	 * echo 'Мне уже '.declension('<b>20</b>','год года лет').'!';
	 * выведет «Мне уже <b>20</b> лет!»
	 *
	 * @param string/int $digit число
	 * @param string $expr склоняемые слова
	 * @param boolean $onlyword возвращать только склонение
	 * @return string
	 */
	function declension($digit,$expr,$onlyword=false)
	{
		if(!is_array($expr))
			$expr = array_filter(explode(' ', $expr));
		if(empty($expr[2]))
			$expr[2]=$expr[1];
		$i=preg_replace('/[^0-9]+/s','',$digit)%100; //intval не всегда корректно работает
		if($onlyword) $digit='';
		if($i>=5 && $i<=20)
			$res=$digit.' '.$expr[2];
		else
		{
			$i%=10;
			if($i==1) $res=$digit.' '.$expr[0];
			elseif($i>=2 && $i<=4) $res=$digit.' '.$expr[1];
			else $res=$digit.' '.$expr[2];
		}
		return trim($res);
	}

	/**
	 * Возвращает дамп переменной или объекта
	 *
	 * @param mixed $var Переменная
	 * @param string $name ?
	 */
	static function pr($var, $name = '') {

		//if ($_SERVER['REMOTE_ADDR'] != '87.236.29.54') return;
		//if (!$USER->IsAdmin)
		//	return;
		echo '<pre>';
		$type = gettype($var);

		$backtrace = debug_backtrace();
		if (isset($backtrace[1]['function']) && $backtrace[1]['function'] == 'pred') {
			$caller = array_shift($backtrace);
		}
		$caller = array_shift($backtrace);
		echo $caller['file'].', '.$caller['line']."\n";

		echo (is_numeric($name) || $name ? $name : '').'('.$type.') = ';
		if ($type == 'boolean') {
			echo ($var === true) ? 'true' : 'false';
		}
		elseif (empty($var)) {
			$arr = array('string', 'array', 'object', 'resource');
			$var = ($type == 'NULL') ? 'NULL' : $var;
			$var = (in_array($type, $arr)) ? 'empty!' : $var;
			echo $var;
		}
		else {
			($type == 'object') ? var_dump($var) : print_r($var);
		}
		echo '</pre>';
	}


	/**
	 * Вывод изображения
	 *
	 * @param int $_imgId ID картинки
	 * @param boolean $_saveImg Сохранять ли изображение в файл
	 * @param int $_width Максимальная ширина изображения
	 * @param int $_height Максимальная высота изображения
	 * @param string $_str надпись на картинке
	 * @param string $_addon дополнительные параметры тега <img>
	 * @param boolean $_showHtml возвращать html-код
	 * @param boolean $_tux вхюхать изображение в размер
	 * @return string
	 */
	function showPreviewImageFromFile($_imgId, $_saveImg, $_width, $_height, $_str, $_addon, $_showHtml, $_tux, $RGB = array())
	{
		$tmp = getimagesize($_SERVER["DOCUMENT_ROOT"].$_imgId);
		//CPL::pr(getimagesize($_SERVER["DOCUMENT_ROOT"].$_imgId));
		//exit;
		$arImg = array(
				"WIDTH" => $tmp[0],
				"HEIGHT" => $tmp[1],
				"CONTENT_TYPE" => $tmp["mime"],
				"FILE_NAME" => basename($_SERVER["DOCUMENT_ROOT"].$_imgId),
				"SUBDIR" => "fromfile",
				"SRC" =>$_imgId
			);
		$_width=intval($_width); //ширина
		$_height=intval($_height); //высота
		$_saveImg = strToUpper($_saveImg)!="Y"? "N": "Y"; //Сохранить изображение или выдать его на лету ? тест
		//$arImg=CFile::GetFileArray($_imgId);
		$is_image = CFile::IsImage($arImg["FILE_NAME"], $arImg["CONTENT_TYPE"]);
		//CPL::pr($is_image);
		//exit;
		if ($is_image)
		{
			$arResultImg = array();
			if(is_file($_SERVER["DOCUMENT_ROOT"]."/upload/PREVIEW/".$_width."x".$_height."/".$arImg["SUBDIR"]."/".$arImg["FILE_NAME"]))
			{
				//Картинка уже есть!
				$arResultImg["IMPATCH"]="/upload/PREVIEW/".$_width."x".$_height."/".$arImg["SUBDIR"]."/".$arImg["FILE_NAME"];
				$arResultImg["SIZE"]=array();
				$arResultImg["SIZE"] = @getimagesize($_SERVER["DOCUMENT_ROOT"].$arResultImg["IMPATCH"]);
				$arResultImg["ADDON"] = " ".trim($_addon)." ";

				//return "/upload/PREVIEW/".$_width."x".$_height."/".$arImg["SUBDIR"]."/".$arImg["FILE_NAME"];
			}
			else
			{
				//Картинки нет
				$newImageFile=$_SERVER["DOCUMENT_ROOT"]."/upload/PREVIEW/".$_width."x".$_height."/".$arImg["SUBDIR"]."/".$arImg["FILE_NAME"];

				$arResize = Array (
					"METHOD" => "resample",
					"COMPRESSION" => "100",
					"WIDTH" => $_width,
					"HEIGHT" => $_height,
				);
				$arFile = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$arImg["SRC"]);
	//---------------------Создаю катринку------------------------------------------------
				$file = $arFile["tmp_name"];
				if(file_exists($file) and is_file($file))
				{
					$width = intval($arResize["WIDTH"]);
					$height = intval($arResize["HEIGHT"]);

					$orig = @getimagesize($file);
					$img_width = $widht;
					$img_height = $height;
					if(is_array($orig))
					{

						if(($width > 0 && $orig[0] > $width) || ($height > 0 && $orig[1] > $height))
						{

							$width_orig = $orig[0];
							$height_orig = $orig[1];

							if($width <= 0)
							{
								$width = $width_orig;
							}

							if($height <= 0)
							{
								$height = $height_orig;
							}

							$height_new = $height_orig;
							if($width_orig > $width)
							{
								$height_new = ($width / $width_orig) * $height_orig;
								$height_new = ($width / $width_orig) * $height_orig;
							}

							if($height_new > $height)
							{
								$width = ($height / $height_orig) * $width_orig;
							}
							else
							{
								$height = $height_new;
							}
///echo 1111111111111111111111;
						}
						else
						{
							$width = $orig[0];
							$height = $orig[1];
							$width_orig = $orig[0];
							$height_orig = $orig[1];
						}
							$image_type = $orig[2];

							return false;
							if($image_type == IMAGETYPE_JPEG)
							{
								$image = imagecreatefromjpeg($file);
							}
							elseif($image_type == IMAGETYPE_GIF)
							{
								$image = imagecreatefromgif($file);
							}
							elseif($image_type == IMAGETYPE_PNG)
							{
								$image = @imagecreatefrompng($file);
								imagealphablending($image, true);
								imagesavealpha($image, true);
							}
							else
								return;

							if ($_tux == true) {
								$image_p = imagecreatetruecolor($_width, $_height);
								if(count($RGB))
									
									$white = imagecolorallocate($image_p, $RGB[0], $RGB[1], $RGB[2]);
								else
									$white = imagecolorallocate($image_p, 255, 255, 255);
								imagefill($image_p, 0, 0, $white);
								$x = ($_width - $width) / 2;
								$y = ($_height - $height) / 2;
							} else {
								$image_p = imagecreatetruecolor($width, $height);
								$x=0;
								$y=0;
							}
							if($image_type == IMAGETYPE_JPEG)
							{
								if($arResize["METHOD"] === "resample"){
														$arResultImg["SIZE2"][]=$width;
							//$arResultImg["SIZE2"][]=$height;
							//$arResultImg["SIZE3"][]=$width_orig;
							//$arResultImg["SIZE3"][]=$height_orig;
								//CPL::pr($img_width);
								//CPL::pr($img_height);
									imagecopyresampled($image_p, $image, $x, $y, 0, 0, $width, $height, $width_orig, $height_orig);
								}else
									imagecopyresized($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
								if (strlen($_str)>0){
									// определяем координаты вывода текста
									$size = 2; // размер шрифта
									$str=$_str;
									$x_text = $width-imagefontwidth($size)*strlen($str)-3;
									$y_text = $height-imagefontheight($size)-3;

									// определяем каким цветом на каком фоне выводить текст
									$white = imagecolorallocatealpha($image_p, 255, 255, 255, 50);
									$black = imagecolorallocatealpha($image_p, 0, 0, 0, 90);
									imagestring($image_p, $size, $x_text-1, $y_text-1, $str, $black);
									imagestring($image_p, $size, $x_text+1, $y_text+1, $str, $black);
									imagestring($image_p, $size, $x_text+1, $y_text-1, $str, $black);
									imagestring($image_p, $size, $x_text-1, $y_text+1, $str, $black);
									imagestring($image_p, $size, $x_text-1, $y_text,   $str, $black);
									imagestring($image_p, $size, $x_text+1, $y_text,   $str, $black);
									imagestring($image_p, $size, $x_text,   $y_text-1, $str, $black);
									imagestring($image_p, $size, $x_text,   $y_text+1, $str, $black);
									imagestring($image_p, $size, $x_text, $y_text, $str, $white);
									unset ($str);
								}
								if($arResize["COMPRESSION"] > 0)
								{
									CheckDirPath(GetDirPath($newImageFile),true);
									imagejpeg($image_p, $newImageFile, $arResize["COMPRESSION"]);
								}
								else
								{
									CheckDirPath(GetDirPath($newImageFile),true);
									imagejpeg($image_p, $newImageFile);
								}
							}
							elseif($image_type == IMAGETYPE_GIF && function_exists("imagegif"))
							{
								imagetruecolortopalette($image_p, true, imagecolorstotal($image));
								imagepalettecopy($image_p, $image);

								//Save transparency for GIFs
								$transparentcolor = imagecolortransparent($image);
								if($transparentcolor >= 0 && $transparentcolor < imagecolorstotal($image))
								{
									$transparentcolor = imagecolortransparent($image_p, $transparentcolor);
									imagefilledrectangle($image_p, 0, 0, $width, $height, $transparentcolor);
								}

								if($arResize["METHOD"] === "resample")
									imagecopyresampled($image_p, $image, $x, $y, 0, 0, $width, $height, $width_orig, $height_orig);
								else
									imagecopyresized($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
								if (strlen($_str)>0){
									// определяем координаты вывода текста
									$size = 2; // размер шрифта
									$str=$_str;
									$x_text = $width-imagefontwidth($size)*strlen($str)-3;
									$y_text = $height-imagefontheight($size)-3;

									// определяем каким цветом на каком фоне выводить текст
									$white = imagecolorallocatealpha($image_p, 255, 255, 255, 50);
									$black = imagecolorallocatealpha($image_p, 0, 0, 0, 90);
									imagestring($image_p, $size, $x_text-1, $y_text-1, $str, $black);
									imagestring($image_p, $size, $x_text+1, $y_text+1, $str, $black);
									imagestring($image_p, $size, $x_text+1, $y_text-1, $str, $black);
									imagestring($image_p, $size, $x_text-1, $y_text+1, $str, $black);
									imagestring($image_p, $size, $x_text-1, $y_text,   $str, $black);
									imagestring($image_p, $size, $x_text+1, $y_text,   $str, $black);
									imagestring($image_p, $size, $x_text,   $y_text-1, $str, $black);
									imagestring($image_p, $size, $x_text,   $y_text+1, $str, $black);
									imagestring($image_p, $size, $x_text, $y_text, $str, $white);
									unset ($str);
								}
								CheckDirPath(GetDirPath($newImageFile),true);
								imagegif($image_p, $newImageFile);
							}
							else
							{
								//Save transparency for PNG
								$transparentcolor = imagecolorallocate($image_p, 255, 255, 255);
								$transparentcolor = imagecolortransparent($image_p, $transparentcolor);
								imagealphablending($image_p, true);
								imagesavealpha($image_p, true);

								if($arResize["METHOD"] === "resample")
									imagecopyresampled($image_p, $image, $x, $y, 0, 0, $width, $height, $width_orig, $height_orig);
								else
									imagecopyresized($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
								if (strlen($_str)>0){
									// определяем координаты вывода текста
									$size = 2; // размер шрифта
									$str=$_str;
									$x_text = $width-imagefontwidth($size)*strlen($str)-3;
									$y_text = $height-imagefontheight($size)-3;

									// определяем каким цветом на каком фоне выводить текст
									$white = imagecolorallocatealpha($image_p, 255, 255, 255, 50);
									$black = imagecolorallocatealpha($image_p, 0, 0, 0, 90);
									imagestring($image_p, $size, $x_text-1, $y_text-1, $str, $black);
									imagestring($image_p, $size, $x_text+1, $y_text+1, $str, $black);
									imagestring($image_p, $size, $x_text+1, $y_text-1, $str, $black);
									imagestring($image_p, $size, $x_text-1, $y_text+1, $str, $black);
									imagestring($image_p, $size, $x_text-1, $y_text,   $str, $black);
									imagestring($image_p, $size, $x_text+1, $y_text,   $str, $black);
									imagestring($image_p, $size, $x_text,   $y_text-1, $str, $black);
									imagestring($image_p, $size, $x_text,   $y_text+1, $str, $black);
									imagestring($image_p, $size, $x_text, $y_text, $str, $white);
									unset ($str);
								}
								CheckDirPath(GetDirPath($newImageFile),true);
								imagepng($image_p, $newImageFile);
							}

							imagedestroy($image);
							imagedestroy($image_p);

							$arFile["size"] = filesize($newImageFile);

					}
				}
				$arResultImg["IMPATCH"]="/upload/PREVIEW/".$_width."x".$_height."/".$arImg["SUBDIR"]."/".$arImg["FILE_NAME"];
				$arResultImg["SIZE"]=array();
				$arResultImg["SIZE"] = @getimagesize($_SERVER["DOCUMENT_ROOT"].$arResultImg["IMPATCH"]);
				$arResultImg["ADDON"] = " ".trim($_addon)." ";
				//return "/upload/PREVIEW/".$_width."x".$_height."/".$arImg["SUBDIR"]."/".$arImg["FILE_NAME"];
	//--------------------------------------------------------------------------------------------------------
			}
		}
		if (is_array($arResultImg)){
			if ($_showHtml=="Y"){
				return "<img src=\"".$arResultImg["IMPATCH"]."\" width=\"".$arResultImg["SIZE"][0]."\" height=\"".$arResultImg["SIZE"][1]."\"".$arResultImg["ADDON"]." />";
			}
			else
			{
				return $arResultImg;
			}
		}
	}
	
	/**
	 * Изменение размера изображения
	 *
	 * @param int $filePath путь до изображения или ID изображения в битриксе
	 * @param int $maxWidth Максимальная ширина изображения
	 * @param int $maxHeight Максимальная высота изображения
	 * @param array/string/boolean $fillColor Цвет заливки фона. Может быть как массивом так и hex кодом или булевой переменной. По умолчанию белый.
	 * @param string $watermarkFilePath Путь до ватермарки. Если указан, то наносит её.
	 * Примеры:
	 * 		"" - белый
	 * 		array() - белый
	 * 		true - белый
	 * 		false - без создания фона
	 * 		array(0,0,0) - чёрный
	 * 		array(255,255,255) - белый
	 *		"#fff" - белый
	 *		"#ffffff" - белый
	 *		"#000" - чёрный
	 *		"#000000" - чёрный
	 * @return array
	 */
	function imageResize($filePath, $maxWidth, $maxHeight, $fillColor = true, $watermarkFilePath = "")
	{
		if(intval($filePath) > 0)
		{
			$filePath = CFile::GetPath($filePath);
		}
		
		$filePath = CPL::drc($filePath);
		$watermarkFilePath = CPL::drc($watermarkFilePath);
		
		
		$tmp = getimagesize($filePath);
		$arImg = array(
			"WIDTH" => $tmp[0],
			"HEIGHT" => $tmp[1],
			"CONTENT_TYPE" => $tmp["mime"],
			"FILE_NAME" => basename($filePath),
			"FILE_SIZE" => filesize($filePath),
		);
		$maxWidth=intval($maxWidth); //ширина
		$maxHeight=intval($maxHeight); //высота
		
		
		$is_image = CFile::IsImage($arImg["FILE_NAME"], $arImg["CONTENT_TYPE"]);
		if($is_image)
		{
			$useFill = false;
			if(is_bool($fillColor) && $fillColor)
			{
				$fillColor = array(255,255,255);
				$useFill = true;
			}
			elseif(is_string($fillColor))
			{
				if(strpos($fillColor, "#") === 0 && (strlen($fillColor) == 7 || strlen($fillColor) == 4))
				{
					if(strlen($fillColor) == 4)
						$fillColor = "#".substr($fillColor,1,1).substr($fillColor,1,1).substr($fillColor,2,1).substr($fillColor,2,1).substr($fillColor,3,1).substr($fillColor,3,1);
						
					$fillColor = array(
						intval(substr($fillColor,1,2), 16),
						intval(substr($fillColor,3,2), 16),
						intval(substr($fillColor,5,2), 16),
					);
					
				}
				else
				{
					$fillColor = array(255,255,255);
				}
				$useFill = true;
			}
			elseif(is_array($fillColor))
			{
				if(count($fillColor) !== 3)
				{
					$fillColor = array(255,255,255);
				}
				$useFill = true;
			}
			
			if($useFill)
				$arImg["SUBDIR"] = implode("_", $fillColor);
			else
				$arImg["SUBDIR"] = "no_fill";
			
			
			$newFilePath = "/upload/PREVIEW/".$maxWidth."x".$maxHeight."/".$arImg["SUBDIR"]."/".$arImg["FILE_SIZE"].$arImg["FILE_NAME"];
			
		
			$arResultImg = array();
			if(is_file($_SERVER["DOCUMENT_ROOT"].$newFilePath))
			{
				//Картинка уже есть!
				$arResultImg = @getimagesize($_SERVER["DOCUMENT_ROOT"].$newFilePath);
				$arResultImg = array(
					"PATH" => $newFilePath,
					"WIDTH" => $arResultImg[0],
					"HEIGHT" => $arResultImg[1],
					"IMAGE_TYPE" => $arResultImg[2],
					"MIME" => $arResultImg["mime"],
					"BITS" => $arResultImg["bits"],
				);
			}
			else
			{
				//Картинки нет
				$newImageFile=$_SERVER["DOCUMENT_ROOT"].$newFilePath;

				$arResize = Array (
					"WIDTH" => $maxWidth,
					"HEIGHT" => $maxHeight,
				);
				$arFile = CFile::MakeFileArray($filePath);
				//---------------------Создаю картинку------------------------------------------------
				$file = $arFile["tmp_name"];
				if(file_exists($file) and is_file($file))
				{
					$width = intval($arResize["WIDTH"]);
					$height = intval($arResize["HEIGHT"]);

					$orig = @getimagesize($file);
					$img_width = $widht;
					$img_height = $height;
					if(is_array($orig))
					{
						if(($width > 0 && $orig[0] > $width) || ($height > 0 && $orig[1] > $height))
						{

							$width_orig = $orig[0];
							$height_orig = $orig[1];

							if($width <= 0)
							{
								$width = $width_orig;
							}

							if($height <= 0)
							{
								$height = $height_orig;
							}

							$height_new = $height_orig;
							if($width_orig > $width)
							{
								$height_new = ($width / $width_orig) * $height_orig;
								$height_new = ($width / $width_orig) * $height_orig;
							}

							if($height_new > $height)
							{
								$width = ($height / $height_orig) * $width_orig;
							}
							else
							{
								$height = $height_new;
							}
						}
						else
						{
							$width = $orig[0];
							$height = $orig[1];
							$width_orig = $orig[0];
							$height_orig = $orig[1];
						}
						
						$image_type = $orig[2];
						
						
						if ($useFill)
						{
							$image_p = ImageCreateTrueColor($maxWidth, $maxHeight);
							
							$color = imagecolorallocate($image_p, $fillColor[0], $fillColor[1], $fillColor[2]);
							
							imagefill($image_p, 0, 0, $color);
							$x = ($maxWidth - $width) / 2;
							$y = ($maxHeight - $height) / 2;
						}
						else
						{
							$image_p = ImageCreateTrueColor($width, $height);
							$x=0;
							$y=0;
						}
						
						switch($image_type)
						{
							case IMAGETYPE_JPEG:
						
								$image = imagecreatefromjpeg($file);
								
								imagecopyresampled($image_p, $image, $x, $y, 0, 0, $width, $height, $width_orig, $height_orig);

								if(strlen($watermarkFilePath) > 0)
								{
									// imagealphablending($image_p, true);
									$pngWaterMarkImg = @imagecreatefrompng($watermarkFilePath);



									
									imagecopyresampled($image_p, $pngWaterMarkImg, $x, $y, 0, 0, imagesx($pngWaterMarkImg), imagesy($pngWaterMarkImg), $width, $height);
									imagedestroy($pngWaterMarkImg);
									// imagealphablending($image_p, false);
								}
								
								CheckDirPath(GetDirPath($newImageFile),true);
								imagejpeg($image_p, $newImageFile, 100);
								break;
						
							case IMAGETYPE_GIF:
						
								$image = imagecreatefromgif($file);
								
								
								imagetruecolortopalette($image_p, true, imagecolorstotal($image));
								imagepalettecopy($image_p, $image);

								//Save transparency for GIFs
								$transparentcolor = imagecolortransparent($image);
								if($transparentcolor >= 0 && $transparentcolor < imagecolorstotal($image))
								{
									$transparentcolor = imagecolortransparent($image_p, $transparentcolor);
									imagefilledrectangle($image_p, 0, 0, $width, $height, $transparentcolor);
								}

								imagecopyresampled($image_p, $image, $x, $y, 0, 0, $width, $height, $width_orig, $height_orig);
								
								
								
								if(strlen($watermarkFilePath) > 0)
								{
									imagealphablending($image_p, true);
									$pngWaterMarkImg = @imagecreatefrompng($watermarkFilePath);

									imagecopyresampled($image_p, $pngWaterMarkImg, $x, $y, 0, 0, imagesx($pngWaterMarkImg), imagesy($pngWaterMarkImg), $width, $height);
									imagedestroy($pngWaterMarkImg);
									imagealphablending($image_p, false);
								}
								
								CheckDirPath(GetDirPath($newImageFile),true);
								
								imagegif($image_p, $newImageFile);
								
								break;
						
							case IMAGETYPE_PNG:
						
								$image = @imagecreatefrompng($file);
								
								$transparentcolor = imagecolorallocatealpha($image_p, 0, 0, 0, 127);
								imagefilledrectangle($image_p, 0, 0, $maxWidth, $maxHeight, $transparentcolor);
								$transparentcolor = imagecolortransparent($image_p, $transparentcolor);


								imagealphablending($image_p, false);
								imagecopyresampled($image_p, $image, $x, $y, 0, 0, $width, $height, $width_orig, $height_orig);
								

								if(strlen($watermarkFilePath) > 0)
								{
									imagealphablending($image_p, true);
									$pngWaterMarkImg = @imagecreatefrompng($watermarkFilePath);

									imagecopyresampled($image_p, $pngWaterMarkImg, $x, $y, 0, 0, imagesx($pngWaterMarkImg), imagesy($pngWaterMarkImg), $width, $height);
									imagedestroy($pngWaterMarkImg);
									imagealphablending($image_p, false);
								}
								
								
								imagesavealpha($image_p, true);
								// imagepng($image_p, $destinationFile);
								CheckDirPath(GetDirPath($newImageFile),true);
								imagepng($image_p, $newImageFile);
							
								
								break;
						
							case IMAGETYPE_BMP:
								return false;
								$image = imagecreatefromwbmp($file);
								
								imagecopyresampled($image_p, $image, $x, $y, 0, 0, $width, $height, $width_orig, $height_orig);
								
								CheckDirPath(GetDirPath($newImageFile),true);
								//image2wbmp($image_p, $newImageFile);
								imagewbmp($image_p, $newImageFile);
								
								break;
								
							default:
								return false;
						}

						imagedestroy($image);
						imagedestroy($image_p);

						$arFile["size"] = filesize($newImageFile);
					}
				}
				$arResultImg = @getimagesize($_SERVER["DOCUMENT_ROOT"].$newFilePath);
				
				// if(strlen($watermarkFilePath) > 0)
				// {
					// CPL::AddWaterMark($newFilePath, $watermarkFilePath);
				// }
				
				$arResultImg = array(
					"PATH" => $newFilePath,
					"WIDTH" => $arResultImg[0],
					"HEIGHT" => $arResultImg[1],
					"IMAGE_TYPE" => $arResultImg[2],
					"MIME" => $arResultImg["mime"],
					"BITS" => $arResultImg["bits"],
				);
			//--------------------------------------------------------------------------------------------------------
			}
			
			return $arResultImg;
		}
		
		return false;
	}
	
	/**
	 * Изменение размера изображения
	 *
	 * @param int $filePath путь до изображения или ID изображения в битриксе
	 * @param int $maxWidth Максимальная ширина изображения
	 * @param int $maxHeight Максимальная высота изображения
	 * @param mixed $fillColor Цвет заливки фона. Может быть как массивом так и hex кодом или булевой переменной. По умолчанию белый.
	 * Примеры:
	 * 		"" - белый
	 * 		array() - белый
	 * 		true - белый
	 * 		false - без создания фона
	 * 		array(0,0,0) - чёрный
	 * 		array(255,255,255) - белый
	 *		"#fff" - белый
	 *		"#ffffff" - белый
	 *		"#000" - чёрный
	 *		"#000000" - чёрный
	 * @param string $bAddFileSize Добавлять ли размер файла в название файла
	 * @return array
	 */
	static function getResizeImage($filePath, $maxWidth = 0, $maxHeight = 0, $fillColor = true, $bAddFileSize = false)
	{
		if(intval($filePath) > 0)
			$filePath = CFile::GetPath($filePath) ?: $filePath;
		
		$filePath = CPL::drc($filePath);
		
		if(!file_exists($filePath) || !is_file($filePath))
			return false;
		
		$width = $maxWidth = round(floatval($maxWidth));
		$height = $maxHeight = round(floatval($maxHeight));
		if(!$width && !$height)
			return false;
		
		$tmp = getimagesize($filePath);
		$arOrigImg = array(
			"WIDTH" => $tmp[0],
			"HEIGHT" => $tmp[1],
			"IMAGETYPE" => $tmp[2],
			"CONTENT_TYPE" => $tmp["mime"],
			"FILE_NAME" => basename($filePath),
			"FILE_SIZE" => filesize($filePath),
		);
		
		if(!CFile::IsImage($arOrigImg['FILE_NAME'], $arOrigImg['CONTENT_TYPE']))
			return false;
	
		$bUseFill = false;
		if(is_bool($fillColor) && $fillColor) {
			$fillColor = array(255,255,255);
			$bUseFill = true;
		}
		elseif(is_string($fillColor)) {
			// if(strpos($fillColor, "#") === 0 && (strlen($fillColor) == 7 || strlen($fillColor) == 4)) {
				// if(strlen($fillColor) == 4)
					// $fillColor = "#".str_repeat(substr($fillColor,1,1), 2).str_repeat(substr($fillColor,2,1), 2).str_repeat(substr($fillColor,3,1), 2);
				
				// $fillColor = array(
					// intval(substr($fillColor,1,2), 16),
					// intval(substr($fillColor,3,2), 16),
					// intval(substr($fillColor,5,2), 16),
				// );
			// }
			if(strpos($fillColor, "#") === 0 && (($bShort = strlen($fillColor) == 4) || strlen($fillColor) == 7)) {
				list($r, $g, $b) = str_split(substr($fillColor, 1), $bShort ? 1 : 2);
				
				$fillColor = array(
					intval($bShort ? $r.$r : $r, 16),
					intval($bShort ? $g.$g : $g, 16),
					intval($bShort ? $b.$b : $b, 16),
				);
			}
			else
				$fillColor = array(255,255,255);

			$bUseFill = true;
		}
		elseif(is_array($fillColor)) {
			if(count($fillColor) !== 3)
				$fillColor = array(255,255,255);

			$bUseFill = true;
		}
		
		$subdir = $bUseFill ? implode("_", $fillColor) : "no_fill";
		$fileName = ($bAddFileSize ? $arOrigImg["FILE_SIZE"] : "").$arOrigImg["FILE_NAME"];
		$newFilePath = "/upload/PREVIEW/".$maxWidth."x".$maxHeight."/".$subdir."/".$fileName;
		
		//Если картинки ещё нет
		if(!is_file($_SERVER["DOCUMENT_ROOT"].$newFilePath)) {
			$newImageFile = $_SERVER["DOCUMENT_ROOT"].$newFilePath;
			
			if(!$width)
				$width = $height / $arOrigImg["HEIGHT"] * $arOrigImg["WIDTH"];
			if(!$height)
				$height = $width / $arOrigImg["WIDTH"] * $arOrigImg["HEIGHT"];
			
			if(($width && $arOrigImg["WIDTH"] > $width) || ($height && $arOrigImg["HEIGHT"] > $height)) {
				$height_new = $arOrigImg["HEIGHT"];
				if($arOrigImg["WIDTH"] > $width)
					$height_new = $width / $arOrigImg["WIDTH"] * $arOrigImg["HEIGHT"];

				if($height_new > $height)
					$width = $height / $arOrigImg["HEIGHT"] * $arOrigImg["WIDTH"];
				else
					$height = $height_new;
				
				$width = round($width);
				$height = round($height);
			}
			else {
				$width = $arOrigImg["WIDTH"];
				$height = $arOrigImg["HEIGHT"];
			}
							
			if ($bUseFill) {
				$image_p = ImageCreateTrueColor($maxWidth, $maxHeight);
				$color = imagecolorallocate($image_p, $fillColor[0], $fillColor[1], $fillColor[2]);
				
				imagefill($image_p, 0, 0, $color);
				$x = ($maxWidth - $width) / 2;
				$y = ($maxHeight - $height) / 2;
			}
			else {
				$image_p = ImageCreateTrueColor($width, $height);
				$x=0;
				$y=0;
			}
			
			switch($arOrigImg["IMAGETYPE"]) {
				case IMAGETYPE_JPEG:
					$image = imagecreatefromjpeg($filePath);
					
					imagecopyresampled($image_p, $image, $x, $y, 0, 0, $width, $height, $arOrigImg["WIDTH"], $arOrigImg["HEIGHT"]);
					
					CheckDirPath(GetDirPath($newImageFile),true);
					imagejpeg($image_p, $newImageFile, 100);
					break;
			
				case IMAGETYPE_GIF:
					$image = imagecreatefromgif($filePath);
					imagetruecolortopalette($image_p, true, imagecolorstotal($image));
					imagepalettecopy($image_p, $image);

					//Save transparency for GIFs
					$transparentcolor = imagecolortransparent($image);
					if($transparentcolor >= 0 && $transparentcolor < imagecolorstotal($image)) {
						$transparentcolor = imagecolortransparent($image_p, $transparentcolor);
						imagefilledrectangle($image_p, 0, 0, $width, $height, $transparentcolor);
					}

					imagecopyresampled($image_p, $image, $x, $y, 0, 0, $width, $height, $arOrigImg["WIDTH"], $arOrigImg["HEIGHT"]);
					
					CheckDirPath(GetDirPath($newImageFile),true);
					imagegif($image_p, $newImageFile);
					
					break;
			
				case IMAGETYPE_PNG:
					$image = @imagecreatefrompng($filePath);
					
					$transparentcolor = imagecolorallocatealpha($image_p, 0, 0, 0, 127);
					imagefilledrectangle($image_p, 0, 0, $maxWidth, $maxHeight, $transparentcolor);
					$transparentcolor = imagecolortransparent($image_p, $transparentcolor);

					imagealphablending($image_p, false);
					imagecopyresampled($image_p, $image, $x, $y, 0, 0, $width, $height, $arOrigImg["WIDTH"], $arOrigImg["HEIGHT"]);
					
					imagesavealpha($image_p, true);
					
					CheckDirPath(GetDirPath($newImageFile),true);
					imagepng($image_p, $newImageFile);
				
					break;
			
				case IMAGETYPE_BMP:
					return false;
					$image = imagecreatefromwbmp($filePath);
					
					imagecopyresampled($image_p, $image, $x, $y, 0, 0, $width, $height, $arOrigImg["WIDTH"], $arOrigImg["HEIGHT"]);
					
					CheckDirPath(GetDirPath($newImageFile),true);
					imagewbmp($image_p, $newImageFile);
					
					break;
					
				default:
					return false;
			}

			imagedestroy($image);
			imagedestroy($image_p);
		}
		
		return $newFilePath;
	}
	
	/**
	 * Нанесение ватермарки
	 *
	 * @param string $filePath путь до изображения
	 * @param string $watermarkFilePath путь до ватермарки
	 */
	function AddWaterMark($filePath, $watermarkFilePath)
	{
		$filePath = CPL::drc($filePath);
		$watermarkFilePath = CPL::drc($watermarkFilePath);
		
		// Получаем ширину, высоту и тип исходного изображени¤
		list($source_image_width, $source_image_height, $source_image_type) = getimagesize($filePath);
		// Если по каким, то причинам неопределён тип, нам не стоит выполнять какие-либо действи¤ с водяным знаком, по скольку это не картинка вовсе
		if ($source_image_type === NULL) {
			return false;
		}
		// Создаем, так называемый ресурс изображения из исходной картинки в зависимости от типа исходной картинки
		switch ($source_image_type) {
		//	case 1: // картинка *.gif
		//		$source_image = imagecreatefromgif($filePath);
		//		imagepng($source_image, $filePath);
		//		imagedestroy($source_image);
		//		list($source_image_width, $source_image_height, $source_image_type) = getimagesize($filePath);
		//		$source_image = imagecreatefrompng($filePath);
		//		break;
			case 2: // картинка *.jpeg, *.jpg
				$source_image = imagecreatefromjpeg($filePath);
				break;
			case 3: // картинка *.png
				$source_image = imagecreatefrompng($filePath);
				break;
			default:
				return false; // Если картинка другого формата, или не картинка совсем, то опять же не стоит делать, что либо дальше с водяным знаком
		}
		// Создаем ресурс изображения для нашего водяного знака
		$watermark_image = imagecreatefrompng($watermarkFilePath);
		// Получаем значения ширины и высоты
		$watermark_width = imagesx($watermark_image);
		$watermark_height = imagesy($watermark_image);
		// Наложение ЦВЗ с прозрачным фоном
		imagealphablending($source_image, true);
		imagesavealpha($source_image, true);
		// Самая важная функция - функция копирования и наложения нашего водяного знака на исходное изображение
		imagecopy($source_image, $watermark_image, $source_image_width - $watermark_width, $source_image_height - $watermark_height, 0, 0, $watermark_width, $watermark_height);

		// Создание и сохранение результирующего изображения с водяным знаком 
		switch ($source_image_type) {
		//	case 1: // картинка *.gif
		//		imagepng($source_image, $filePath);
		//		break;
			case 2: // картинка *.jpeg, *.jpg
				imagejpeg($source_image, $filePath, 100);
				break;
			case 3: // картинка *.png
				imagepng($source_image, $filePath);
				break;
		}
		
		// Уничтожение всех временных ресурсов
		imagedestroy($source_image);
		imagedestroy($watermark_image);
	}
	
	

	/**
	* Проверяем, является ли $password текущим паролем пользователя.
	*
	* @param int $userId
	* @param string $password
	*
	* @return bool
	*/
	function isUserPassword($userId, $password)
	{
	    $userData = CUser::GetByID($userId)->Fetch();

	    $salt = substr($userData['PASSWORD'], 0, (strlen($userData['PASSWORD']) - 32));

	    $realPassword = substr($userData['PASSWORD'], -32);
	    $password = md5($salt.$password);

	    return ($password == $realPassword);
	}
	
	
	
	/**
	* Improperly formatted javascript being add to tags and the limit of 15 instences of recursion 
	* before memory allocation runs out are some of the concerns involved in coding.  
	* Here is the code that I created to leave tags intact but strip scripting from only inside the tags... 
	*
	*
	*/
	function strip_javascript($filter) {

		// realign javascript href to onclick
		$filter = preg_replace("/href=(['\"]).*?javascript:(.*)?
		\\1/i", "onclick=' $2 '", $filter);

		//remove javascript from tags
		while(preg_match("/<(.*)?javascript.*?\(.*?((?>[^()]+)
		|(?R)).*?\)?\)(.*)?>/i", $filter))
		$filter = preg_replace("/<(.*)?javascript.*?\(.*?((?>
		[^()]+)|(?R)).*?\)?\)(.*)?>/i", "<$1$3$4$5>", $filter);

		// dump expressions from contibuted content
		if(0) $filter = preg_replace("/:expression\(.*?((?>[^
		(.*?)]+)|(?R)).*?\)\)/i", "", $filter);

		while(preg_match("/<(.*)?:expr.*?\(.*?((?>[^()]+)|(?
		R)).*?\)?\)(.*)?>/i", $filter))
		$filter = preg_replace("/<(.*)?:expr.*?\(.*?((?>[^()]
		+)|(?R)).*?\)?\)(.*)?>/i", "<$1$3$4$5>", $filter);

		// remove all on* events   
		while(preg_match("/<(.*)?\s?on.+?=?\s?.+?(['\"]).*?\\2
		\s?(.*)?>/i", $filter) )
		$filter = preg_replace("/<(.*)?\s?on.+?=?\s?.+?
		(['\"]).*?\\2\s?(.*)?>/i", "<$1$3>", $filter);

		return $filter;
	} 
	
	
	/**
	* Очистка текста от нежелательного контента (html, JS)
	*
	* @param string $text
	*
	* @return string
	*/
	function clearText($text = "") {
		return nl2br(htmlspecialchars(strip_tags(CPL::strip_javascript($text))));
	}



	/**
	* Обновление курса вылют с сайта ЦБ РФ
	*
	* @param string $CURRENCY  Код валюты
	*
	* @return string
	**/
	function GetRateFromCBR($CURRENCY) {
		global $DB;
		global $APPLICATION;

		$DATE_RATE=date("d.m.Y");//сегодня
		$QUERY_STR = "date_req=".$DB->FormatDate($DATE_RATE, CLang::GetDateFormat("SHORT", $lang), "D.M.Y");

		//делаем запрос к www.cbr.ru с просьбой отдать курс на нынешнюю дату          
		$strQueryText = QueryGetData("www.cbr.ru", 80, "/scripts/XML_daily.asp", $QUERY_STR, $errno, $errstr);

		//получаем XML и конвертируем в кодировку сайта          
		$charset = "windows-1251";
		if (preg_match("/<"."\?XML[^>]{1,}encoding=[\"']([^>\"']{1,})[\"'][^>]{0,}\?".">/i", $strQueryText, $matches))
		{
			$charset = Trim($matches[1]);
		}
		$strQueryText = eregi_replace("<!DOCTYPE[^>]{1,}>", "", $strQueryText);
		$strQueryText = eregi_replace("<"."\?XML[^>]{1,}\?".">", "", $strQueryText);
		$strQueryText = $APPLICATION->ConvertCharset($strQueryText, $charset, SITE_CHARSET);

		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/xml.php");

		//парсим XML
		$objXML = new CDataXML();
		$res = $objXML->LoadString($strQueryText);
		if($res !== false)
		$arData = $objXML->GetArray();
		else
		$arData = false;

		$NEW_RATE=Array();

		//получаем курс нужной валюты $CURRENCY
		if (is_array($arData) && count($arData["ValCurs"]["#"]["Valute"])>0)
		{
			for ($j1 = 0; $j1<count($arData["ValCurs"]["#"]["Valute"]); $j1++)
			{
				if ($arData["ValCurs"]["#"]["Valute"][$j1]["#"]["CharCode"][0]["#"]==$CURRENCY)
				{
					$NEW_RATE['CURRENCY']=$CURRENCY;
					$NEW_RATE['RATE_CNT'] = IntVal($arData["ValCurs"]["#"]["Valute"][$j1]["#"]["Nominal"][0]["#"]);
					$NEW_RATE['RATE'] = DoubleVal(str_replace(",", ".", $arData["ValCurs"]["#"]["Valute"][$j1]["#"]["Value"][0]["#"]));
					$NEW_RATE['DATE_RATE']=$DATE_RATE;
					break;
				}
			}
		}

		if ((isset($NEW_RATE['RATE']))&&(isset($NEW_RATE['RATE_CNT'])))
		{

			//курс получили, возможно, курс на нынешнюю дату уже есть на сайте, проверяем
			CModule::IncludeModule('currency');
			$arFilter = array(
				"CURRENCY" => $NEW_RATE['CURRENCY'],
				"DATE_RATE"=>$NEW_RATE['DATE_RATE']
				);
			$by = "date";
			$order = "desc";

			$db_rate = CCurrencyRates::GetList($by, $order, $arFilter);
			
			//такого курса нет, создаём курс на нынешнюю дату
			if(!$ar_rate = $db_rate->Fetch())
			CCurrencyRates::Add($NEW_RATE);

		}

		//возвращаем код вызова функции, чтобы агент не "убился"
		return 'GetRateFromCBR("'.$CURRENCY.'");';
	}
	
	function getRemoteCountry($ip = "") {
		if ($ip!="") {
			$sock = fsockopen ("whois.ripe.net",43,$errno,$errstr);
			if ($sock) {
				fputs ($sock, $ip."\r\n");
				while (!feof($sock)) {
					$str.=trim(fgets ($sock,128)." <br>");
				}
			}
			else {
				$str.="$errno($errstr)";
				return;
			}
			fclose ($sock);
		}
		$need = "country:";
		$pos = strpos($str,$need);
		$search = substr($str,$pos,18);
		$excount = explode(":", $search);

		$country = trim($excount[1]);
		return $country;
	}


	

	/**
	* 
	*
	*
	*
	*/
	function db2Array($dbResult, $method = null) {
		if (!is_object($dbResult)) {
			return array();
		}
		if (!is_string($method)) {
			$method = 'Fetch';
		}

		$result = array();
		while (false !== ($result[] = $dbResult->$method()));
			array_pop($result);

		return $result;
	}
	
	
	function rec_file_size($ss)
	{
		$mb = (1024 * 1024);
		if ($ss > $mb) 
		{
			return sprintf ("%01.2f", ($ss / $mb)) . " мб";
		} 
		elseif ($ss >= 1024) 
		{
			return sprintf ("%01.0f", ($ss / 1024)) . " кб";
		} 
		else 
		{
			return sprintf("%01.0f", $ss)." байт";
		}
	}

	function GetCountInBasket()
	{
		if (CModule::IncludeModule("sale"))
		{
			$dbBasketItems = CSaleBasket::GetList(array(), array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"), false, false, array());
			$i=0;
			while($ar=$dbBasketItems->fetch())
				$i++;
			return $i;
		}
	}
	
	/**
	* Узнаем количество товара в корзине
	*
	* 
	*/
    function getBasketItemQuantity($PRODUCT_ID)
    {
        $PRODUCT_ID=intval($PRODUCT_ID);
        if (!CModule::IncludeModule("sale") && $PRODUCT_ID>0)
            return false;
        
        $arBasketItem = CSaleBasket::GetList(
                array(),
                array(
					"FUSER_ID" => CSaleBasket::GetBasketUserID(),
					"LID" => SITE_ID,
					"ORDER_ID" => "NULL",
					"PRODUCT_ID"=>$PRODUCT_ID,
				),
                false,
                array("nTopCount" => 1),
                array("QUANTITY")
		)->GetNext(false, false);
        if($arBasketItem)
			return intval($arBasketItem["QUANTITY"]);
        else
			return false;
    }

	/**
	* Узнаем, в корзине товар или нет
	*
	* 
	*/
    function IsInBasket($ID)
    {
        $ID=intval($ID);
        if (!CModule::IncludeModule("sale") && $ID>0)
            return false;
        
        $arBasketItems = array();
        $dbBasketItems = CSaleBasket::GetList(
                array(
                        "NAME" => "ASC",
                        "ID" => "ASC"
                    ),
                array(
                        "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                        "LID" => SITE_ID,
                        "ORDER_ID" => "NULL",
                        "PRODUCT_ID"=>$ID,
                    ),
                false,
                false,
                array("ID", "PRODUCT_ID", "QUANTITY")
            );
        if ($arItems = $dbBasketItems->GetNext())
            return $arItems;
            
        return false;
    }

	
	/**
	 * Транслитерирует строку и заменяет все символы на знак -. Убирает повторяющиеся  знаки -.
	 * @param array $arSelect
	 * @return string
	 */
	function Transliterate($string)
	{
		$string = trim($string);
		$string = preg_replace("/[_\s\.,?!\[\](){}]+/", "-", $string);
		$string = preg_replace("/-{2,}/", "--", $string);
		$string = preg_replace("/_-+_/", "--", $string);
		$string = preg_replace("/[_\-]+$/", "", $string);
		
		$cyr = array(
			"Щ","Ш","Ч","Ц","Ю","Я","Ж","А","Б","В","Г","Д","Е","Ё","З","И","Й","К","Л","М","Н","О","П","Р","С","Т","У","Ф","Х","Ь","Ы","Ъ","Э","Ю","Я",
			"щ","ш","ч","ц","ю","я","ж","а","б","в","г","д","е","ё","з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х","ь","ы","ъ","э","ю","я"
		);
		$lat = array(
			"Shh","Sh","Ch","C","Ju","Ja","Zh","A","B","V","G","D","Je","Jo","Z","I","J","K","L","M","N","O","P","R","S","T","U","F","Kh","","Y","","E","Je","Ji",
			"shh","sh","ch","c","ju","ja","zh","a","b","v","g","d","je","jo","z","i","j","k","l","m","n","o","p","r","s","t","u","f","kh","","y","","e","je","ji"
		);
		for ($i=0; $i < count($cyr); $i++)
		{
			$c_cyr = $cyr[$i];
			$c_lat = $lat[$i];
			$string = str_replace($c_cyr, $c_lat, $string);
		}
		$string = preg_replace("/([qwrtpsdfghklzxcvbnmQWRTPSDFGHKLZXCVBNM]+)[jJ]e/", "\${1}e", $string);
		$string = preg_replace("/([qwrtpsdfghklzxcvbnmQWRTPSDFGHKLZXCVBNM]+)[jJ]/", "\${1}'", $string);
		$string = preg_replace("/([eyuioaEYUIOA]+)[Kk]h/", "\${1}h", $string);
		$string = preg_replace("/^kh/", "h", $string);
		$string = preg_replace("/^Kh/", "H", $string);
		
		$string = ToLower($string);
		$string = preg_replace("/j{2,}/", "j", $string);
		$string = preg_replace("/[^0-9a-z_\-]+/", "-", $string);
		$string = preg_replace("/-{2,}/", "-", $string);
		$string = trim($string, "-");
		return $string;
	}
	
	/**
	 * Возвращает массив дерева секций инфоблока. В $arFilter обязательно нужно указать ID инфоблока.
	 * Если нужно получить дерево не с первого уровня, то нужно указатьв  фильтре ID секции и четвёртым параметром указать DEPTH_LEVEL этой секции.
	 *
	 * @param array $arSelect
	 * @param array $arFilter
	 * @param array $key Если не указывать, то по умолчанию будет ID. Это ключ в котором будет лежать информация по разделу. Иногда есть необходимость в качестве ключа использовать NAME.
	 * @param array $depthLevel Уровень с которого стартует дерево. Если не указатьывать, будет первый. Если указывать, то нужно указать ID секции от которой строить дерево в $arFilter
	 * @return integer
	 */
	function getIBlockSectionsTreeOld($arSelect, $arFilter, $key = "ID", $depthLevel = 1, $getElements = false, $arElementsSelect = array())
	{
		if(!isset($arFilter["IBLOCK_ID"]))
			return "Не указан ID инфоблока!";
		
		$arDefaultFilter = array(
			"DEPTH_LEVEL" => $depthLevel,
		);
		
		$arDefaultSelect = array(
			"ID",
			"NAME",
		);
		
		$sectionsReq = CIBlockSection::GetList(array("NAME" => "ASC"),array_merge($arDefaultFilter, $arFilter),false,array_merge($arDefaultSelect, $arSelect));
		$arSectionsTree = array();
		while($arSection = $sectionsReq->GetNext(false,false))
		{
			$arFilter["SECTION_ID"] = $arSection["ID"];
			$arSection["CHILD_SECTIONS"] = CPL::getIBlockSectionsTreeOld($arSelect, $arFilter, $key, ($depthLevel + 1), $getElements, $arElementsSelect);
			
			if($getElements)
			{
				$ElementsReq = CIBlockElement::GetList(array("NAME" => "DESC"),array("IBLOCK_ID" => $arFilter["IBLOCK_ID"], "SECTION_ID" => $arSection["ID"]),false,false,array_merge($arElementsSelect,array("ID", "NAME")));
				$arElements = array();
				while($arElement = $ElementsReq->GetNext(false,false))
				{
					$arElements[$arElement["ID"]]["ID"] = $arElement["ID"];
					$arElements[$arElement["ID"]]["NAME"] = $arElement["NAME"];
					foreach($arElement as $propCode => $value)
					{
						if(strpos($propCode, "PROPERTY_") !== false)
						{
							$arElements[$arElement["ID"]]["PROPERTIES"][$propCode][$value] = $value;
						}
					}
					$arSection["CHILD_ELEMENTS"][$arElement[$key]] = $arElements[$arElement["ID"]];
				}
			}
			
			// foreach($arSection["CHILD_ELEMENTS"] as $elKey => $arElement)
			// {
				// foreach($arElement["PROPERTIES"] as $propCode => $value)
				// {
					// if(is_array($value) && count($value) === 1)
						// $arSection["CHILD_ELEMENTS"][$elKey]["PROPERTIES"][$propCode] = array_shift($value);
				// }
			// }
			
			// CPL::pr($arElements);die;
			$arSectionsTree[$arSection[$key]] = $arSection;
			if(!$arSectionsTree[$arSection[$key]]["CHILD_SECTIONS"])
				unset($arSectionsTree[$arSection[$key]]["CHILD_SECTIONS"]);

			unset($arFilter["SECTION_ID"]);
		}
		
		if(is_array($arSectionsTree) && count($arSectionsTree) > 0)
			return $arSectionsTree;
		else
			return false;
	}
	
	function getIBlockSectionsTree($arSectionsParams,$keyCode = "ID")
	{
		if(!isset($arSectionsParams["arFilter"]["IBLOCK_ID"]))
			return false;
		if(!is_array($arSectionsParams["arOrder"]))
			$arSectionsParams["arOrder"] = array();
		if(!is_array($arSectionsParams["arFilter"]))
			$arSectionsParams["arFilter"] = array();
		if(!is_array($arSectionsParams["arSelect"]))
			$arSectionsParams["arSelect"] = array(
				"ID",
				"NAME",
				"DEPTH_LEVEL",
				"IBLOCK_SECTION_ID"
			);
			
		
		if(!in_array($keyCode, $arSectionsParams["arSelect"]))
			$arSectionsParams[] = $keyCode;
		
		$sectionsReq = CIBlockSection::GetList($arSectionsParams["arOrder"], $arSectionsParams["arFilter"], false, $arSectionsParams["arSelect"]);
		$arSections = array();
		while($arSection = $sectionsReq->GetNext(false,false))
		{
			$arSectionsByDepthLevel[$arSection["DEPTH_LEVEL"]][$arSection["ID"]] = $arSection;
		}
		
		krsort($arSectionsByDepthLevel);
		
		foreach($arSectionsByDepthLevel as $DEPTH_LEVEL => &$arSections)
		{
			foreach($arSections as $arSection)
			{
				if($arSection["IBLOCK_SECTION_ID"] > 0)
					$arSectionsByDepthLevel[($DEPTH_LEVEL - 1)][$arSection["IBLOCK_SECTION_ID"]]["CHILD_SECTIONS"][$arSection[$keyCode]] = $arSection;
					
			}
			if($DEPTH_LEVEL !== 1)
				unset($arSectionsByDepthLevel[$DEPTH_LEVEL]);
		}
		
		$arTmp = end($arSectionsByDepthLevel);
		$arTree = array();
		foreach($arTmp as $key => $arSection)
		{
			$arTree[$arSection[$keyCode]] = $arSection;
		}
		unset($arTmp);
		return $arTree;
	}
	
	/**
	 * Возвращает ID свойства по названию. Название транслитирируется, и свойство в итоге ищется по его коду. Если свойства нету, оно создаётся.
	 *
	 * @param int $IBLOCK_ID
	 * @param string $NAME
	 * @param string $MULTIPLE Если множественное, то нужно указать "Y"
	 * @param array $PROPERTY_TYPE тип свойства
	 * @return integer
	 */
	function addIBlockSectionsTreeFromArray($IBLOCK_ID, $arTree, $arCurrentIBlockTree, $curentSectionID = false)
	{
		foreach($arTree as $sectionName => $arChildTree)
		{
			if(!is_array($arCurrentIBlockTree) || is_array($arCurrentIBlockTree) && !array_key_exists($sectionName, $arCurrentIBlockTree) )
			{
				
				$obSection = new CIBlockSection;
				
				$arLoadProductArray = array(
					"NAME" => $sectionName,
					"CODE" => CPL::UrlTranslit($sectionName),
					"IBLOCK_ID" => $IBLOCK_ID,
					"IBLOCK_SECTION_ID" => $curentSectionID,
				);
				
				if($sectionID = $obSection->add($arLoadProductArray))
					$arCurrentIBlockTree[$sectionName]["ID"] = $sectionID;
				else
				{
					// echo $obSection->LAST_ERROR;
					continue;
				}
			}
			$arCurrentIBlockTree[$sectionName]["CHILD_SECTIONS"] = CPL::addIBlockSectionsTreeFromArray($IBLOCK_ID, $arTree[$sectionName], $arCurrentIBlockTree[$sectionName]["CHILD_SECTIONS"], $arCurrentIBlockTree[$sectionName]["ID"]);
		}
		
		return $arCurrentIBlockTree;
	}
	
	/**
	 * Возвращает ID свойства по названию. Название транслитирируется, и свойство в итоге ищется по его коду. Если свойства нету, оно создаётся.
	 *
	 * @param int $IBLOCK_ID
	 * @param string $NAME
	 * @param string $MULTIPLE Если множественное, то нужно указать "Y"
	 * @param array $PROPERTY_TYPE тип свойства
	 * @return integer
	 */
	function GetPropId($IBLOCK_ID, $NAME, $MULTIPLE = "N", $PROPERTY_TYPE = "S")
	{
		$ibp = new CIBlockProperty;
		$CODE = substr(strtoupper(str_replace("-", "_", CPL::UrlTranslit($NAME))), 0, 45);

		if(strlen($NAME) < 3)
			return false;

		$propertyReq = CIBlockProperty::GetList(Array(), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>$CODE));
		if($prop_fields = $propertyReq->GetNext(false,false))
		{
			$PropID = $prop_fields["ID"];
		}
		else
		{
			$arFields = Array(
				"IBLOCK_ID" => $IBLOCK_ID,
				"ACTIVE" => "Y",
				"NAME" => $NAME,
				"CODE" => $CODE,
				"PROPERTY_TYPE" => $PROPERTY_TYPE,
				"MULTIPLE" => $MULTIPLE,
			);
			$PropID = $ibp->Add($arFields);
		}
		return $PropID;
	}
	
	/**
	 * Возвращает ID элемента по названию. Если элемента нету, то он создаётся (неактивным). Если указан ID типа почтового события, отправляет письмо.
	 *
	 * @param int $IBLOCK_ID
	 * @param string $NAME
	 * @param string $MAIL_EVENT
	 * @return integer
	 */
	 
	
	function GetElementIdByName($IBLOCK_ID, $NAME, $MAIL_EVENT = "")
	{
		$arEl = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $IBLOCK_ID, "NAME" => $NAME), false, false, array("ID"))->GetNext(false, false);
		
		if($arEl["ID"])
		{
			return $arEl["ID"];
		}
		else
		{
			$CIBlockElement = new CIBlockElement;
			
			$arLoadProductArray = array(
				"MODIFIED_BY"       => $GLOBALS["USER"]->GetID(),
				"IBLOCK_SECTION_ID" => false,
				"IBLOCK_ID"         => $IBLOCK_ID,
				"NAME"              => $NAME,
				"CODE"              => CPL::UrlTranslit($NAME),
				"ACTIVE"            => "N",            // активен
			);
		
			if($ELEMENT_ID = $CIBlockElement->Add($arLoadProductArray))
			{
				
				CPL::newElementEmailSend($MAIL_EVENT, $IBLOCK_ID, $ELEMENT_ID);
				
				return $ELEMENT_ID;
			}
			else
			{
				return false;
			}
		}
	}
	
	
	function newElementEmailSend($MAIL_EVENT, $IBLOCK_ID, $ELEMENT_ID)
	{
		if(strlen($MAIL_EVENT) > 0 && intval($IBLOCK_ID) > 0 && intval($ELEMENT_ID) > 0)
		{
			$elArr = CIBlockElement::GetList(array(),array("IBLOCK_ID" => $IBLOCK_ID, "ID" => $ELEMENT_ID),false,false,array("NAME"))->GetNext(false,false);
			$ELEMENT_NAME = $elArr["NAME"];
			
			
			if(strlen($ELEMENT_NAME) > 0)
			{
				$arMailFields = array(
					"IBLOCK_TYPE" => CIBlock::GetArrayByID($IBLOCK_ID, "IBLOCK_TYPE_ID"),
					"IBLOCK_ID" => $IBLOCK_ID,
					"IBLOCK_NAME" => CIBlock::GetArrayByID($IBLOCK_ID, "NAME"),
					"ELEMENT_ID" => $ELEMENT_ID,
					"ELEMENT_NAME" => $ELEMENT_NAME,
					"USER_ID" => $GLOBALS["USER"]->GetID(),
					"USER_LOGIN" => $GLOBALS["USER"]->GetLogin(),										
				);
				
				return CEvent::Send($MAIL_EVENT, SITE_ID, $arMailFields);
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * Проверяет вхождение $_SERVER["DOCUMENT_ROOT"] в путь. Если его нет, то добавляет.
	 * Если передаётся url, то она его не меняет.
	 * @param string $path путь
	 * @return string
	 */
	static function drc($path)
	{
		if(strpos($path, "://") === false && strpos($path, $_SERVER["DOCUMENT_ROOT"]) !== 0)
		{
			$path = $_SERVER["DOCUMENT_ROOT"].$path;
		}
		return $path;
	}
	
	/**
	 * Возвращает массив сделанный из csv файла
	 * Можно передавать локальный путь или url
	 * @param string $filePath путь
	 * @param string $separator разделитель
	 * @param string $enclosure символ ограничителя поля
	 * @return array
	 */
	function ReadCsv($filePath, $separator = ";", $enclosure = '"')
	{
		$filePath = CPL::drc($filePath);
		
		
		if(!$file = fopen($filePath, "r"))
			return false;
		
		
		if(strpos($filePath, "://") !== false)
		{
			$arHeader = array_change_key_case(get_headers($filePath, 1),CASE_LOWER);
			$fileSize = $arHeader['content-length'];
		}
		else
		{
			$fileSize = filesize($filePath);
		}
		
		$content = fread($file, $fileSize);
		$arData = explode("\n", $content);
		
		$keys = array();
		foreach(explode($separator, array_shift($arData)) as $val)
		{
			$keys[] = $val;
		}
		
		//$keys = fgetcsv($file, 0, $separator);//Тут мы получаем названия полей
		$csvArr = array();
		$csvIter = 0;
		foreach($arData as $ln)
		{
			if($ln === "")
				continue;
				
			$i = 0;
			$lnArr = explode($separator, $ln);
			foreach($lnArr as $value)
			{
				if($value[0] === $enclosure && $value[strlen($value) - 1] === $enclosure)
				{
					$value = substr($value, 1,strlen($value) - 2);
				}
					
				$csvArr[$csvIter][trim($keys[$i])] = $value;
				$i++;
			}
			$csvIter++;
		}

		fclose($file);
		return $csvArr;
	}
	
	/**
	 * Записывает данные в csv файл.
	 * Формат массива должен соответствовать массиву из ReadCsv
	 * @param string $filePath путь
	 * @param string $arData массив с данными
	 * @param string $separator разделитель
	 * @param string $enclosure символ ограничителя поля
	 * @return boolean
	 */
	static function WriteCsv($filePath, $arData, $separator = ';', $enclosure = '"')
	{
		if(count($arData) <= 0)
			return false;
		
		//Если первые элемент не массив, то значит что то не так. Выходим.
		foreach($arData as $dataCheck)
		{
			if(!is_array($dataCheck))
				return false;
			break;
		}
		
		$filePath = CPL::drc($filePath);
		
		//Если файла нет, то создаём его
		if(!file_exists($filePath)) {
			CheckDirPath($filePath);
			fclose(fopen($filePath, "x"));
		}
		
		if(file_exists($filePath))
		{
			if($file = fopen($filePath, "w+"))
			{
				$fields = array();
				
				foreach($temp = array_shift($arData) as $field => $val)
				{
					$fields[] = $field;
				}
				
				
				array_unshift($arData, $temp);
				
				fputcsv($file, $fields, $separator, $enclosure);
				
				
				foreach($arData as $data)
				{
					fputcsv($file, $data, $separator, $enclosure);
				}
				
			}else
			{
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * Возвращает массив сделанный из xml файла, средствами битрикса
	 * Можно передавать локальный путь или url
	 * @param string $filePath путь
	 * @return array
	 */
	function ReadXml($filePath)
	{
		$filePath = CPL::drc($filePath);
		
		$xml = new CDataXML();
		
		if(strpos($filePath, "://") !== false)
		{
			$content = file_get_contents($filePath);
			$xml->LoadString($content);
		}
		else
		{
			$xml->Load($filePath);
		}

		$arData = $xml->GetArray();
		
		return $arData;
	}
	
	/**
	 * Возвращает массив цен в формате $arResult["PRICE_MATRIX"] каталога
	 * @param int $ELEMENT_ID
	 * @return array
	 */
	static function GetPriceMatrix($ELEMENT_ID)
	{
		if(!CModule::IncludeModule("catalog") || !$ELEMENT_ID)
			return false;
		
		$PRICE_MATRIX = CatalogGetPriceTableEx($ELEMENT_ID);
		if (isset($PRICE_MATRIX["COLS"]) && is_array($PRICE_MATRIX["COLS"]))
		{
			foreach($PRICE_MATRIX["COLS"] as $keyColumn=>$arColumn)
				$PRICE_MATRIX["COLS"][$keyColumn]["NAME_LANG"] = htmlspecialcharsbx($arColumn["NAME_LANG"]);

			return $PRICE_MATRIX;
		}
		return false;
	}
	
	/**
	 * Узнаём SECTION_ID по SECTION_CODE_PATH (идём по разделам начиная с корневого)
	 * @param int $IBLOCK_ID
	 * @param string $SECTION_CODE_PATH
	 * @return int
	 */
	static function GetSectionIdFromSectionCodePath($IBLOCK_ID, $SECTION_CODE_PATH)
	{
		$IBLOCK_ID = intval($IBLOCK_ID);
		$SECTION_CODE_PATH = trim($SECTION_CODE_PATH, "/");
		
		if(!CModule::IncludeModule("catalog") || !$IBLOCK_ID || !$SECTION_CODE_PATH)
			return false;
		
		$arSectionIDs = array();
		foreach(explode("/", $SECTION_CODE_PATH) as $i => $SECTION_CODE)
		{
			$DEPTH_LEVEL = $i + 1;
			
			$arSectionFilter = array(
				"IBLOCK_ID" => $IBLOCK_ID,
				"DEPTH_LEVEL" => $DEPTH_LEVEL,
				"CODE" => $SECTION_CODE
			);
			//Если уже сделали выборку по родительскому разделу, то добавляем в фильтр его ID
			if(isset($arSectionIDs[$i - 1]))
				$arSectionFilter["SECTION_ID"] = $arSectionIDs[$i - 1];
			
			$SECTION_ID = end(CIBlockSection::GetList(
				array(),
				$arSectionFilter,
				false,
				array("ID"),
				array("nPageSize" => 1)
			)->GetNext(false, false));

			if($SECTION_ID)
				$arSectionIDs[] = $SECTION_ID;
			else
				return false;
		}
		
		return !empty($arSectionIDs) ? end($arSectionIDs) : false;
	}
	
	static function getHLBlockDataClass($HLBLOCK_ID){
		$HLBLOCK_ID = intval($HLBLOCK_ID);
		if(CModule::IncludeModule("highloadblock") && $HLBLOCK_ID)
		{
			$arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById($HLBLOCK_ID)->fetch();
			return \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock)->getDataClass();
		}
		return false;
	}
	static function getSiteURL() {
		return 'http'.(CMain::IsHTTPS() ? 's' : '').'://'.SITE_SERVER_NAME;
	}
}




class CAllPL
{
	function getClassInfo()
	{
		echo "Class ProfLine v0.8.1 build 20091209";
	}
}

/**
 * Класс для удобного сохранения логов
 */
class log
{
	public $logPath = "";
	private $file = false;
	
	private $explodeStatus = "";
	private $startTime;
	private $head = array();
	private $fields = array();
	private $body = array();
	
	 
	/****************************************************************************************************
	*PUBLIC
	****************************************************************************************************/
	
	public function drc($path)//Проверяет вхождение документ рут в путь. Если его нет, то добавляет.
	{
		if(strpos($path, $_SERVER["DOCUMENT_ROOT"]) !== 0)
		{
			$path = $_SERVER["DOCUMENT_ROOT"].$path;
		}
		return $path;
	}
	
	public function __construct($logPath) {
		if(strpos($logPath, $_SERVER["DOCUMENT_ROOT"]) !== 0)
		{
			$logPath = $_SERVER["DOCUMENT_ROOT"].$logPath;
		}
		$this->logPath = $logPath;
		
		//Открываем поток на файл с которым будем работать или создаём его, если он отсутствует.
		if(file_exists($this->logPath))
		{
			//Открываем если существует
			$file = fopen($this->logPath, "a+");
			
			/*//Разбираем данные из файла и записываем в объект
			$fileContent = fread($file, filesize($this->logPath));
			$fileContentArr = explode ("\n", $fileContent);
			foreach($fileContentArr as $string)
			{
				if($string === "<file_head>")
					$this->explodeStatus = "head";
				if($string === "<file_fields>")
					$this->explodeStatus = "fields";
				if($string === "<file_body>")
				{
					$this->explodeStatus = "body";
					$bodyIter = 0;
				}
					
				if($this->explodeStatus == "head")
				{
					$headFields = explode("\t", $string);
					foreach($headFields as $field)
					{
						$this->head[] = $field;
					}
				}
				unset($headFields);
				
				if($this->explodeStatus == "fields")
				{
					$fields = explode("\t", $string);
					foreach($fields as $field)
					{
						$this->fields[] = $field;
					}
				}
				
				if($this->explodeStatus == "body")
				{
					$bodyValues = explode("\t", $string);
					foreach($bodyValues as $key => $value)
					{
						$this->body[$i][] = $field;
					}
					$bodyIter++;
				}
			}*/
		}
		else
		{
			//Создаём если файла нет
			$file = fopen($logPath, "a+");
		}
		
		$this->startTime = date("d.m.Y H:i:s");
		fwrite($file, "<start_time>\n".date("d.m.Y H:i:s")."\n");
		
		$this->file = $file;
		
	}
	
	public function __destruct() {
		unset($this->logPath);
		fclose($this->file);//Закрываем поток
		unset($this->file);
	}
	
	public function addLog($logString)
	{
		if(fwrite($this->file, $logString."\n"))
			return true;
		else
			return false;
	}
	
	public function ReadCsv($filePath, $separator = ";", $enclosure = '"')
	{
		$filePath = log::drc($filePath);
		
		if(file_exists($filePath))
		{
			if(!$file = fopen($filePath, "r"))
				return false;
		}else
		{
			return false;
		}
		
		if(filesize($filePath) === 0)
			return array();
			
		$content = fread($file, filesize($filePath));
		$tempArr = explode("\n", $content);
		
		$keys = array();
		foreach(explode($separator, array_shift($tempArr)) as $val)
		{
			$keys[] = trim($val);
		}
		
		//$keys = fgetcsv($file, 0, $separator);//Тут мы получаем названия полей
		$csvArr = array();
		$csvIter = 0;
		foreach($tempArr as $ln)
		{
			if($ln === "")
				continue;
				
			$i = 0;
			$lnArr = explode($separator, $ln);
			foreach($lnArr as $value)
			{
				if($value[0] === $enclosure && $value[strlen($value) - 1] === $enclosure)
				{
					$value = substr($value, 1,strlen($value) - 2);
				}
					
				$csvArr[$csvIter][$keys[$i]] = trim($value);
				$i++;
			}
			$csvIter++;
		}
		/*while($ln = fgetcsv($file, 0, $separator))
		{
			$i = 0;
			foreach($ln as $value)
			{
				$csvArr[$csvIter][$keys[$i]] = $value;
				$i++;
			}
			$csvIter++;
		}*/
		//array_pop($csvArr);
		//array_shift($csvArr);
		fclose($file);
		return $csvArr;
	}
	
	public function WriteCsv($filePath, $dataArr, $separator = ';', $enclosure = '"')
	{	
		if(count($dataArr) <= 0)
			return false;
		
		//Если первые элемент не массив, то значит что то не так. Выходим.
		foreach($dataArr as $dataCheck)
		{
			if(!is_array($dataCheck))
				return false;
			break;
		}
		
		$filePath = log::drc($filePath);
		
		//Если файла нет, то создаём его
		if(!file_exists($filePath))
			fclose(fopen($filePath, "x"));
		
		if(file_exists($filePath))
		{
			if($file = fopen($filePath, "w+"))
			{
				$fields = array();
				
				foreach($temp = array_shift($dataArr) as $field => $val)
				{
					$fields[] = $field;
				}
				
				
				array_unshift($dataArr, $temp);
				
				fputcsv($file, $fields, $separator, $enclosure);
				
				
				foreach($dataArr as $data)
				{
					fputcsv($file, $data, $separator, $enclosure);
				}
				
			}else
			{
				return false;
			}
			return true;
		}
		else
			return false;
	}
	
	public function clearLog()
	{
		if($this->file)
		{
			fclose($this->file);
			$this->file = fopen($this->logPath, "w+");
			fwrite($this->file, "<start_time>\n".date("d.m.Y H:i:s")."\n");
			return true;
		}
		else
			return false;
	}
	
	public function getFilePathList($dirPath, $needle)
	{
		if(is_string($needle))
			$needle = array($needle);
		if(!is_array($needle))
			return false;
			
		$resultList = array();
		$recursiveResult = array();
		$dir = opendir($dirPath);
		$fileInDirCheck = false;
		while($filename = readdir($dir))
		{
			if($filename !== "." && $filename !== "..")
			{
				if(is_file($dirPath."/".$filename))
				{
					foreach($needle as $pattern)
					{
						if(strpos($filename, $pattern) !== false)
						{
							$resultList[] = $dirPath."/".$filename;
						}
					}
				}
				
				if(is_dir($dirPath."/".$filename))
				{
					$recursiveResult[] = log::getFilePathList($dirPath."/".$filename, $needle);
				}
			}
		}
		
		if(count($recursiveResult) >= 1)
		{
			foreach($recursiveResult as $result)
			{
				if(is_array($result) && count($result) >= 1)
				{
					foreach($result as $path)
					$resultList[] = $path;
				}
			}
		}
		
		if(is_array($resultList))
			return $resultList;
		else
			return false;
		
	}
	/****************************************************************************************************
	*PRIVATE
	****************************************************************************************************/
	
	
	
}
?>
