<h1 align="center">Laravel Packager</h1>

<p align="center">
<a href="https://packagist.org/packages/luna/laravel-packager"><img src="https://poser.pugx.org/luna/laravel-packager/v/stable" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/luna/laravel-packager"><img src="https://poser.pugx.org/luna/laravel-packager/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/luna/laravel-packager"><img src="https://poser.pugx.org/luna/laravel-packager/v/unstable" alt="Latest Unstable Version"></a>
<a href="https://styleci.io/repos/84989989"><img src="https://styleci.io/repos/84989989/shield?branch=master" alt="StyleCI"></a>
<a href="https://travis-ci.org/DuckThom/laravel-packager"><img src="https://travis-ci.org/DuckThom/laravel-packager.svg?branch=master" alt="TravisCI"></a>
<a href="https://packagist.org/packages/luna/laravel-packager"><img src="https://poser.pugx.org/luna/laravel-packager/license" alt="License"></a>
</p>

<h3># Prerequisites</h3>
This package should run on PHP 5.5+ and Laravel 5.1+.

<h3># Setup</h3>
First, add this package to your `composer.json`:

```
    composer require luna/laravel-packager "~1.0"
```

Then, add the service provider to `config/app.php`:

```php
    "providers" => [
        // snip
        Luna\Packager\ServiceProvider::class,
    ];
```

If you want to use the package in the current project without adding it to, for example, packagist just yet, ie. for development, add the following line to your main project's `composer.json`:

```
{
    "autoload": {
        "classmap": [
            "database",
            "packages"   #  Add this line to your main projects composer.json
        ],
        "psr-4": {
            "App\\": "app/",
            "Tests\\": "tests/"
        }
    }
}
```

After adding that line, run `composer dump`, and add the package's service provider to `config/app.php`.

For example, you made a package which has the following structure: `<project_root>/packages/Foo/Bar/BarServiceProvider.php`.

When you add the line to the `composer.json` in the project root, you can use that service provider with it's usual namespace: `Foo\Bar\BarServiceProvider::class`.

<h3># Usage</h3>

```
    php artisan make:package <Vendor> <Package> [--base-dir=packages]
```

Be default, the package files are created in `<project_root>/packages/Vendor/Package`.

By specifying `--base-dir` in the `make:package` command, you can change where the files are placed.
