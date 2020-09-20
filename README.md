# White Power Info Manager
Система просмотра сайта Web 3.0

Многие сайты не имеют дополнительного функционала и отображаются так, как задумал дизайнер.
Иногда это никуда не годится, и поэтому система позволяет самому решать, что где и как отображать, и какие действия с какой информацией выполнять.

Данная система пока максимально примитивна, но концепция на поверхности.
Работает она пока с одним сайтом, но это не принципиально.
Есть сайт http://www.photoline.ru, он работает с 1999 года. Выглядит так же.

Задача: взять информацию о фото из соответствующего раздела, отобразить с нужном виде и добавить возможность выполнять некоторые действия с этой информацией.

Сделано:
- Отобразить превью фото в заданном диапазоне ширины с динамическим количеством превью в строке.
- По клику на превью открывается полноразмерное фото (закрывается по клику или Esc).
- Отобразить ID фото в виде ссылки на страницу фото на сайте.
- Отобразить имя автора в виде ссылки на страницу автора на сайте.
- Отобразить дату и название.
- Добавить кнопку «Показать дополнительную информацию» (загрузка через fetch+PHP).
- Добавить кнопку «Сохранить» (сохраняет фото по пути, указанном в свойстве класса приложения, http://lifeinfo.pro/w/tool/im/photo/site/photoline.ru/img/).
  Если фото сохранено, то вместо кнопки «Cохранить», отображается кнопка «Удалить».

Сделать:
- Индикация действия fetch.
- Динамическая подгрузка комментариев.
- База данных.
- Кнопки «Присвоить рейтинг», «Добавить в коллекцию», «Скрыть».
- Конструктор, создающий любые флаговые наборы для фото (цвет, рейтинг, флаг, коллекция, жанр, тип, и т. д.).
- Автоматическое добавление информации о фото в базу.


Тестовая версия: http://lifeinfo.pro/w/tool/im/photo/site/photoline.ru/w_photoline.ru.php
