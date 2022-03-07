# Laravel Artisan Domain Contexts

<p align="center">
    <img src="/images/art/socialcard.png" alt="Social Card of Laravel Artisan Domain Contexts">
</p>

[![PHP Version][ico-php]][link-php]
[![Laravel Version][ico-laravel]][link-laravel]
[![CI Status][ico-actions]][link-actions]
[![PHPCS - GitHub Workflow Status](https://img.shields.io/github/workflow/status/allysonsilva/laravel-artisan-domain-contexts/PHP%20CodeSniffer%20-%20Coding%20Standards?label=PHPCS&logo=github)](https://github.com/allysonsilva/laravel-artisan-domain-contexts/actions/workflows/phpcs.yml)
[![PHPMD - GitHub Workflow Status](https://img.shields.io/github/workflow/status/allysonsilva/laravel-artisan-domain-contexts/PHPMD%20-%20PHP%20Mess%20Detector?label=PHPMD&logo=github)](https://github.com/allysonsilva/laravel-artisan-domain-contexts/actions/workflows/phpmd.yml)
[![PHPStan - GitHub Workflow Status](https://img.shields.io/github/workflow/status/allysonsilva/laravel-artisan-domain-contexts/PHPStan%20-%20Code%20Static%20Analysis?label=PHPStan&logo=github)](https://github.com/allysonsilva/laravel-artisan-domain-contexts/actions/workflows/phpstan.yml)
[![Coverage Status][ico-codecov]][link-codecov]
[![Code Quality/Consistency][ico-code-quality]][link-code-quality]
[![Latest Version][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)

## Table of Contents

  - [Overview](#overview)
  - [🚀 Installation](#--installation)
    - [Requirements](#requirements)
    - [Laravel version Compatibility](#laravel-version-compatibility)
    - [Install the Package](#install-the-package)
    - [Publish the Config](#publish-the-config)
  - [🔧 Configuration](#--configuration)
  - [📖 Usage](#--usage)
    - [Understanding the `--context` option](#understanding-the---context-option)
      - [Example](#example)
    - [Understanding the `--context-namespace` option](#understanding-the---context-namespace-option)
    - [Understanding the `--all-contexts` option](#understanding-the---all-contexts-option)
    - [Understanding the `--only-default` option](#understanding-the---only-default-option)
    - [Understanding the `--multi-databases` option](#understanding-the---multi-databases-option)
    - [List of commands using contexts](#list-of-commands-using-contexts)
    - [🏗 `make` commands](#-make-commands)
      - [Examples](#examples)
    - [`migrate` commands](#migrate-commands)
      - [Understanding the behavior of `migrate:fresh` and `migrate:refresh`](#understanding-the-behavior-of-migratefresh-and-migraterefresh)
      - [📹  Demo `migrate:fresh`](#--demo-migratefresh)
      - [📹  Demo `migrate:refresh`](#--demo-migraterefresh)
      - [Understanding the behavior of `migrate:reset`, `migrate:rollback`, `migrate:status` and `migrate`](#understanding-the-behavior-of-migratereset-migraterollback-migratestatus-and-migrate)
      - [📹  Demo `migrate:reset`](#--demo-migratereset)
      - [📹  Demo `migrate:rollback`](#--demo-migraterollback)
    - [`db:seed` command](#dbseed-command)
      - [📹  Demo `db:seed`](#--demo-dbseed)
  - [🧪 Testing](#--testing)
  - [📝 Changelog](#--changelog)
  - [🤝 Contributing](#--contributing)
  - [🔒 Security](#--security)
  - [🏆 Credits](#--credits)
  - [License](#license)

## Overview

> This package provides the ability to use **artisan commands** in different **domain contexts**. It allows to work interactively in the migration commands and seeders, choosing which class should be executed.

<p align="center">
    <img src="/images/demos/overview.gif?raw=true"/>
</p>

## 🚀  Installation

### Requirements

The package has been developed and tested to work with the following minimum requirements:

- *PHP 8.0*
- *Laravel 8.70*

### Laravel version Compatibility

| Laravel | PHP |    Package   |   Maintained   |
|:-------:|:---:|:------------:|:--------------:|
|   9.x   | 8.0 |   **^2.0**   |       ✅       |
|   8.70  | 8.0 |   **^1.0**   |       ✅       |

### Install the Package

You can install the package via Composer:

```bash
composer require allysonsilva/laravel-artisan-domain-contexts
```

### Publish the Config

You can then publish the package's config file by using the following command:

```bash
php artisan vendor:publish --tag="context-config"
```

## 🔧  Configuration

1. Create a folder, inside the `app` folder with the same name as the config of `config('context.folders.domain')`

2. Add trait to `app/Console/Kernel.php` file with usage options in all Laravel commands:
    ```diff
    use Illuminate\Console\Scheduling\Schedule;
    use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
    +use Allyson\ArtisanDomainContext\Concerns\ArtisanConsoleTrait;

    class Kernel extends ConsoleKernel
    {
    +    use ArtisanConsoleTrait;
    ```

3. Inside the domain folder (`config('context.folders.domain')`), is where you can find Laravel components (such as migrations, seeders, models, jobs, etc). The names of the folders, where the classes are, are in the config of `config('context.folders.components')`.

## 📖  Usage

To use **commands by context**, the following options have been added to Laravel commands:

- **`--context`**
- **`--context-namespace`**
- **`--all-contexts`**
- **`--only-default`**
- **`--multi-databases`**

**Some options are only available in a certain command, but the `--context` option is present in all commands in the list below!**

The list of standard laravel commands that were handled by adding these options can be seen [table below](#list-of-commands-using-contexts).

### Understanding the `--context` option

**This option is present in all commands listed below!**

When this option is passed in the command, then the component/class/resource is manipulated or created, according to the resource type setting in `config('context.folders.components')`.

To change the path/name of the folder where the class should be manipulated or created, see the config in `config('context.folders.components')`.

#### Example

So, for example, to create a middleware in a given context, in this case, in the user context, the command can be: `php artisan make:middleware --context=User YourMiddleware`.

A middleware class was created in `app/Domain/User/Http/Middlewares/YourMiddleware.php`.

If the config of `config('context.folders.components.middlewares')` has the value of `Http/AnotherFolder` instead of `Http/Middlewares` (default), and the previous command is executed, then the class would be created in `app/Domain/User/Http/AnotherFolder/YourMiddleware.php`.

### Understanding the `--context-namespace` option

This option will only be used in the `make` commands, with that, see and [explanation about it](#make-commands).

### Understanding the `--all-contexts` option

When this option is passed in the command, then it will be executed non-interactively, that is, it will execute the context-specific filtered classes.

This option is not present in the `make` commands only in the migration and db:seed commands. See the [table below](#list-of-commands-using-contexts).

It has the same behavior as the `--force` option.

### Understanding the `--only-default` option

By default, *migrations* commands are executed in all contexts when no options are passed. To run migrations commands in Laravel's default folder (`database/migrations`) use this option.

This option is only in the migration and `db:seed` commands. It is not present in the `make` commands.

### Understanding the `--multi-databases` option

*This option is only present in migration commands!*

By default the migration commands are run on the database configured in the `DB_DATABASE` *env*, so you can only use one database in the command. With this option, you can use multiple databases for the same command via the config `config('context.migrations.databases')`

When this option is passed, then the command will be executed on different databases according to the config of `config('context.migrations.databases')`.

The config of `config('context.migrations.databases')` refers to the name of the database that the operation will be performed on.

### List of commands using contexts

<table>
    <tr>
        <th rowspan="2">Commands</th>
        <th colspan="5">Additional Command Options</th>
    </tr>
    <tr>
        <td><code>--context</code></td>
        <td><code>--context-namespace</code></td>
        <td><code>--all-contexts</code></td>
        <td><code>--only-default</code></td>
        <td><code>--multi-databases</code></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:cast</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:channel</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:command</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:event</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:exception</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:factory</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:factory</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:job</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:listener</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:mail</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:middleware</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:migration</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:model</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:notification</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:observer</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:policy</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:provider</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:request</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:resource</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:rule</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>make:seeder</code></strong></th>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>migrate:fresh</code></strong></th>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center">✔️</td>
        <td align="center"></td>
    </tr>
    <tr>
        <th align="center"><strong><code>migrate:refresh</code></strong></th>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
    </tr>
    <tr>
        <th align="center"><strong><code>migrate:reset</code></strong></th>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
    </tr>
    <tr>
        <th align="center"><strong><code>migrate:rollback</code></strong></th>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
    </tr>
    <tr>
        <th align="center"><strong><code>migrate:status</code></strong></th>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
    </tr>
    <tr>
        <th align="center"><strong><code>migrate</code></strong></th>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
    </tr>
    <tr>
        <th align="center"><strong><code>db:seed</code></strong></th>
        <td align="center">✔️</td>
        <td align="center"></td>
        <td align="center">✔️</td>
        <td align="center">✔️</td>
        <td align="center"></td>
    </tr>
</table>

### 🏗 `make` commands

> Make commands, create files in a given context or Laravel's default folder when no context is specified.

All the `make` commands that are listed in the table above, have 2 options in their command, which are:

- `--context`: *Name of the folder where the class will be created*, if not passed in the command, then the class will be created in the *Laravel's default folder*.

- `--context-namespace`: Custom namespace that will be used in place of the class's normal namespace.

To change the name of the component folder in which the class is created, see the following config `config('context.folders.components')`.

#### Examples

The example commands below will use the following folder organization:

```
app/
├── Domain
│   ├── Foo
│   ├── Post
└── └── User
```

**How to create a [MIGRATION] in a specific context?**

Use the following command below as an example, passing the `--context` option with the name of the **context** in *which the migration will be created*.

The migration files will be saved in the *config* folder `config('context.folders.components.migrations')` in the *context folder*, according to the `--context` option of the command.

```bash
php artisan make:migration --context=Post create_posts_table
```

*A new migration has been created at*: `app/Domain/Post/Database/Migrations/2022_xx_xx_xxxxxx_create_posts_table.php`

The file path parts are:

- **`Domain`**: Value configured according to `config('context.folders.domain')`.
- **`Post`**: Value of the `--context` option.
- **`Database/Migrations`**: Value configured according to `config('context.folders.components.migrations')`.

**How to create a [JOB] in a specific context?**

In the same way as explained earlier in the case of migration, *the `--context` option is also used to create a new job in a specific context*.

```bash
php artisan make:job --context=Foo MyJob
```

*A new job has been created at*: `app/Domain/Foo/Jobs/MyJob.php`

The file path parts are:

- **`Domain`**: Value configured according to `config('context.folders.domain')`.
- **`Foo`**: Value of the `--context` option.
- **`Jobs`**: Value configured according to `config('context.folders.components.jobs')`.

**How to create a class/component with a custom namespace?**

*Use the `--context-namespace` option to customize the class namespace prefix.*

If you want to create a model with a specific namespace, Use the following command below as an example.

```bash
php artisan make:model --context=Post --context-namespace=PostDomain Post
```

The previous command will *create a model in the path*: `app/Domain/Post/Models/Post.php`

With the following content:

```diff
<?php

+namespace PostDomain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
}
```

The namespace for the above class is: `namespace PostDomain\Models`.

**How to create class in Laravel's default folder?**

To create the class in the default Laravel folder, don't use/pass the `--context` option!

The following command will create the class in Laravel's default folder at `app/Events/MyEvent.php`.

```bash
php artisan make:event YourEvent
```

### `migrate` commands

> The migration commands are executed interactively, and you can even select each individual migration, or all migrations in a given context to perform the operation.

The following are the options that may be on commands:

- `--context`: Context where migrations should be performed/handled.

- `--all-contexts`: The command must be run on migrations from all contexts.

- `--only-default`: If this option is passed, then only migrations from the *default Laravel folder* (`database/migrations`) will be used for the command.

- `--multi-databases`: Will run the command on all config databases `config('context.migrations.databases')`.

To see the migration commands in action, let's use the folder organization below as an example and see the expected results:

```
app
└── Domain
    ├── Foo
    │   └── Database
    │       ├── Migrations
    │       │   ├── 2022_02_30_000000_create_baz_table.php
    │       │   └── 2022_02_30_000000_create_foo_table.php
    │       └── Seeders
    │           ├── BazTableSeeder.php
    │           └── FooTableSeeder.php
    ├── Post
    │   └── Database
    │       ├── Migrations
    │       │   ├── 2022_02_30_000000_create_posts_1_table.php
    │       │   ├── 2022_02_30_000000_create_posts_2_table.php
    │       │   └── 2022_02_30_000000_create_posts_3_table.php
    │       └── Seeders
    │           ├── PostsTableSeeder1.php
    │           ├── PostsTableSeeder2.php
    │           └── PostsTableSeeder3.php
    └── User
        └── Database
            ├── Migrations
            │   ├── 2022_02_30_000000_create_users_1_table.php
            │   ├── 2022_02_30_000000_create_users_2_table.php
            │   └── 2022_02_30_000000_create_users_3_table.php
            └── Seeders
                ├── UsersTableSeeder1.php
                ├── UsersTableSeeder2.php
                └── UsersTableSeeder3.php
```

#### Understanding the behavior of `migrate:fresh` and `migrate:refresh`

- Both have the same set of options: `--context` and `--only-default`
- `migrate:refresh` has one more option which is `--multi-databases`

**Both work in the same way / Summary**:

- *When no options are passed to the command, the default is to perform migrations from all contexts. For this reason it does not have the `--all-contexts` option. Well, it's the same behavior as if you had the option.*
- *Cannot select/choose migration individually.*
- *Run all migrations from given context, from all contexts or from Laravel's default folder.*

See the questions and answers below for better understanding:

- How to run the command in a specific context?
    ```bash
    php artisan migrate:<fresh or refresh> --context=YOUR_CONTEXT
    ```

- How to run command in all contexts?
    ```bash
    php artisan migrate:<fresh or refresh>
    ```

- How to run command only in default Laravel migration folder?
    ```bash
    php artisan migrate:<fresh or refresh> --only-default
    ```

- How can I run the command on multiple databases? (*only refresh*)
    ```bash
    # In all config databases `config('context.migrations.databases')`
    php artisan migrate:refresh --multi-databases
    # Or on multiple databases of a specific context:
    php artisan migrate:refresh --context=User --multi-databases
    ```

#### 📹  Demo `migrate:fresh`

See the demo below for better understanding:

<p align="center">
    <img src="/images/demos/migrate-fresh.gif?raw=true"/>
</p>

#### 📹  Demo `migrate:refresh`

See the demo below for better understanding:

<p align="center">
    <img src="/images/demos/migrate-refresh.gif?raw=true"/>
</p>

#### Understanding the behavior of `migrate:reset`, `migrate:rollback`, `migrate:status` and `migrate`

- All 4 commands have the following options:
  - `--context`
  - `--all-contexts`
  - `--only-default`
  - `--multi-databases`

**Summary of all commands**:

- *By default, a list will always appear with the migrations to choose from. Whether migrations from all contexts, or migrations from a specific context using the `--context` option, there will always be a list of migrations to be chosen and used in the commands. **To run non-interactive, use the `--force` option.***
- *When no options are passed to the command, the default is to list migrations from all contexts, using artisan's [`choice`](https://laravel.com/docs/artisan#multiple-choice-questions) method.*
- *To run the command on migrations of all contexts, use the `--all-contexts` option.*

See the questions and answers below for better understanding:

*The `$command` variable, can be one of the items `['migrate:reset', 'migrate:rollback', 'migrate:status', 'migrate']`.*

- How to run the command in a specific context?
    ```bash
    # A list of migrations present in the context will appear to be chosen for execution!
    php artisan $command --context=YOUR_CONTEXT
    # To execute in a "forced" way, that is, all migrations from a given context, use the `--force` option!
    php artisan $command --context=YOUR_CONTEXT --force
    ```

- How to run command in all contexts?
    ```bash
    # List of migrations to choose which will be performed
    php artisan $command
    # Run migrations from all contexts "forced"
    php artisan $command --all-contexts
    ```

- How to run command only in default Laravel migration folder?
    ```bash
    php artisan $command --only-default
    ```

- How can I run the command on multiple databases?
    ```bash
    # In all config databases `config('context.migrations.databases')`
    php artisan $command --multi-databases
    # Or on multiple databases of a specific context:
    php artisan $command --context=User --multi-databases
    # Or force execution of the command
    php artisan $command --context=User --multi-databases --force
    ```

#### 📹  Demo `migrate:reset`

See the demo below for better understanding:

<p align="center">
    <img src="/images/demos/migrate-reset.gif?raw=true"/>
</p>

#### 📹  Demo `migrate:rollback`

See the demo below for better understanding:

<p align="center">
    <img src="/images/demos/migrate-rollback.gif?raw=true"/>
</p>

### `db:seed` command

Three options are found in the command:

- `--context`
- `--all-contexts`
- `--only-default`

See the questions and answers below for better understanding:

- How to run the command in a specific context?
    ```bash
    # A list of seeders present in the context will appear to be chosen for execution!
    php artisan db:seed --context=YOUR_CONTEXT
    # To execute in a "forced" way, that is, all seeders from a given context, use the `--force` option!
    php artisan db:seed --context=YOUR_CONTEXT --force
    ```

- How to run command in all contexts?
    ```bash
    # List of seeders to choose which will be performed
    php artisan db:seed
    # Run seeders from all contexts "forced"
    php artisan db:seed --all-contexts
    ```

- How to run command only in default Laravel migration folder?
    ```bash
    php artisan db:seed --only-default
    ```

#### 📹  Demo `db:seed`

See the demo below for better understanding:

<p align="center">
    <img src="/images/demos/db-seed.gif?raw=true"/>
</p>

## 🧪  Testing

``` bash
composer test:unit
```

## 📝  Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information about the changes on this package.

## 🤝  Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 🔒  Security

If you discover any security related issues, please email github@allyson.dev instead of using the issue tracker.

## 🏆  Credits

- [Allyson Silva](https://github.com/allysonsilva)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-php]: https://img.shields.io/packagist/php-v/allysonsilva/laravel-artisan-domain-contexts?color=%234F5B93&logo=php
[ico-laravel]: https://img.shields.io/static/v1?label=laravel&message=%E2%89%A58.0&color=ff2d20&logo=laravel
[ico-actions]: https://github.com/allysonsilva/laravel-artisan-domain-contexts/actions/workflows/ci.yml/badge.svg
[ico-codecov]: https://codecov.io/gh/allysonsilva/laravel-artisan-domain-contexts/branch/main/graph/badge.svg?token=MXQO0HIBFM
[ico-code-quality]: https://app.codacy.com/project/badge/Grade/5f2b3a98dbee48aa8e14ba4a55681f09
[ico-version]: https://img.shields.io/packagist/v/allysonsilva/laravel-artisan-domain-contexts.svg?label=stable
[ico-downloads]: https://img.shields.io/packagist/dt/allysonsilva/laravel-artisan-domain-contexts.svg

[link-php]: https://www.php.net
[link-laravel]: https://laravel.com
[link-actions]: https://github.com/allysonsilva/laravel-artisan-domain-contexts/actions/workflows/ci.yml
[link-codecov]: https://codecov.io/gh/allysonsilva/laravel-artisan-domain-contexts
[link-code-quality]: https://www.codacy.com/gh/allysonsilva/laravel-artisan-domain-contexts/dashboard
[link-packagist]: https://packagist.org/packages/allysonsilva/laravel-artisan-domain-contexts
[link-downloads]: https://packagist.org/packages/allysonsilva/laravel-artisan-domain-contexts
