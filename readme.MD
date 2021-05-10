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

**dump**

Стандартный твиговский dump.

**dump_custom**

Красивый твиговский dump (на базе `Symfony\Component\VarDumper`).

**json_decode**

json_decode как твиговская функция.

### Прочее

- `encore_entry_link_tags` - путь к CSS entry Symfony Encore
- `encore_entry_script_tags`  - путь к JS entry Symfony Encore
- `quote` - строку в кавычки
- `sortbyfield` - сортировка массива по полю
- `url` - url по роуту и его параметрам
- `absolute_url` - абсолютный (со схемой и хостом) по url
- `path` - путь по роуту и его параметрам
- `naturaljoin`

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
