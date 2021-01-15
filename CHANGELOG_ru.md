# (MODX)EvolutionCMS.snippets.ddGetFileInfo changelog


## Версия 2.4 (2021-01-15)
* \+ Возможность возвращать MIME тип файла (см. параметр `tpl`).
* \* Параметры → `tpl`: Некорректный плейсходлер `[+file+]` исправлен.
* \+ README, CHANGELOG: Улучшения стиля.
* \+ README → Документация → Описание параметров → `tpl_placeholders`: Текст улучшен.


## Версия 2.1 (2015-12-28)
* \+ Добавлен вывод типа файла (плэйсхолдер `[+type+]` при выводе через `tpl` и `type` в `output` соответственно). Удобно использовать с [Font Awesome](http://fontawesome.io/).
* \* Нулевой размер файла выводится (не считается ошибкой filesize).
* \* Сниппет `ddGetDocumentField` больше не используется, значение поля документа получается при помощи метода `ddTools::getTemplateVarOutput`.
* \* Вместо прямого обращения к полю `$modx->config` используется метод `$modx->getConfig`.
* \* Внимание! Сниппет использует библиотеку `modx.ddTools` версии 0.15.


## Версия 2.0 (2014-03-25)
* \* Сниппет переименован в `ddGetFileInfo`.
* \* Следующие параметры были переименованы:
	* \* `getField` → `docField`.
	* \* `getId` → `docId`.
	* \* `type` → `sizeType`.
	* \* `prec` → `sizePrec`.
* \* При выводе через шаблон следующие плэйсхолдеры были переименованы:
	* \* `[+filesize+]` → `[+size+]`.
	* \* `[+fileext+]` → `[+extension+]`.
	* \* `[+filename+]` → `[+name+]`.
	* \* `[+filepath+]` → `[+path+]`.
* \+ Добавлен параметр `$output`, позволяющий задать, что именно будет выводиться, если не задан шаблон.


## Версия 1.6.1 (2013-10-23)
* \+ Существование файла теперь проверяется через `fopen`, что позволяет работать с удалёнными файлами.
* \* Если имя файла начинается с сивола `/`, он всегда вырезается.
* \* Если размер файла получить не удалось (например, файл где-то в интернетах), плэйсхолдер `filesize` в чанке `tpl` будет содержать пустую строку.
* \* Рефакторинг.


## Версия 1.6 (2013-08-14)
* \* Внимание! Нарушена обратная совместимость.
* \+ При выводе через шаблон добавлен плэйсхолдер `[+file+]` (полный адрес файла).
* \* При выводе через шаблон плэйсхолдер `[+ext+]` переименован в `[+fileext+]` (для однообразности).
* \- Удалён параметр `getPublished` за ненадобностью.


## Версия 1.5 (2013-01-17)
* \+ Добавлена возможность передавать дополнительные (параметр `placeholders`) в чанк `tpl`.
* \* Внимание! Сниппет теперь использует библиотеку ddTools 0.4 (при использовании параметра `placeholders`).


## Версия 1.4 (2012-08-13)
* \+ При выводе через шаблон добавлены плэйсхолдеры: `[+filename+]` (имя файла), `[+filepath+]` (путь к файлу).


## Версия 1.3 (2011-06-07)
* \+ Добавлена возможность получения адреса файла из поля заданного документа (добавлены параметры `getId`, `getField`, `getPublished`).


## Версия 1.2 (2011-04-20)
* \+ Если файл получить не удалось, пробуем отрезать '/' от начала имени.
* \+ Добавлен плейсхолдер `[+ext+]`, в который записывается расширение файла при выводе через шаблон.


## Версия 1.0 (2010)
* \+ Первый релиз.


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />
<style>ul{list-style:none;}</style>