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
