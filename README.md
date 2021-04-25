# (MODX)EvolutionCMS.snippets.ddGetFileInfo

Выводит информацию о фале: размер, имя, расширение и пр.


## Requires
* PHP >= 5.6
* [(MODX)EvolutionCMS](https://github.com/evolution-cms/evolution) >= 1.1
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.biz/modx/ddtools) >= 0.49


## Documentation


### Installation


#### 1. Elements → Snippets: Create a new snippet with the following data

1. Snippet name: `ddGetFileInfo`.
2. Description: `<b>2.4</b> Выводит информацию о фале: размер, имя, расширение и пр.`.
3. Category: `Core`.
4. Parse DocBlock: `no`.
5. Snippet code (php): Insert content of the `ddGetFileInfo_snippet.php` file from the archive.


#### 2. Elements → Manage Files

1. Create a new folder `assets/snippets/ddGetFileInfo/`.
2. Extract the archive to the folder (except `ddGetFileInfo_snippet.php`).


### Parameters description

* `file`
	* Desctription: Имя файла (путь).
	* Valid values:
		* `stringFilePath` — путь к файлу можно указать относительно корня сайта (`/` в начале не играет роли, поддерживаются оба варианта), а можно и полный (включая `$modx->config['base_path']`)
		* `stringUrl` — можно указать не только локальный файл, но и адрес в интернете, но в этом случае по объективным причинам поддерживаются не все функции
	* **Required**
	
* `file_docField`
	* Desctription: Поле документа (включая TV), содержащее путь к файлу (если вы хотите, чтобы сниппет получил адрес файла из поля документа).
	* Valid values: `string`
	* Default value: —
	
* `file_docId`
	* Desctription: ID документа, из поля которого нужно получить адрес файла.
	* Valid values: `integerDocId`
	* Default value: —
	
* `sizeNameFormat`
	* Desctription: Формат вывода названия размера файла.
	* Valid values:
		* `'none'`
		* `'EnShort'` — e. g. `MB`
		* `'EnFull'` — e. g. `Megabyte`
		* `'RuShort'` — e. g. `Мб`
		* `'RuFull'` — e. g. `Мегабайт`
	* Default value: `'EnShort'`
	
* `sizePrecision`
	* Desctription: Количество цифр после запятой.
	* Valid values: `integer`
	* Default value: `2`
	
* `output`
	* Desctription: Что нужно вернуть, если не задан шаблон `tpl`.
	* Valid values:
		* `'size'`
		* `'extension'`
		* `'type'`
		* `'typeMime'`
		* `'name'`
		* `'path'`
	* Default value: `'size'`
	
* `tpl`
	* Desctription: Шаблон для вывода (без шаблона возвращает согласно параметру `output`).
		
		Available placeholders:
		* `[+file+]` — полный адрес файла
		* `[+name+]` — имя файла
		* `[+path+]` — путь к файлу
		* `[+size+]` — размер файла
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
		* `[+typeMime+]` — content type in MIME format (only for local files, not for URLs)
		
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value: —
	
* `tpl_placeholders`
	* Desctription:
		Additional data has to be passed into the `tpl`.  
		Nested objects and arrays are supported too:
		* `{"someOne": "1", "someTwo": "test" }` => `[+someOne+], [+someTwo+]`.
		* `{"some": {"a": "one", "b": "two"} }` => `[+some.a+]`, `[+some.b+]`.
		* `{"some": ["one", "two"] }` => `[+some.0+]`, `[+some.1+]`.
	* Valid values:
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringHjsonObject` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormated` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
		* It can also be set as a native PHP object or array (e. g. for calls through `$modx->runSnippet`):
			* `arrayAssociative`
			* `object`
	* Default value: —


### Examples


#### Run the snippet through `\DDTools\Snippet::runSnippet` without DB and eval

```php
//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);

//Run (MODX)EvolutionCMS.snippets.ddGetFileInfo
\DDTools\Snippet::runSnippet([
	'name' => 'ddGetFileInfo',
	'params' => [
		'file' => 'assets/images/evo-logo.png',
		'output' => 'size'
	]
]);
```


## Links

* [Home page](https://code.divandesign.biz/modx/ddgetfileinfo)
* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-snippets-ddgetfileinfo)


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />