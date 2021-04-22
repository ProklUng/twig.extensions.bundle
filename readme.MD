# Бандл с набором расширений для Twig

**INTERNAL**

## Установка

`composer require proklung/twig-extensions-bundle`

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
