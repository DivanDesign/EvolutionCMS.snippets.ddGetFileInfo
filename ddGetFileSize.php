<?php
/**
 * ddGetFileSize.php
 * @version 1.4 (2012-08-13)
 * 
 * Выводит размер файла.
 * 
 * @param file {string} - Имя файла (путь).
 * @param getField {string} - Поле документа, содержащее путь к файлу.
 * @param getId {integer} - Id документа из которого берётся поле.
 * @param getPublished {0; 1} - Опубликован ли документ, поле с файлом которого нужно получить. По умолчанию: 1.
 * @param type {-1, 0, 1, 2} - Тип вывода размера файла. По умолчанию: 0.
 * @param prec {integer} - Количество цифр после запятой. По умолчанию: 2.
 * @param tpl {string: chunkName} - Шаблон для вывода, без шаблона возвращает просто размер. Доступные плэйсхолдеры: [+filesize+] (размер файла), [+ext+] (расширение файла), [+filename+] (имя файла), [+filepath+] (путь к файлу).
 * 
 * @copyright 2012, DivanDesign
 * http://www.DivanDesign.ru
 */

//Получаем имя файла из заданного поля
if (isset($getField)){
	$file = $modx->runSnippet('ddGetDocumentField', array(
		'id' => $getId,
		'published' => $getPublished,
		'field' => $getField
	));
}

//Проверяем на существование файла
if (!file_exists($file)) $file = ltrim($file, '/');

if (isset($file) && $file != '' && file_exists($file)){
	$type = isset($type) ? intval($type) : 0;
	$prec = isset($prec) ? intval($prec) : 2;

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
			while (($size/1024)>=1) {
				$size = $size/1024;
				$i++;
			}
			
			return round($size,$prec).$mas[$i];
		}
	}
	
	//Формируем строку размера файла
	$result = ddfsize_format(filesize($file), $type, $prec);
	
	//Если есть tpl, то парсим или возвращаем размер
	if (isset($tpl)){
		$extPos = strrpos($file, '.');
		$folPos = strrpos($file, '/');
		
		$result = $modx->parseChunk($tpl, array(
			//Размер
			'filesize' => $result,
			//Расширение
			'ext' => substr($file, $extPos + 1),
			//Имя файла
			'filename' => substr($file, $folPos + 1, $extPos - $folPos - 1),
			//Путь к файлу
			'filepath' => substr($file, 0, $folPos),
		), '[+', '+]');
	}

	return $result;
}
?>