# Manage translations in laravel without ui and via api links

[![Latest Version on Packagist](https://img.shields.io/packagist/v/samehdoush/laravel-translations-api.svg?style=flat-square)](https://packagist.org/packages/samehdoush/laravel-translations-api)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/samehdoush/laravel-translations-api/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/samehdoush/laravel-translations-api/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/samehdoush/laravel-translations-api/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/samehdoush/laravel-translations-api/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/samehdoush/laravel-translations-api.svg?style=flat-square)](https://packagist.org/packages/samehdoush/laravel-translations-api)

Simple and developer friendly for easy translation management and higher capabilities

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-translations-api.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-translations-api)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation


You can install the package via composer:

```bash
composer require samehdoush/laravel-translations-api
```



## Auto install
```bash
php artisan translations-api:install
```
## OR 
You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="translations-api-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="translations-api-config"
```

This is the contents of the published config file:

```php
return [
    'source_language' => env('TRANSLATIONS_SOURCE_LANGUAGE', 'en'), // Source Language
    'exclude_files' => [
        //'validation.php', // Exclude default validation for example.
    ],
    'route_prefix' => env('TRANSLATIONS_PATH', 'translations'),
    'middleware' => ['api','auth:sanctum'],
    'database_connection' => env('TRANSLATIONS_DB_CONNECTION', null),
];
```

## Usage

To import your translations, run the following command:

```bash
php artisan translations:import
```

To import and overwrite all previous translations, use the following command:

```bash
php artisan translations:import --fresh
```
#### Exporting Translations

To export your translations, run the following command:

```bash
php artisan translations:export
```

## Get translations

```php
/api/translations GET METHOD
return [
    [
            'installed' => boolean,
            'translations' => Object,
        ]
]
```

## delete translation

```php
/api/translations/delete/{translation} DELETE METHOD

```

## get progress translation

```php
/api/translations/{translation}/progress GET METHOD

return [
    [
            'percentage' => float,
        ]
```

## create new Source key

```php
/api/translations/createSourceKey POST METHOD
  Body :     [
            'key' => 'required',
            'file' => 'required',
            'key_translation' => 'required',
        ]

```

## create new Translation

```php
/api/translations/createTranslation POST METHOD
  Body :     [
            'language' => 'required|exists:ltu_languages,id',
  ]

```

## GET phrases

```php
/api/translations/phrases/{translation} GET METHOD
  RETURN :     [
            'phrases' => Object,
  ]

```

## show phrase

```php
/api/translations/phrases/{translation}/edit/{phrase:uuid} GET METHOD
  RETURN :     [
            'phrase' => Object,
            'translation' => Object,
        ]

```

## update phrase

```php
/api/translations/phrases/{translation}/edit/{phrase:uuid} put METHOD
  BODY :     [
            'value' => 'required',
        ]

```
## DELETE phrase

```php
/api/translations/phrases/{translation}/delete/{phrase:uuid} delete METHOD


```


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [sameh doush](https://github.com/samehdoush)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
