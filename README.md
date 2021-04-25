# (MODX)EvolutionCMS.snippets.ddGetFileInfo

Displays information about a file: type, mime, size in readable format, path, name, extension, etc.


## Requires
* PHP >= 5.6
* [(MODX)EvolutionCMS](https://github.com/evolution-cms/evolution) >= 1.1
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.biz/modx/ddtools) >= 0.49


## Documentation


### Installation


#### Manually


##### 1. Elements → Snippets: Create a new snippet with the following data

1. Snippet name: `ddGetFileInfo`.
2. Description: `<b>2.4</b> Displays information about a file: type, mime, size in readable format, path, name, extension, etc.`.
3. Category: `Core`.
4. Parse DocBlock: `no`.
5. Snippet code (php): Insert content of the `ddGetFileInfo_snippet.php` file from the archive.


##### 2. Elements → Manage Files

1. Create a new folder `assets/snippets/ddGetFileInfo/`.
2. Extract the archive to the folder (except `ddGetFileInfo_snippet.php`).


#### Using [(MODX)EvolutionCMS.libraries.ddInstaller](https://github.com/DivanDesign/EvolutionCMS.libraries.ddInstaller)

Just run the following PHP code in your sources or [Console](https://github.com/vanchelo/MODX-Evolution-Ajax-Console):

```php
//Include (MODX)EvolutionCMS.libraries.ddInstaller
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddInstaller/require.php'
);

//Install (MODX)EvolutionCMS.snippets.ddGetFileInfo
\DDInstaller::install([
	'url' => 'https://github.com/DivanDesign/EvolutionCMS.snippets.ddGetFileInfo',
	'type' => 'snippet'
]);
```

* If `ddGetFileInfo` is not exist on your site, `ddInstaller` will just install it.
* If `ddGetFileInfo` is already exist on your site, `ddInstaller` will check it version and update it if needed.


### Parameters description

* `file`
	* Desctription: File name (path).
	* Valid values:
		* `stringFilePath` — the path to the file can be specified relative to the site root (`/` at the beginning does not matter, both variants are supported), or the full path (including `$modx->config['base_path']`)
		* `stringUrl` — you can specify not only a local file, but also an Internet address, but in this case not all functions are supported for objective reasons
	* **Required**
	
* `file_docField`
	* Desctription: A document field (including TV) containing the path to the file (if you want the snippet to get the file address from the document field).
	* Valid values: `string`
	* Default value: —
	
* `file_docId`
	* Desctription: Resource ID, from the field of which you want to get the file address.
	* Valid values: `integerDocId`
	* Default value: —
	
* `sizeUnitFormat`
	* Desctription: Format of file size unit.  
		Values are case insensitive (the following names are equal: `'enshort'`, `'EnShort'`, `'ENSHORT'`, etc).
	* Valid values:
		* `'none'`
		* `'EnShort'` — e. g. `MB`
		* `'EnFull'` — e. g. `Megabyte`
		* `'RuShort'` — e. g. `Мб`
		* `'RuFull'` — e. g. `Мегабайт`
	* Default value: `'EnShort'`
	
* `sizePrecision`
	* Desctription: The number of decimal digits to round to.
	* Valid values: `integer`
	* Default value: `2`
	
* `output`
	* Desctription: File information to output (if `tpl` is not set).
	* Valid values:
		* `'size'`
		* `'extension'`
		* `'type'`
		* `'typeMime'`
		* `'name'`
		* `'path'`
	* Default value: `'size'`
	
* `tpl`
	* Desctription: Output template (if the parameter is absent, file data corresponding to `output` will be returned).  
		Available placeholders:
		* `[+file+]` — full file address
		* `[+name+]` — file name
		* `[+path+]` — file path
		* `[+size+]` — file size with a unit in a human-readable format
		* `[+extension+]` — file extension
		* `[+type+]` — file type:
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