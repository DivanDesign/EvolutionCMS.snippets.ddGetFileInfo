<?php
/**
 * ddGetFileInfo
 * @version 2.4 (2021-01-15)
 * 
 * @see README.md
 * 
 * @copyright 2010–2021 DD Group {@link https://DivanDesign.biz }
 */

//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);


//Renaming params with backward compatibility
$params = extract(\ddTools::verifyRenamedParams([
	'params' => $params,
	'compliance' => [
		'file_docField' => 'docField',
		'file_docId' => 'docId',
		'sizeNameFormat' => 'sizeType',
		'sizePrecision' => 'sizePrec',
		'tpl_placeholders' => 'placeholders'
	],
	'returnCorrectedOnly' => false
]));

$params = \DDTools\ObjectTools::extend([
	'objects' => [
		//Defaults
		(object) [
			'file' => null,
			'file_docField' => null,
			'file_docId' => null,
			'sizeNameFormat' => 'EnShort',
			'sizePrecision' => 2,
			'output' => 'size',
			'tpl' => null,
			'tpl_placeholders' => null
		],
		$params
	]
]);

$params->sizePrecision = intval($params->sizePrecision);


//The snippet must return an empty string even if result is absent
$snippetResult = '';

//Получаем имя файла из заданного поля
if (!empty($params->file_docField)){
	$params->file = \ddTools::getTemplateVarOutput(
		[$params->file_docField],
		$params->file_docId
	);
	$params->file = $params->file[$params->file_docField];
}

if (!empty($params->file)){
	$fileFullPathName = $params->file;
	
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
		//Backward compatibility
		if (is_numeric($params->sizeNameFormat)){
			$params->sizeNameFormat = strtr(
				$params->sizeNameFormat,
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
				}elseif ($type == 'RuShort'){
					$mas = [
						' б',
						' Кб',
						' Мб',
						' Гб',
						' Тб',
						' Пб',
						' Эб'
					];
				}elseif ($type == 'RuFull'){
					$mas = [
						' байт',
						' Килобайт',
						' Мегабайт',
						' Гигабайт',
						' Терабайт',
						' Петабайт',
						' Эксабайт'
					];
				}elseif ($type == 'EnShort'){
					$mas = [
						' B',
						' KB',
						' MB',
						' GB',
						' TB',
						' PB',
						' EB'
					];
				}elseif ($type == 'EnFull'){
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
			$params->file,
			'.'
		);
		$dirPos = strrpos(
			$params->file,
			'/'
		);
		
		//TODO: Использовать класс «SplFileInfo»
		$snippetResultArray = [
			//Полный адрес файла
			'file' => $params->file,
			//Размер
			'size' => '',
			//Расширение
			'extension' => substr(
				$params->file,
				$extensionPos + 1
			),
			//«Тип» файла
			'type' => '',
			//Type in MIME format
			'typeMime' => '',
			//Имя файла
			'name' => substr(
				$params->file,
				$dirPos + 1,
				$extensionPos - $dirPos - 1
			),
			//Путь к файлу
			'path' => substr(
				$params->file,
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
				$params->sizeNameFormat,
				$params->sizePrecision
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
		if (!empty($params->tpl)){
			//Если есть дополнительные данные
			if (!empty($params->tpl_placeholders)){
				$params->tpl_placeholders = \ddTools::encodedStringToArray($params->tpl_placeholders);
				//Unfold for arrays support (e. g. “{"somePlaceholder1": "test", "somePlaceholder2": {"a": "one", "b": "two"} }” => “[+somePlaceholder1+]”, “[+somePlaceholder2.a+]”, “[+somePlaceholder2.b+]”; “{"somePlaceholder1": "test", "somePlaceholder2": ["one", "two"] }” => “[+somePlaceholder1+]”, “[+somePlaceholder2.0+]”, “[somePlaceholder2.1]”)
				$params->tpl_placeholders = \ddTools::unfoldArray($params->tpl_placeholders);
				
				$snippetResultArray = \DDTools\ObjectTools::extend([
					'objects' => [
						$snippetResultArray,
						$params->tpl_placeholders
					]
				]);
			}
			
			$snippetResult = \ddTools::parseText([
				'text' => $modx->getTpl($params->tpl),
				'data' => $snippetResultArray
			]);
		}else{
			$snippetResult = $snippetResultArray[$params->output];
		}
	}
}

return $snippetResult;
?>