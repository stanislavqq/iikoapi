# iikoapi

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

PHP Библиотека для работы с iiko.biz api.
Не рекомендуется использовать на реальных проектах. 
Эта библиотека представлена скорее как пример, чем завершенный продукт.


PHP Library for working with iiko.biz api. 
Not recommended for use on real projects. 
This library is presented as an example rather than a complete product.


## Install

Via Composer

``` bash
$ composer require stanislavqq/iikoapi v1.0.5-beta
```

## Usage
Инициализация и получение токена для работы с api.
``` php
use stanislavqq\iikoapi\Api;

$iiko = new Api([
    'login' => 'demoDelivery',
    'password' => 'PI1yFaKFCGvvJKi'
]);

echo $iiko->getToken();
```

### Получить организации
Создание обьекта класса Organization: 
``` php
use stanislavqq\iikoapi\Organization; 

$orgList = $iiko->getOrganizationList();
$organization = new Organization($orgList[0]);

echo $organization->id;
echo $organization->name;
```

### Получить меню:
``` php
$organization = new Organization($orgList[0]);

$iiko->setOrganization($organization);
$menu = $iiko->getNomenclature(); //Вернет массив обьектов класса Product

foreach($menu as $product) {
    echo $product->name;
}
```

### Создание заказа
``` php 

$product = $iiko->createProduct();
$product->name = 'Паста по-итальянски';

$order = $iiko->createOrder();
$order->setProduct($product);
```
Метод setProduct клааса Order принимает обьект класса Product `$order->setProduct($product);`
Так же есть метод для добавления множества товаров `$order->setProducts(array $products);` 
## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email stanislavqq@yandex.ru instead of using the issue tracker.

## Credits

- [Stanislav QQ][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/stanislavqq/iikoapi.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/stanislavqq/iikoapi.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/stanislavqq/iikoapi.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/stanislavqq/iikoapi.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/stanislavqq/iikoapi
[link-travis]: https://travis-ci.org/stanislavqq/iikoapi
[link-scrutinizer]: https://scrutinizer-ci.com/g/stanislavqq/iikoapi/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/stanislavqq/iikoapi
[link-downloads]: https://packagist.org/packages/stanislavqq/iikoapi
[link-author]: https://github.com/stanislavqq
[link-contributors]: ../../contributors
