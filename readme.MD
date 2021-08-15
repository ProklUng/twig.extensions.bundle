# Бандл с набором расширений для Twig

**INTERNAL**

## Установка

`composer require proklung/twig-extensions-bundle`

## Конфигурация

Файл `local/configs/packages/twig_extension_pack.yaml`:

```yaml
twig_extension_pack:
  # Путь к dev сборке Webpack
  webpack_build_dev_path: 'local/build/'
  # Путь к production сборке Webpack
  webpack_build_production_path: 'local/dist/'
  # Сервис кэшера (реализующий Symfony\Contracts\Cache\CacheInterface) для реализации работы директивы cache 
  # (https://twig.symfony.com/doc/3.x/tags/cache.html)
  # Если null (~) - фича отключена
  cacher: 'cache.app' 
  # Экспорт runtimes наружу (в Битрикс)
  runtimes_export: false 
```

## Расширения

**render**

Доработанный вариант функции render из фреймворка.

Первый вариант - рендер по контроллеру.

~~~twig
{{ render(controller("Local\\ExampleSimpleController", { 'id': 3 })) }}
~~~

Второй вариант - рендер по роуту.

~~~twig
{{ render('/api/entrypoint/', {'headers' : { 'X-Requested-With': 'XMLHttpRequest'} }) }}
~~~

Нюанс: не будет работать без установленного кастомного бандла с Symfony роутером ([https://github.com/ProklUng/wp.symfony.router.bundle](этим) или [https://github.com/ProklUng/BitrixSymfonyRouterBundle](этим)).

**render_service**

~~~twig

Рендер из сервиса.

{{ render_service('app.controller.user', 'detail', {'user': user}, {'eventDispatcher': 'event_dispatcher'}) }}
~~~

**renderController**

~~~twig

Рендер контроллера.

{{ renderController('Local\Buffering\Buffer::showBuffered', 'additional-styles') }}
~~~

**dump**

Стандартный твиговский dump.

**dump_custom**

Красивый твиговский dump (на базе `Symfony\Component\VarDumper`).

**json_decode**

json_decode как твиговская функция.

**glob**

glob как твиговский фильтр.

```twig
{% for size, icon in 'img/icons/favicon-*.png'|glob %}
    <link rel="icon" type="image/png" sizes="{{ size }}" href="{{ asset(icon) }}">
{% endfor %}

{% for css in 'node_modules/@fortawesome/fontawesome-free-webfonts/css/*.css'|glob(false) %}
    <link href="{{ asset(css) }}" rel="stylesheet">
{% endfor %}
```

**youtube**

```twig
{{ 'youtube_video_id'|youtube }} {# YouTube, размер по умолчанию (560x315) #}
{{ 'youtube_video_id'|youtube(640) }} {# YouTube, заданной ширины (640x315) #}
{{ 'youtube_video_id'|youtube(640, 480) }} {# YouTube с заданной шириной и высотой #}
{{ 'youtube_video_id'|youtube('100%') }} {# YouTube, fluid ширина (100% x 315px) #}

{% set content = '<div><p>Youtube video:</p>http://youtu.be/video_id</div>' %}
{{ content|youtubeBoxes|raw }} {# div с p и встроенное видео #}
```

### Прочее

- `encore_entry_link_tags` - путь к CSS entry Symfony Encore
- `encore_entry_script_tags`  - путь к JS entry Symfony Encore
- `quote` - строку в кавычки
- `sortbyfield` - сортировка массива по полю
- `url` - url по роуту и его параметрам
- `absolute_url` - абсолютный (со схемой и хостом) по url
- `path` - путь по роуту и его параметрам
- `naturaljoin`
- `csrf_token` - проверка csrf токена приложения
- `include(<путь к файлу>)` - включение php или html файла
- `truncate` - обрезка строки с сохранением html тэгов

Wordpress:

- `apply_filter` - apply_filter.

### Битриксовые

Из [пакета](https://github.com/maximaster/tools.twig), оригинальная документация:

#### Переменные из Битрикс

* `APPLICATION` - экземпляр класса \CMain, который хранится в глобальной переменной `$APPLICATION`
* `USER` - экземпляр класса \CUser, который хранится в глобальной переменной `$USER`
* `app` - экземпляр класса \Bitrix\Main\Application, если библиотека работает в битриксе с d7
* `result` - массив $arResult, сформированный в компоненте. См. примечания по настройке `extract_result`
* `params` - массив arParams, содержащий параметры компонента
* `template` - экземпляр класса \CBitrixComponentTemplate для данного шаблона
* `component` - экземпляр класса \CBitrixComponent для текущего компонента. Следует учитывать, что это не тот класс, который определен в компоненте, а именно \CBitrixComponent
* `templateFolder` - путь до директории текущего шаблона относительно DOCUMENT_ROOT
* `parentTemplateFolder` - путь до директории шаблона родительского компонента, если используется комплексный компонент. Эта переменная не учитывает родство шаблонов при использовании, например, функции `extends` в twig
* `lang` - массив языкозависимых переменных текущего шаблона. Будет удалено в версии 1.1
 
#### Функции из Битрикс

* `showComponent()` - аналог `\CMain::IncludeComponent()`. Данная функция только подключает компонент, но не возвращает результат работы компонента. Если вызывать подключение компонента с помощью объекта `APPLICATION`, то twig пытается вывести возвращаемое этим методом значение, что приводит часто к появлению артефактов в шаблонах.
* `getMessage()` - синоним для функции `GetMessage()` или метода `\Bitrix\Main\Loc::getMessage()`, если проект работает с d7
* `bitrix_sessid_post()` и `bitrix_sessid_get()` - переменные для генерации подписи с ID сессии в формах, синонимы одноименных функций в битриксе
* `showNote()`, `showError()` и `showMessage()` - синонимы функций `ShowNote()`, `ShowError()` и `ShowMessage()` 

#### Переменные из PHP

К сожалению, в Битрикс иногда сложно обойтись без использования суперглобальных переменных напрямую в шаблонах, поэтому все суперглобальные переменные из php пробрасываются в каждый шаблон и доступны по именам _SERVER, _GET, _POST, _REQUEST, _SESSION, _COOKIE, _FILES, _ENV и _GLOBALS

### Дополнительные дела

Если на проекте используется [пакет](https://github.com/ProklUng/tools.twig) с поддержкой твиговского движка
в компонентах, то в экземпляр того Твига автоматически перебрасываются все зарегистрированные в контейнере
`extensions` (то, что отмечено в конфигах тэгом `twig.extension`) и `runtimes` (тэг `twig.runtime`).

Также эта штука поддается использованию автономно, без контейнера.

Где-нибудь в `init.php` (для `extensions`; для `runtimes` класс обработчик - `Prokl\BitrixOrdinaryToolsBundle\Services\Twig\TwigRuntimesBag`):

```php
use Prokl\BitrixOrdinaryToolsBundle\Services\Twig\TwigExtensionsBag;
use Bitrix\Main\EventManager;

$events = [new Local\TwigExtension, Local\FooExtension::class];
$handler = new TwigExtensionsBag($events);

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler(
    '',
    'onAfterTwigTemplateEngineInited',
    [$handler, 'handle']
);
```

Если [пакет](https://github.com/ProklUng/tools.twig) не установлен, то соответствующие сервисы
удаляется на стадии компиляции контейнера.

Изначальная конфигурация [пакета](https://github.com/ProklUng/tools.twig) имеет приоритет над прокидываемым
снаружи.