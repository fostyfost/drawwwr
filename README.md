# DRAWWWR
> Рисуй, делись и смотри творчество других.

Модуль для CMS 1С-Битрикс.

## Минимальные системные требования
  - 1С-Битрикс версии 17.5
  - 1C-Битрикс редакции "Старт"
  - PHP версии 5.6
  - Любой web-сервер, совместимый с 1С-Битрикс
  - Современный браузер

## Установка
1. Установить 1С-Битрикс
2. На шаге с выбором решения для установки перейти в панель администрирования
3. Перейти в **Рабочий стол > Настройки > Настройки продукта > Настройки модулей > Настройки главного модуля**
4. На вкладке "Aвторизация" **сгенерировать ключ шифрования** и **установить флажок "Передавать пароль в зашифрованном виде"**
5. Скопировать папку **fostyfost.drawwwr** в **local/modules/**
6. При необходимости установить обновления
7. Перейти в **Рабочий стол > Marketplace > Установленные решения**
8. Установить модуль
9. **Запустить мастер**
10. Нажать **Настройки решнеия**
11. Нажать **Далее**
12. Нажать **Перейти на сайт**
13. При необходимости установить шаблон сайта
14. ...
15. **PROFIT**

## Фичи
* Комплексный компонент, включающий в себя компонент списка, компонент создания изображения, компонент редактирования
 изображения
* Компонент дешифрации по паролю
* Компонент постраничной навигации
* .h5i-файлы для хранения информации о слоях и всех изображениях на них
* Шифрование паролей перед передачей на сервер
* Шифрование исходных файлов .h5i перед передачей на сервер
* Дешифрование файла на клиенте
* Автоматическая настройка при установке
* Полное удаление модуля и созданных с ним инфоблоков
* Установка сразу в режиме ЧПУ
* D7 там, где это стабильно и возможно
* Адаптивность
* Кастомный дебагер для вывода на экран, в консоль, в файл, с фала на экран, с файла в консоль
* Для показа панели в публичной части сайта необходимо добавить параметр адресной строки **SHOW_PANEL=Y**
* Для выноса панели на передний план необходимо добавить параметр адресной строки **BX_PANEL_Z_INDEX=9999999**
* Для отмены предупреждений шаблонов компонентов о включенном режиме правки необходимо добавить параметр адресной 
строки **DISABLE_CHECK=Y**
* Сброс кеша компонента списка при обновлении, редактировании или добавлении нового рисунка

## Список технологий, плагинов и т.п.
* CSS3
* HTML5 API
* Canvas
* jQuery
* jQuery UI
* CryptoJS
* HTML5 Drawing studio
* Font Awesome
* jQuery Light Gallery
* BBC
* RSVP.js
* Lo-Dash
* Blob.js
* canvas-toBlob.js

## Лицензия
MIT

