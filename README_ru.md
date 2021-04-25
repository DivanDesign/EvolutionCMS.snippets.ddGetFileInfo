# (MODX)EvolutionCMS.snippets.ddGetFileInfo

Выводит информацию о файле: тип, mime, размер в читабельном формате, путь, имя, расширение и пр.


## Использует
* PHP >= 5.6
* [(MODX)EvolutionCMS](https://github.com/evolution-cms/evolution) >= 1.1
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.ru/modx/ddtools) >= 0.49


## Документация


### Установка


#### Вручную


##### 1. Элементы → Сниппеты: Создайте новый сниппет со следующими параметрами

1. Название сниппета: `ddGetFileInfo`.
2. Описание: `<b>2.4</b> Выводит информацию о фале: размер, имя, расширение и пр.`.
3. Категория: `Core`.
4. Анализировать DocBlock: `no`.
5. Код сниппета (php): Вставьте содержимое файла `ddGetFileInfo_snippet.php` из архива.


##### 2. Элементы → Управление файлами

1. Создайте новую папку `assets/snippets/ddGetFileInfo/`.
2. Извлеките содержимое архива в неё (кроме файла `ddGetFileInfo_snippet.php`).


#### Используя [(MODX)EvolutionCMS.libraries.ddInstaller](https://github.com/DivanDesign/EvolutionCMS.libraries.ddInstaller)

Просто вызовите следующий код в своих исходинках или модуле [Console](https://github.com/vanchelo/MODX-Evolution-Ajax-Console):

```php
//Подключение (MODX)EvolutionCMS.libraries.ddInstaller
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddInstaller/require.php'
);

//Установка (MODX)EvolutionCMS.snippets.ddGetFileInfo
\DDInstaller::install([
	'url' => 'https://github.com/DivanDesign/EvolutionCMS.snippets.ddGetFileInfo',
	'type' => 'snippet'
]);
```

* Если `ddGetFileInfo` отсутствует на вашем сайте, `ddInstaller` просто установит его.
* Если `ddGetFileInfo` уже есть на вашем сайте, `ddInstaller` проверит его версию и обновит, если нужно. 


### Описание параметров

* `file`
	* Описание: Имя файла (путь).
	* Допустимые значения:
		* `stringFilePath` — путь к файлу можно указать относительно корня сайта (`/` в начале не играет роли, поддерживаются оба варианта), а можно и полный (включая `$modx->config['base_path']`)
		* `stringUrl` — можно указать не только локальный файл, но и адрес в интернете, но в этом случае по объективным причинам поддерживаются не все функции
	* **Обязателен**
	
* `file_docField`
	* Описание: Поле документа (включая TV), содержащее путь к файлу (если вы хотите, чтобы сниппет получил адрес файла из поля документа).
	* Допустимые значения: `string`
	* Значение по умолчанию: —
	
* `file_docId`
	* Описание: ID документа, из поля которого нужно получить адрес файла.
	* Допустимые значения: `integerDocId`
	* Значение по умолчанию: —
	
* `sizeUnitFormat`
	* Описание: Формат вывода единицы измерения размера файла.  
		Значения регистронезависимы (следующие значения равны: `'enshort'`, `'EnShort'`, `'ENSHORT'` и т. п.).
	* Допустимые значения:
		* `'none'`
		* `'EnShort'` — e. g. `MB`
		* `'EnFull'` — e. g. `Megabyte`
		* `'RuShort'` — e. g. `Мб`
		* `'RuFull'` — e. g. `Мегабайт`
	* Значение по умолчанию: `'EnShort'`
	
* `sizePrecision`
	* Описание: Количество цифр после запятой.
	* Допустимые значения: `integer`
	* Значение по умолчанию: `2`
	
* `output`
	* Описание: Какую информацию о файле вывести (если не задан шаблон `tpl`).
	* Допустимые значения:
		* `'size'`
		* `'extension'`
		* `'type'`
		* `'typeMime'`
		* `'name'`
		* `'path'`
	* Значение по умолчанию: `'size'`
	
* `tpl`
	* Описание: Шаблон для вывода (без шаблона возвращает согласно параметру `output`).  
		Доступные плейсхолдеры:
		* `[+file+]` — полный адрес файла
		* `[+name+]` — имя файла
		* `[+path+]` — путь к файлу
		* `[+size+]` — размер файла с единицей измерения в удобочитаемом формате
		* `[+extension+]` — расширение файла
		* `[+type+]` — тип файла:
			* `'archive'`
			* `'image'`
			* `'video'`
			* `'audio'`
			* `'text'`
			* `'pdf'`
			* `'word'`
			* `'excel'`
			* `'powerpoint'`
		* `[+typeMime+]` — тип содержимого в формате MIME (только для локальных файлов, не для URL-адресов)
	* Допустимые значения:
		* `stringChunkName`
		* `string` — передавать код напрямую без чанка можно начиная значение с `@CODE:`
	* Значение по умолчанию: —
	
* `tpl_placeholders`
	* Описание:
		Дополнительные данные, которые будут переданы в шаблон `tpl`.  
		Вложенные объекты и массивы также поддерживаются:
		* `{"someOne": "1", "someTwo": "test" }` => `[+someOne+], [+someTwo+]`.
		* `{"some": {"a": "one", "b": "two"} }` => `[+some.a+]`, `[+some.b+]`.
		* `{"some": ["one", "two"] }` => `[+some.0+]`, `[+some.1+]`.
	* Допустимые значения:
		* `stringJsonObject` — в виде [JSON](https://ru.wikipedia.org/wiki/JSON)
		* `stringHjsonObject` — в виде [HJSON](https://hjson.github.io/)
		* `stringQueryFormated` — в виде [Query string](https://en.wikipedia.org/wiki/Query_string)
		* Также может быть задан, как нативный PHP объект или массив (например, для вызовов через `$modx->runSnippet`).
			* `arrayAssociative`
			* `object`
	* Значение по умолчанию: —


### Примеры


#### Запустить сниппет через `\DDTools\Snippet::runSnippet` без DB и eval

```php
//Подключение (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);

//Запуск (MODX)EvolutionCMS.snippets.ddGetFileInfo
\DDTools\Snippet::runSnippet([
	'name' => 'ddGetFileInfo',
	'params' => [
		'file' => 'assets/images/evo-logo.png',
		'output' => 'size'
	]
]);
```


## Ссылки

* [Home page](https://code.divandesign.ru/modx/ddgetfileinfo)
* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-snippets-ddgetfileinfo)


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />