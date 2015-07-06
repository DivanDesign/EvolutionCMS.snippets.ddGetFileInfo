<?php
/**
 * ddGetFileInfo.php
 * @version 2.0 (2014-03-25)
 * 
 * @desc Выводит информацию о фале: размер, имя, расширение и пр.
 * 
 * @uses The library modx.ddTools 0.13.
 * 
 * @param $file {string} - Имя файла (путь). @required
 * @param $docField {string} - Поле документа, содержащее путь к файлу. Default: —.
 * @param $docId {integer} - Id документа из которого берётся поле. Default: —.
 * @param $sizeType {-1, 0, 1, 2} - Тип вывода размера файла. Default: 0.
 * @param $sizePrec {integer} - Количество цифр после запятой. Default: 2.
 * @param $output {'size'; 'extension'; 'name'; 'path'} - Что нужно вернуть, если не задан шаблон. Default: 'size'.
 * @param $tpl {string: chunkName} - Шаблон для вывода, без шаблона возвращает просто размер. Доступные плэйсхолдеры: [+size+] (размер файла), [+extension+] (расширение файла), [+file+] (полный адрес файла), [+name+] (имя файла), [+path+] (путь к файлу). Default: ''.
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
		
		//Если есть tpl, то парсим или возвращаем размер
		if (isset($tpl)){
			//Если есть дополнительные данные
			if (isset($placeholders)){
				//Подключаем modx.ddTools
				require_once $modx->config['base_path'].'assets/snippets/ddTools/modx.ddtools.class.php';
				
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