# ddGetFileSize
Выводит размер файла.

## Changelog
### 1.6 (2013-08-14)
* \* Внимание! Нарушена обратная совместимость.
* \+ При выводе через шаблон добавлен плэйсхолдер «[+file+]» (полный адрес файла).
* \* При выводе через шаблон плэйсхолдер «[+ext+]» переименован в «[+fileext+]» (для однообразности).
* \- Удалён параметр «getPublished» за ненадобностью.

### 1.5 (2013-01-17)
* \+ Добавлена возможность передавать дополнительные (параметр «placeholders») в чанк «tpl».
* \* Внимание! Сниппет теперь использует библиотеку ddTools 0.4 (при использовании параметра «placeholders»).

### 1.4 (2012-08-13)
* \+ При выводе через шаблон добавлены плэйсхолдеры: «[+filename+]» (имя файла), «[+filepath+]» (путь к файлу).

### 1.3 (2011-06-07)
* \+ Добавлена возможность получения адреса файла из поля заданного документа (добавлены параметры «getId», «getField», «getPublished»).

### 1.2 (2011-04-20)
* \+ Если файл получить не удалось, пробуем отрезать '/' от начала имени.
* \+ Добавлен плейсхолдер «[+ext+]», в который записывается расширение файла при выводе через шаблон.

### 1.0
* \+ Первая версия.

<style>ul{list-style:none;}</style>