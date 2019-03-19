<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

$com_info = [
    'menu_name' => lang('Parse_yandex_xml', 'Parse_yandex_xml'), // Menu name
    'description' => '',            // Module Description
    'admin_type' => 'window',       // Open admin class in new window or not. Possible values window/inside
    'window_type' => 'xhr',         // Load method. Possible values xhr/iframe
    'w' => 600,                     // Window width
    'h' => 550,                     // Window height
    'version' => '0.1',             // Module version
    'author' => 'partner@@imagecms.net',  // Author info
    'icon_class' => 'fa fa-ambulance fa-spin'
];

/* Инструкция по модулю:
1. В админке в списке модулей http://karakatica.su/admin/components/modules_tableесть модуль  aquador_parse_xml
2. Главная страница модуля http://karakatica.su/admin/components/init_window/parse_yandex_xml , на ней проходит настройка выгрузки текущего файла,
Важно!!!
модуль предназанчен только работы с файлами  структуры формата Яндекс маркета!. Тоесть, можна импортировать любые файлы, главное, что бы они были такой же структуры:

3. Работа с модулем.   Для вгрызки товаров с файла нужно:  https://prntscr.com/jxpwjx

1.Выбрать категорию магазина, в которой создадутся категории с файла , а в них товары. ИЛИ - 1.1- Можно прямо здесь создать нужную категорию в магазине, в нее все будет грузится с файла.
2.Внести "свое" названия для текущего файла. Только латиница
3.Вставить ссылку на файл, ИЛИ 3.1. - загрузить файл с компютера.
4. Сохранить данные.
5. обновить страницу.
6. Нажав СТАРТ - начинается выгрузка ьтоваров с файла.

В этом блоке проходит только Создание новых товаров.

4. Если дальше нужно обновлять товары из этого файла  , то переходим  в раздел "Список соответсвия поставщиков и файлов"  http://karakatica.su/admin/components/cp/parse_yandex_xml/list_provider_files
 в нем создаем связь указааного поставщика с файлом . https://prntscr.com/jxpx9y
Создали связь, и далее, можно вручную перейдя по ссылке  (или настроить планировщик на сервере) http://karakatica.su/parse_yandex_xml/cron_updete/update/provider   обновлять товары.

ВАЖНО:
вместо provider   нужно ставить название поставщика , в вашем текущем случае - это
http://karakatica.su/parse_yandex_xml/cron_updete/update/provider1


5. Если нужно выгрузить другой файл (создать товары)
вносите другого поставщика сюда https://prntscr.com/jxpx9y

его ссылка для обновления будет http://karakatica.su/parse_yandex_xml/cron_updete/update/XXX уже (вместо ХХХ . конечно . названия, которое установите в списке.)*/