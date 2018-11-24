<?php
/**
 * ddGetFileInfo
 * @version 2.1 (2015-12-28)
 * 
 * @desc Выводит информацию о фале: размер, имя, расширение и пр.
 * 
 * @uses PHP >= 5.4.
 * @uses MODXEvo.libraries.ddTools >= 0.18.
 * 
 * @param $file {string} — Имя файла (путь). @required
 * @param $file_docField {string} — Поле документа, содержащее путь к файлу. Default: —.
 * @param $file_docId {integer} — Id документа из которого берётся поле. Default: —.
 * @param $sizeNameFormat {-1|0|1|2} — Тип вывода размера файла. Default: 0.
 * @param $sizePrecision {integer} — Количество цифр после запятой. Default: 2.
 * @param $output {'size'|'extension'|'type'|'name'|'path'} — Что нужно вернуть, если не задан шаблон. Default: 'size'.
 * @param $tpl {string_chunkName} — Шаблон для вывода, без шаблона возвращает просто размер. Доступные плэйсхолдеры: [+file+] (полный адрес файла), [+name+] (имя файла), [+path+] (путь к файлу), [+size+] (размер файла), [+extension+] (расширение файла), [+type+] (тип файла: 'archive', 'image', 'video', 'audio', 'text', 'pdf', 'word', 'excel', 'powerpoint', ''). Default: —.
 * @param $tpl_placeholders {stirng_json|string_queryFormated} — Additional data as JSON (https://en.wikipedia.org/wiki/JSON) or Query string (https://en.wikipedia.org/wiki/Query_string) has to be passed into “tpl”. Default: ''.
 * @example &tpl_placeholders=`{"pladeholder1": "value1", "pagetitle": "My awesome pagetitle!"}`
 * 
 * @copyright 2010–2015 DivanDesign {@link http://www.DivanDesign.biz }
 */

//Include MODXEvo.libraries.ddTools
require_once $modx->getConfig('base_path').'assets/libs/ddTools/modx.ddtools.class.php';

//Backward compatibility
extract(ddTools::verifyRenamedParams(
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
	$file = ddTools::getTemplateVarOutput(
		[$file_docField],
		$file_docId
	);
	$file = $file[$file_docField];
}

$result = '';

if (!empty($file)){
	$output = isset($output) ? $output : 'size';
	
	//Всегда удаляем слэш слева
	$file = ltrim(
		$file,
		'/'
	);
	
	//Пытаемся открыть файл
	$fileHandle = @fopen(
		$file,
		'r'
	);
	
	if ($fileHandle){
		fclose($fileHandle);
		
		$sizeNameFormat = isset($sizeNameFormat) ? intval($sizeNameFormat) : 0;
		$sizePrecision = isset($sizePrecision) ? intval($sizePrecision) : 2;
		
		//TODO: Переделать на какие-то человеко-понятные ключи
		if (!function_exists('ddfsize_format')){
			function ddfsize_format(
				$size,
				$type,
				$prec
			){
				//устанавливаем конфигурацию вывода приставок, надеюсь разберетесь
				if ($type == -1){
					$mas = [
						'',
						'',
						'',
						'',
						'',
						'',
						''
					];
				}else if ($type == 0){
					$mas = [
						' б',
						' Кб',
						' Мб',
						' Гб',
						' Тб',
						' Пб',
						' Эб'
					];
				}else if ($type == 1){
					$mas = [
						' байт',
						' Килобайт',
						' Мегабайт',
						' Гигабайт',
						' Терабайт',
						' Петабайт',
						' Эксабайт'
					];
				}else if ($type == 2){
					$mas = [
						' B',
						' KB',
						' MB',
						' GB',
						' TB',
						' PB',
						' EB'
					];
				}
				
				$i = 0;
				while (($size / 1024) >= 1){
					$size = $size / 1024;
					$i++;
				}
				
				return round(
					$size,
					$prec
				).$mas[$i];
			}
		}
		
		$extPos = strrpos(
			$file,
			'.'
		);
		$folPos = strrpos(
			$file,
			'/'
		);
		
		//TODO: Использовать класс «SplFileInfo»
		$resArr = [
			//Полный адрес файла
			'file' => $file,
			//Размер
			'size' => '',
			//Расширение
			'extension' => substr(
				$file,
				$extPos + 1
			),
			//«Тип» файла
			'type' => '',
			//Имя файла
			'name' => substr(
				$file,
				$folPos + 1,
				$extPos - $folPos - 1
			),
			//Путь к файлу
			'path' => substr(
				$file,
				0,
				$folPos
			),
		];
		
		//Пробуем получить размер файла
		$filesize = @filesize($file);
		//Если вышло
		if ($filesize !== false){
			//Формируем строку размера файла
			$resArr['size'] = ddfsize_format(
				$filesize,
				$sizeNameFormat,
				$sizePrecision
			);
		}
		
		//Пытаемся определить тип файла
		switch (strtolower($resArr['extension'])){
			case 'zip':
			case '7z':
			case 'tar':
			case 'gz':
			case 'rar':
				$resArr['type'] = 'archive';
			break;
			
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'gif':
			case 'bmp':
			case 'tif':
			case 'tiff':
			case 'webp':
				$resArr['type'] = 'image';
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
				$resArr['type'] = 'video';
			break;
			
			case 'flac':
			case 'ape':
			case 'wav':
			case 'aiff':
			case 'wma':
			case 'mp3':
			case 'oga':
				$resArr['type'] = 'audio';
			break;
			
			case 'txt':
				$resArr['type'] = 'text';
			break;
			
			case 'pdf':
				$resArr['type'] = 'pdf';
			break;
			
			case 'doc':
			case 'docx':
				$resArr['type'] = 'word';
			break;
			
			case 'xls':
			case 'xlsx':
			case 'xlsm':
			case 'xlsb':
				$resArr['type'] = 'excel';
			break;
			
			case 'ppt':
			case 'pptx':
			case 'pps':
			case 'ppsx':
				$resArr['type'] = 'powerpoint';
			break;
		}
		
		//Если есть tpl, то парсим или возвращаем размер
		if (isset($tpl)){
			//Если есть дополнительные данные
			if (isset($tpl_placeholders)){
				$tpl_placeholders = ddTools::encodedStringToArray($tpl_placeholders);
				//Unfold for arrays support (e. g. “{"somePlaceholder1": "test", "somePlaceholder2": {"a": "one", "b": "two"} }” => “[+somePlaceholder1+]”, “[+somePlaceholder2.a+]”, “[+somePlaceholder2.b+]”; “{"somePlaceholder1": "test", "somePlaceholder2": ["one", "two"] }” => “[+somePlaceholder1+]”, “[+somePlaceholder2.0+]”, “[somePlaceholder2.1]”)
				$tpl_placeholders = ddTools::unfoldArray($tpl_placeholders);
				
				//Разбиваем их
				$resArr = array_merge(
					$resArr,
					$tpl_placeholders
				);
			}
			
			$result = $modx->parseChunk(
				$tpl,
				$resArr,
				'[+',
				'+]'
			);
		}else{
			$result = $resArr[$output];
		}
	}
}

return $result;
?>