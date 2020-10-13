# Map.ir log usage kafka and ELK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/map-ir/laravel-log-usage.svg?style=flat-square)](https://packagist.org/packages/map-ir/laravel-log-usage)
[![Total Downloads](https://img.shields.io/packagist/dt/map-ir/laravel-log-usage.svg?style=flat-square)](https://packagist.org/packages/map-ir/laravel-log-usage)

#### kafka
#### elasticSearch
#### logstash
## Installation

You can install the package via composer:

```bash
composer require map-ir/laravel-log-usage
```
##laravel Installation:
in laravel < 5.4
add this line to your config/app.php providers:
``` php
MapIr\LaravelLogUsage\LaravelLogUsageServiceProvider::class,
```
 after install package in laravel run artisan for publish config file 
```bash
php artisan vendor:publish --tag=config
```
After publish the package files you must open laravel-log-usage.php in config folder.
##lumen Installation:
add package middleware in bootstrap/app.php.
``` php
 $app->middleware([
     \MapIr\LaravelLogUsage\Http\Middleware\LogUsageMiddleware::class
 ]);
```
copy package config directory `vendor/map-ir/laravel-log-usage/config/logUsage.php` to root folder alongside with app directory.
update bootstrap/app.php by adding this line in `Register Config Files` section:
``` php
$app->configure('logUsage');
```
> you can set the keys in your .env file
``` dotenv
TOPIC="topic name for produce kafka"
BROKER_VERSION="version use broker"
KAFKA_MRIM="Topic metadata refresh interval in milliseconds broker"
REQUIRED_ACK="This field indicates how many acknowledgements the leader broker"
IS_ASYNC="Whether to use asynchronous production messages"
KAFKA_KEY="key for kafka producer index"
```
## Usage
 for config ELK Consumer kafka 
### Testing

``` bash
composer test
```
### Security

If you discover any security related issues, please email a.nasiri@map.ir instead of using the issue tracker.

## Credits

- [Armin Nasiri](https://github.com/map-ir)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.


