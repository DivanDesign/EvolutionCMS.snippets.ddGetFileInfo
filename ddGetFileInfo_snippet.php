<?php
/**
 * ddGetFileInfo
 * @version 2.3 (2019-12-12)
 * 
 * @see README.md
 * 
 * @copyright 2010–2019 DD Group {@link https://DivanDesign.biz }
 */

//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);

//The snippet must return an empty string even if result is absent
$snippetResult = '';

//Backward compatibility
extract(\ddTools::verifyRenamedParams(
	$params,
	[
		'file_docField' => 'docField',
		'file_docId' => 'docId',
		'sizeNameFormat' => 'sizeType',
		'sizePrecision' => 'sizePrec',
		'tpl_placeholders' => 'placeholders'
	]
));

//Получаем имя файла из заданного поля
if (isset($file_docField)){
	$file = \ddTools::getTemplateVarOutput(
		[$file_docField],
		$file_docId
	);
	$file = $file[$file_docField];
}

if (!empty($file)){
	$output =
		isset($output) ?
		$output :
		'size'
	;
	
	$fileFullPathName = $file;
	
	//URL
	if (
		filter_var(
			$fileFullPathName,
			FILTER_VALIDATE_URL
		) !== false
	){
		$isFileUrl = true;
		
		$isFileExists =
			stripos(
				get_headers($fileFullPathName)[0],
				'200 OK'
			) ?
			true :
			false
		;
	//File
	}else{
		$isFileUrl = false;
		
		//If file doesn't contain base path
		if (
			substr(
				$fileFullPathName,
				0,
				strlen($modx->getConfig('base_path'))
			) != $modx->getConfig('base_path')
		){
			//Всегда удаляем слэш слева
			$fileFullPathName = ltrim(
				$fileFullPathName,
				'/'
			);
			
			//Add it
			$fileFullPathName =
				$modx->getConfig('base_path') .
				$fileFullPathName
			;
		}
		
		$isFileExists = file_exists($fileFullPathName);
	}
	
	if ($isFileExists){
		$sizeNameFormat =
			isset($sizeNameFormat) ?
			$sizeNameFormat :
			'EnShort'
		;
		$sizePrecision =
			isset($sizePrecision) ?
			intval($sizePrecision) :
			2
		;
		
		//Backward compatibility
		if (is_numeric($sizeNameFormat)){
			$sizeNameFormat = strtr(
				$sizeNameFormat,
				[
					'-1' => 'none',
					'0' => 'RuShort',
					'1' => 'RuFull',
					'2' => 'EnShort',
				]
			);
		}
		
		if (!function_exists('ddfsize_format')){
			function ddfsize_format(
				$size,
				$type,
				$prec
			){
				//устанавливаем конфигурацию вывода приставок, надеюсь разберетесь
				if ($type == 'none'){
					$mas = [
						'',
						'',
						'',
						'',
						'',
						'',
						''
					];
				}else if ($type == 'RuShort'){
					$mas = [
						' б',
						' Кб',
						' Мб',
						' Гб',
						' Тб',
						' Пб',
						' Эб'
					];
				}else if ($type == 'RuFull'){
					$mas = [
						' байт',
						' Килобайт',
						' Мегабайт',
						' Гигабайт',
						' Терабайт',
						' Петабайт',
						' Эксабайт'
					];
				}else if ($type == 'EnShort'){
					$mas = [
						' B',
						' KB',
						' MB',
						' GB',
						' TB',
						' PB',
						' EB'
					];
				}else if ($type == 'EnFull'){
					$mas = [
						' Bytes',
						' Kilobytes',
						' Megabytes',
						' Gigabytes',
						' Terabytes',
						' Petabytes',
						' Exabytes'
					];
				}
				
				$i = 0;
				while (($size / 1024) >= 1){
					$size = $size / 1024;
					$i++;
				}
				
				return
					round(
						$size,
						$prec
					) .
					$mas[$i]
				;
			}
		}
		
		$extensionPos = strrpos(
			$file,
			'.'
		);
		$dirPos = strrpos(
			$file,
			'/'
		);
		
		//TODO: Использовать класс «SplFileInfo»
		$snippetResultArray = [
			//Полный адрес файла
			'file' => $file,
			//Размер
			'size' => '',
			//Расширение
			'extension' => substr(
				$file,
				$extensionPos + 1
			),
			//«Тип» файла
			'type' => '',
			//Type in MIME format
			'typeMime' => '',
			//Имя файла
			'name' => substr(
				$file,
				$dirPos + 1,
				$extensionPos - $dirPos - 1
			),
			//Путь к файлу
			'path' => substr(
				$file,
				0,
				$dirPos
			),
		];
		
		$filesize = false;
		
		if (!$isFileUrl){
			//Пробуем получить размер файла
			$filesize = @filesize($fileFullPathName);
			
			$snippetResultArray['typeMime'] =
				//If it's SVG
				in_array(
					$snippetResultArray['extension'],
					[
						'svg',
						'svgz'
					]
				) ?
				//Assign manually because mime_content_type is not working correct in this case
				'image/svg+xml' :
				//Call default PHP function
				mime_content_type($fileFullPathName)
			;
		}
		
		//Если вышло
		if ($filesize !== false){
			//Формируем строку размера файла
			$snippetResultArray['size'] = ddfsize_format(
				$filesize,
				$sizeNameFormat,
				$sizePrecision
			);
		}
		
		//Пытаемся определить тип файла
		switch (strtolower($snippetResultArray['extension'])){
			case 'zip':
			case '7z':
			case 'tar':
			case 'gz':
			case 'rar':
				$snippetResultArray['type'] = 'archive';
			break;
			
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'gif':
			case 'bmp':
			case 'tif':
			case 'tiff':
			case 'webp':
				$snippetResultArray['type'] = 'image';
			break;
			
			case 'webm':
			case 'mkv':
			case 'ogv':
			case 'avi':
			case 'wmv':
			case 'flv':
			case 'mpg':
			case 'mpeg':
			case 'mp4':
			case 'm4v':
				$snippetResultArray['type'] = 'video';
			break;
			
			case 'flac':
			case 'ape':
			case 'wav':
			case 'aiff':
			case 'wma':
			case 'mp3':
			case 'oga':
				$snippetResultArray['type'] = 'audio';
			break;
			
			case 'txt':
				$snippetResultArray['type'] = 'text';
			break;
			
			case 'pdf':
				$snippetResultArray['type'] = 'pdf';
			break;
			
			case 'doc':
			case 'docx':
				$snippetResultArray['type'] = 'word';
			break;
			
			case 'xls':
			case 'xlsx':
			case 'xlsm':
			case 'xlsb':
				$snippetResultArray['type'] = 'excel';
			break;
			
			case 'ppt':
			case 'pptx':
			case 'pps':
			case 'ppsx':
				$snippetResultArray['type'] = 'powerpoint';
			break;
		}
		
		//Если есть tpl, то парсим или возвращаем размер
		if (isset($tpl)){
			//Если есть дополнительные данные
			if (isset($tpl_placeholders)){
				$tpl_placeholders = \ddTools::encodedStringToArray($tpl_placeholders);
				//Unfold for arrays support (e. g. “{"somePlaceholder1": "test", "somePlaceholder2": {"a": "one", "b": "two"} }” => “[+somePlaceholder1+]”, “[+somePlaceholder2.a+]”, “[+somePlaceholder2.b+]”; “{"somePlaceholder1": "test", "somePlaceholder2": ["one", "two"] }” => “[+somePlaceholder1+]”, “[+somePlaceholder2.0+]”, “[somePlaceholder2.1]”)
				$tpl_placeholders = \ddTools::unfoldArray($tpl_placeholders);
				
				//Разбиваем их
				$snippetResultArray = array_merge(
					$snippetResultArray,
					$tpl_placeholders
				);
			}
			
			$snippetResult = \ddTools::parseText([
				'text' => $modx->getTpl($tpl),
				'data' => $snippetResultArray
			]);
		}else{
			$snippetResult = $snippetResultArray[$output];
		}
	}
}

return $snippetResult;
?>