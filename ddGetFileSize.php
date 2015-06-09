<?php
//ddGetFileSize.php v1.0
//Выводит размер файла
//file - имя файла
//tpl -  шаблон для вывода.
//type - тип вывода размера файла. Доступные значения: -1, 0, 1, 2. По умолчанию - 0.
//prec - количество цифр после запятой. По умолчанию - 2.

//Сниппет разработан студией Диван.Дизайн (www.divandesign.ru)

if (isset($file) && $file != "" && file_exists($file)){
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

	$result = ddfsize_format(filesize($file), $type, $prec);

	if (isset($tpl)){
		$result = $modx->parseChunk($tpl,array('filesize' => $result),'[+','+]');
	}

	return $result;
}
?>