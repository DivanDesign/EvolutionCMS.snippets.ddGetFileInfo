<?php
namespace ddGetFileInfo;

class Snippet extends \DDTools\Snippet {
	protected $version = '2.5.1';
	
	protected $params = [
		// Defaults
		'file' => null,
		'file_docField' => null,
		'file_docId' => null,
		'sizeUnitFormat' => 'EnShort',
		'sizePrecision' => 2,
		'output' => 'size',
		'tpl' => null,
		'tpl_placeholders' => null,
	];
		
	protected $paramsTypes = [
		'sizePrecision' => 'integer',
		'tpl_placeholders' => 'objectArray',
	];
		
	protected $renamedParamsCompliance = [
		'file_docField' => 'docField',
		'file_docId' => 'docId',
		'sizeUnitFormat' => [
			'sizeNameFormat',
			'sizeType',
		],
		'sizePrecision' => 'sizePrec',
		'tpl_placeholders' => 'placeholders',
	];
	
	/**
	 * prepareParams
	 * @version 1.1.1 (2024-08-06)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringQueryFormatted}
	 * 
	 * @return {void}
	 */
	protected function prepareParams($params = []){
		// Call base method
		parent::prepareParams($params);
		
		// Backward compatibility
		if (is_numeric($this->params->sizeUnitFormat)){
			$this->params->sizeUnitFormat = strtr(
				$this->params->sizeUnitFormat,
				[
					'-1' => 'none',
					'0' => 'RuShort',
					'1' => 'RuFull',
					'2' => 'EnShort',
				]
			);
		}
		
		$this->params->sizeUnitFormat = strtolower($this->params->sizeUnitFormat);
	}
	
	/**
	 * run
	 * @version 1.0.7 (2025-06-17)
	 * 
	 * @return {string}
	 */
	public function run(){
		// The snippet must return an empty string even if result is absent
		$result = '';
		
		// Получаем имя файла из заданного поля
		if (!empty($this->params->file_docField)){
			$this->params->file = \ddTools::getTemplateVarOutput(
				[
					$this->params->file_docField,
				],
				$this->params->file_docId
			);
			
			$this->params->file = $this->params->file[$this->params->file_docField];
		}
		
		if (!empty($this->params->file)){
			$fileFullPathName = $this->params->file;
			
			// URL
			if (
				filter_var(
					$fileFullPathName,
					FILTER_VALIDATE_URL
				) !==
				false
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
			// File
			}else{
				$isFileUrl = false;
				
				// If file doesn't contain base path
				if (
					substr(
						$fileFullPathName,
						0,
						strlen(\ddTools::$modx->getConfig('base_path'))
					) !=
					\ddTools::$modx->getConfig('base_path')
				){
					// Всегда удаляем слэш слева
					$fileFullPathName = ltrim(
						$fileFullPathName,
						'/'
					);
					
					// Add it
					$fileFullPathName =
						\ddTools::$modx->getConfig('base_path') .
						$fileFullPathName
					;
				}
				
				$isFileExists = file_exists($fileFullPathName);
			}
			
			if ($isFileExists){
				$extensionPos = strrpos(
					$this->params->file,
					'.'
				);
				$dirPos = strrpos(
					$this->params->file,
					'/'
				);
				
				// TODO: Использовать класс «SplFileInfo»
				$snippetResultObject = (object) [
					// Полный адрес файла
					'file' => $this->params->file,
					// Размер
					'size' => '',
					// Расширение
					'extension' => substr(
						$this->params->file,
						$extensionPos + 1
					),
					// «Тип» файла
					'type' => '',
					// Type in MIME format
					'typeMime' => '',
					// Имя файла
					'name' => substr(
						$this->params->file,
						$dirPos + 1,
						$extensionPos - $dirPos - 1
					),
					// Путь к файлу
					'path' => substr(
						$this->params->file,
						0,
						$dirPos
					),
				];
				
				$filesize = false;
				
				if (!$isFileUrl){
					// Пробуем получить размер файла
					$filesize = @filesize($fileFullPathName);
					
					$snippetResultObject->typeMime =
						// If it's SVG
						in_array(
							$snippetResultObject->extension,
							[
								'svg',
								'svgz',
							]
						) ?
						// Assign manually because mime_content_type is not working correct in this case
						'image/svg+xml' :
						// Call default PHP function
						mime_content_type($fileFullPathName)
					;
				}
				
				// Если вышло
				if ($filesize !== false){
					// Формируем строку размера файла
					$snippetResultObject->size = $this->getFileSizeInHumanFormat([
						'size' => $filesize,
						'unitFormat' => $this->params->sizeUnitFormat,
						'precision' => $this->params->sizePrecision,
					]);
				}
				
				// Пытаемся определить тип файла
				$snippetResultObject->type = $this->getFileTypeByExtension($snippetResultObject->extension);
				
				// Если есть tpl, то парсим или возвращаем размер
				if (!empty($this->params->tpl)){
					// Если есть дополнительные данные
					if (!empty($this->params->tpl_placeholders)){
						$snippetResultObject = \DDTools\ObjectTools::extend([
							'objects' => [
								$snippetResultObject,
								$this->params->tpl_placeholders,
							],
						]);
					}
					
					$result = \ddTools::parseText([
						'text' => \ddTools::getTpl($this->params->tpl),
						'data' => $snippetResultObject,
					]);
				}else{
					$result = $snippetResultObject->{$this->params->output};
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * getFileSizeInHumanFormat
	 * @version 1.0.3 (2025-06-17)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringHjsonObject|stringQueryFormatted}
	 * @param $params->size {integer} — File size in bytes.
	 * @param $params->unitFormat {'none'|'EnShort'|'EnFull'|'RuShort'|'RuFull'} — Format of file size unit.
	 * @param $params->precision {integer} — The number of decimal digits to round to.
	 * 
	 * @return {string}
	 */
	private function getFileSizeInHumanFormat($params){
		$params = \DDTools\ObjectTools::convertType([
			'object' => $params,
			'type' => 'objectStdClass'
		]);
		
		// Устанавливаем конфигурацию вывода приставок
		if ($params->unitFormat == 'none'){
			$mas = [
				'',
				'',
				'',
				'',
				'',
				'',
				'',
			];
		}elseif ($params->unitFormat == 'rushort'){
			$mas = [
				' б',
				' Кб',
				' Мб',
				' Гб',
				' Тб',
				' Пб',
				' Эб',
			];
		}elseif ($params->unitFormat == 'rufull'){
			$mas = [
				' байт',
				' Килобайт',
				' Мегабайт',
				' Гигабайт',
				' Терабайт',
				' Петабайт',
				' Эксабайт',
			];
		}elseif ($params->unitFormat == 'enshort'){
			$mas = [
				' B',
				' KB',
				' MB',
				' GB',
				' TB',
				' PB',
				' EB',
			];
		}elseif ($params->unitFormat == 'enfull'){
			$mas = [
				' Bytes',
				' Kilobytes',
				' Megabytes',
				' Gigabytes',
				' Terabytes',
				' Petabytes',
				' Exabytes',
			];
		}
		
		$i = 0;
		while (
			($params->size / 1024)
			>= 1
		){
			$params->size = $params->size / 1024;
			$i++;
		}
		
		return
			round(
				$params->size,
				$params->precision
			)
			. $mas[$i]
		;
	}
	
	/**
	 * getFileTypeByExtension
	 * @version 1.0.0 (2025-06-17)
	 * 
	 * @param $extension {string} — File extension.
	 * 
	 * @return {string}
	 */
	private function getFileTypeByExtension(string $extension) :string {
		$result = '';
		
		switch (strtolower($extension)){
			case 'zip':
			case '7z':
			case 'tar':
			case 'gz':
			case 'rar':
				$result = 'archive';
			break;
			
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'gif':
			case 'bmp':
			case 'tif':
			case 'tiff':
			case 'webp':
				$result = 'image';
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
				$result = 'video';
			break;
			
			case 'flac':
			case 'ape':
			case 'wav':
			case 'aiff':
			case 'wma':
			case 'mp3':
			case 'oga':
				$result = 'audio';
			break;
			
			case 'txt':
				$result = 'text';
			break;
			
			case 'pdf':
				$result = 'pdf';
			break;
			
			case 'doc':
			case 'docx':
				$result = 'word';
			break;
			
			case 'xls':
			case 'xlsx':
			case 'xlsm':
			case 'xlsb':
				$result = 'excel';
			break;
			
			case 'ppt':
			case 'pptx':
			case 'pps':
			case 'ppsx':
				$result = 'powerpoint';
			break;
		}
		
		return $result;
	}
}