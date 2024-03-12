# Add comments to SQL queries made by Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-sql-commenter.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-sql-commenter)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/spatie/laravel-sql-commenter/run-tests?label=tests)](https://github.com/spatie/laravel-sql-commenter/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/spatie/laravel-sql-commenter/Check%20&%20fix%20styling?label=code%20style)](https://github.com/spatie/laravel-sql-commenter/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-sql-commenter.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-sql-commenter)

This package can add comments to queries performed by Laravel. These comments will use the [sqlcommenter](https://google.github.io/sqlcommenter/) format, which is understood by various tools and services, such as [PlanetScale's Query Insights](https://docs.planetscale.com/concepts/query-insights).

Here's what a query looks like by default:

```mysql
select * from users
```

Using this package, comments like this one will be added.

```mysql
select * from "users"/*controller='UsersController',action='index'*/;
```

The comments allow you easily pinpoint the source of the query in your codebase.


## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-sql-commenter.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-sql-commenter)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/laravel-sql-commenter
```

Optionally, you can publish the config file with:

```bash
php artisan vendor:publish --tag="sql-commenter-config"
```

This is the content of the published config file:

```php
return [
    /*
     * When set to true, comments will be added to all your queries
     */
    'enabled' => true,

    /*
     * These classes add comments to an executed query.
     */
    'commenters' => [
        Spatie\SqlCommenter\Commenters\ControllerCommenter::class => ['includeNamespace' => false],
        Spatie\SqlCommenter\Commenters\RouteCommenter::class,
        Spatie\SqlCommenter\Commenters\JobCommenter::class => ['includeNamespace' => false],
        Spatie\SqlCommenter\Commenters\FileCommenter::class => [
            'backtraceLimit' => 20, 
            'excludePathSegments' => [],
            'useRelativePath' => false,
        ],
        Spatie\SqlCommenter\Commenters\CurrentUserCommenter::class,
        // Spatie\SqlCommenter\Commenters\FrameworkVersionCommenter::class,
        // Spatie\SqlCommenter\Commenters\DbDriverCommenter::class,
    ],

    /*
     * If you need fine-grained control over the logging, you can extend
     * the SqlCommenter class and specify your custom class here
     */
    'commenter_class' => Spatie\SqlCommenter\SqlCommenter::class,
];
```

## Usage

With the package installed, comments are automatically added. By publishing the config file, you can choose which things are added to the comments.

### Adding arbitrary comments

If you want to add other arbitrary comments to the SqlComment, you can use the `addComment` method. The given comment will be added to the next performed query.

```php
use Spatie\SqlCommenter\SqlCommenter;

app(SqlCommenter::class)->addComment('foo', 'bar');

// select * from "users"/*foo='bar'*/;
```

### Dynamically enabling and disabling adding comments

You can dynamically enable and disable query logging. 

Let's assume that you only want to add comments for a certain part in your code base. First, you would need to set the value of the `enabled` key in the `sql-commenter` config file to `false`. This will stop the package from adding comments to all queries. Right before the part where you want to add comments, call `SqlCommenter::enable()` and at the end call `SqlCommenter::disable()`

```php
use \Spatie\SqlCommenter\SqlCommenter;

// queries performed here won't have comments

SqlCommenter::enable();

// queries performed here will have comments

SqlCommenter::disable();

// queries performed here won't have comments
```

### Adding you own commentator

If you want to add a comment to all performed queries, you can create your own `Commentator` class. It should implement the `Spatie\SqlCommenter\Commenters\Commenter` interface. The `comments` function should return a single or an array of `Spatie\SqlCommenter\Comment`.

Here's an example:

```php
namespace App\Support\SqlCommenters;

use Illuminate\Database\Connection;
use Spatie\SqlCommenter\Comment;

class MyCustomCommenter implements Commenter
{
    /** @return Comment|array<Comment>|null */
    public function comments(string $query, Connection $connection): Comment|array|null
    {
        return new Comment('my-custom-key',  'my-custom-value');
    }
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/riasvdv/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Rias Van der Veken](https://github.com/riasvdv)
- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
