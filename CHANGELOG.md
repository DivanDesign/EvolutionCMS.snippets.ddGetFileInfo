# (MODX)EvolutionCMS.snippets.ddGetFileInfo changelog


## Version 2.3 (2019-12-12)
* \+ If `file` doesn't contain base path, the snippet will add it.
* \* `fopen` is not used anymore because `@` operator doesn't always work.
* \* `filesize` is used only for local files, not for URLs. `@` steel used because not only URLs will generate errors.


## Version 2.2.1 (2018-11-24)
* \* Critical variable name error was fixed.


## Version 2.2 (2018-11-24)
* \* Attention! PHP >= 5.4 is required.
* \* Attention! MODXEvo >= 1.1 is required.
* \* Attention! MODXEvo.libraries.ddTools >= 0.18 is required.
* \+ Added JSON and Query string formats support for the `tpl_placeholders` parameter (with backward compatibility).
* \+ Added support of the `@CODE:` keyword prefix in the `tpl` parameter.
* \* The following parameters were renamed (with backward compatibility):
	* \* `docField` → `file_docField`.
	* \* `docId` → `file_docId`.
	* \* `sizeType` → `sizeNameFormat`.
	* \* `sizePrec` → `sizePrecision`.
	* \* `placeholders` → `tpl_placeholders`.
* \* The `sizeNameFormat` parameter changes:
	* \* User-friendly values.
	* \+ Added `EnFull` value.
	* \* Is equal to `EnShort` by default.


## Version 1.0 (2010)
* \+ The first release.


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />
<style>ul{list-style:none;}</style>