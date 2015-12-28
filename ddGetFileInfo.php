<?php
/**
 * ddGetFileInfo.php
 * @version 2.0 (2014-03-25)
 * 
 * @desc Выводит информацию о фале: размер, имя, расширение и пр.
 * 
 * @uses The library modx.ddTools 0.15.
 * 
 * @param $file {string} - Имя файла (путь). @required
 * @param $docField {string} - Поле документа, содержащее путь к файлу. Default: —.
 * @param $docId {integer} - Id документа из которого берётся поле. Default: —.
 * @param $sizeType {-1, 0, 1, 2} - Тип вывода размера файла. Default: 0.
 * @param $sizePrec {integer} - Количество цифр после запятой. Default: 2.
 * @param $output {'size'; 'extension'; 'type'; 'name'; 'path'} - Что нужно вернуть, если не задан шаблон. Default: 'size'.
 * @param $tpl {string: chunkName} - Шаблон для вывода, без шаблона возвращает просто размер. Доступные плэйсхолдеры: [+file+] (полный адрес файла), [+name+] (имя файла), [+path+] (путь к файлу), [+size+] (размер файла), [+extension+] (расширение файла), [+type+] (тип файла: 'archive', 'image', 'video', 'audio', 'text', 'pdf', 'word', 'excel', 'powerpoint', ''). Default: ''.
 * @param $placeholders {separated string} - Дополнительные данные, которые необходимо передать в чанк «tpl». Формат: строка, разделённая '::' между парой ключ-значение и '||' между парами. Default: ''.
 * 
 * @copyright 2014, DivanDesign
 * http://www.DivanDesign.biz
 */

//Получаем имя файла из заданного поля
if (isset($docField)){
	$file = ddTools::getTemplateVarOutput(array($docField), $docId);
	$file = $file[$docField];
}

$result = '';

if (!empty($file)){
	$output = isset($output) ? $output : 'size';
	
	//Всегда удаляем слэш слева
	$file = ltrim($file, '/');
	
	//Пытаемся открыть файл
	$fileHandle = @fopen($file, 'r');
	
	if ($fileHandle){
		fclose($fileHandle);
		
		$sizeType = isset($sizeType) ? intval($sizeType) : 0;
		$sizePrec = isset($sizePrec) ? intval($sizePrec) : 2;
		
		if (!function_exists('ddfsize_format')){
			function ddfsize_format($size, $type, $prec){
				//устанавливаем конфигурацию вывода приставок, надеюсь разберетесь
				if ($type == -1){
					$mas = array('', '', '', '', '', '', '');
				}else if ($type == 0){
					$mas = array(' б', ' Кб', ' Мб', ' Гб', ' Тб', ' Пб', ' Эб');
				}else if ($type == 1){
					$mas = array(' байт', ' Килобайт', ' Мегабайт', ' Гигабайт', ' Терабайт', ' Петабайт', ' Эксабайт');
				}else if ($type == 2){
					$mas = array(' B', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB');
				}
				
				$i = 0;
				while (($size / 1024) >= 1){
					$size = $size / 1024;
					$i++;
				}
				
				return round($size, $prec).$mas[$i];
			}
		}
		
		$extPos = strrpos($file, '.');
		$folPos = strrpos($file, '/');
		
		$resArr = array(
			//Полный адрес файла
			'file' => $file,
			//Размер
			'size' => '',
			//Расширение
			'extension' => substr($file, $extPos + 1),
			//«Тип» файла
			'type' => '',
			//Имя файла
			'name' => substr($file, $folPos + 1, $extPos - $folPos - 1),
			//Путь к файлу
			'path' => substr($file, 0, $folPos),
		);
		
		//Пробуем получить размер файла
		$filesize = @filesize($file);
		//Если вышло
		if ($filesize){
			//Формируем строку размера файла
			$resArr['size'] = ddfsize_format($filesize, $sizeType, $sizePrec);
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
			if (isset($placeholders)){
				//Подключаем modx.ddTools
				require_once $modx->getConfig('base_path').'assets/libs/ddTools/modx.ddtools.class.php';
				
				//Разбиваем их
				$resArr = array_merge($resArr, ddTools::explodeAssoc($placeholders));
			}
			
			$result = $modx->parseChunk($tpl, $resArr, '[+', '+]');
		}else{
			$result = $resArr[$output];
		}
	}
}

return $result;
?>