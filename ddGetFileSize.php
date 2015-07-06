<?php
/**
 * ddGetFileSize.php
 * @version 1.6 (2013-08-14)
 * 
 * Выводит размер файла.
 * 
 * @uses modx ddTools class 0.8.1.
 * 
 * @param $file {string} - Имя файла (путь). @required
 * @param $getField {string} - Поле документа, содержащее путь к файлу.
 * @param $getId {integer} - Id документа из которого берётся поле.
 * @param $type {-1, 0, 1, 2} - Тип вывода размера файла. По умолчанию: 0.
 * @param $prec {integer} - Количество цифр после запятой. По умолчанию: 2.
 * @param $tpl {string: chunkName} - Шаблон для вывода, без шаблона возвращает просто размер. Доступные плэйсхолдеры: [+filesize+] (размер файла), [+fileext+] (расширение файла), [+file+] (полный адрес файла), [+filename+] (имя файла), [+filepath+] (путь к файлу). По умолчанию: ''.
 * @param $placeholders {separated string} - Дополнительные данные, которые необходимо передать в чанк «tpl». Формат: строка, разделённая '::' между парой ключ-значение и '||' между парами. По умолчанию: ''.
 * 
 * @copyright 2013, DivanDesign
 * http://www.DivanDesign.ru
 */

//Получаем имя файла из заданного поля
if (isset($getField)){
	$file = $modx->runSnippet('ddGetDocumentField', array(
		'id' => $getId,
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
		
		$resArr = array(
			//Полный адрес файла
			'file' => $file,
			//Размер
			'filesize' => $result,
			//Расширение
			'fileext' => substr($file, $extPos + 1),
			//Имя файла
			'filename' => substr($file, $folPos + 1, $extPos - $folPos - 1),
			//Путь к файлу
			'filepath' => substr($file, 0, $folPos),
		);

		//Если есть дополнительные данные
		if (isset($placeholders)){
			//Подключаем modx.ddTools
			require_once $modx->config['base_path'].'assets/snippets/ddTools/modx.ddtools.class.php';

			//Разбиваем их
			$resArr = array_merge($resArr, ddTools::explodeAssoc($placeholders));
		}
		
		$result = $modx->parseChunk($tpl, $resArr, '[+', '+]');
	}

	return $result;
}
?>