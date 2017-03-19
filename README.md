# wp-migrations

This WordPress plugin helps to synchronize settings and options between local environments and production.
Keeping options in sync is generally done by hand and quite often a setting is forgotten when the site is deployed or updated.
The implementation fo this plugin is largely based upon the [Migrator](https://github.com/laravel/framework/tree/5.4/src/Illuminate/Database/Migrations) of Laravel.   
To find out what options have been changed an overview is available listing all the updated option.

- [wp-migrations](#wp-migrations)
  * [Installation](#installation)
    + [Install with composer](#install-with-composer)
    + [Install with git](#install-with-git)
  * [Usage](#usage)
    + [Setup](#setup)
    + [Creating migrations](#creating-migrations)
      - [Migration types](#migration-types)
      - [Validator](#validator)
    + [How to find out what options where changed?](#how-to-find-out-what-options-where-changed-)
  * [Contributing](#contributing)
  * [License](#license)

## Installation
The plugin isn't available in the WordPress plugin repository yet.
For now the only ways to install the plugin is via composer or git.

### Install with composer
Add this git repository to your composer.json repositories:
```json
{
    "repositories":[
        {
            "type":"vcs",
            "url":"git@github.com:pionect/wp-migrations.git"
        }
    ]
}
```
Then configure the [custom install paths](https://github.com/composer/installers#custom-install-paths) for WordPress plugins
```json
{
    "extra": {
        "installer-paths": {
            "wp-content/plugins/{$name}/": ["type:wordpress-plugin"]
        }
    }
}
```
Finally require this plugin _and perhaps some others with [WordPress Packagist](https://wpackagist.org/)_
```bash
composer require pionect/wp-migrations
```

### Install with git
If you don't want to use composer simply download or clone the project in the `wp-content/plugins` folder.
```bash
cd wp-content/plugins
git clone git@github.com:pionect/wp-migrations.git
```

## Usage
### Setup
The plugin doesn't migrate a thing out of the box. It waits for you to tell where the directory is with all the migrations.
Add a filter to your functions.php or your plugin.
```php
function my_wpmigrations_directory($directory){
    //this assumes a migrations directory next to this file
    return __DIR__ .'/migrations';
}
add_filter('wpmigrations_directory', 'my_wpmigrations_directory');
      
//Optionally change the namespace of your migrations
function my_wpmigrations_namespace($namespace){
    return '\Pionect\Structure\Migrations';
}
add_filter('wpmigrations_namespace', 'my_wpmigrations_namespace');
```

In this folder you can place a file per migration.
Some examples are supplied in the [examples](examples) directory.

### Creating migrations
Some expectations about migrations are:
- Migrations file names are prefixed with numbers and underscores. 
For example `1_first_migration` and `2_second_migration` or `2017_03_01_first_migration` and `2017_03_02_second_migration`
- Migrations file names are [snake case](https://en.wikipedia.org/wiki/Snake_case)
- The classname of the migration should be the camel case version of the filename, without the numbers.
For example `2017_03_01_first_migration` becomes `Class FirstMigration`
- Migrations should always be in a namespace. By default in the `namepsace Migration`.
- There are two migrations types for installing plugins. I'd rather encourage you to do so only using composer.

#### Migration types
A migration can be a simple class containing only the `up` function.
Anything can be done there, like queries on the database or creating files/folders, etc.

function | parameters | return value
-------- | ---------- | ------------
up | none | none

For migrating the value of an option there is an existing `MigrationType` called `OptionMigration`.
When you extend your class with this type you are expected to implement the abstract functions `get_option_name` and `up`.

function | parameters | return value
-------- | ---------- | ------------
get_option_name | none | a string with the option name.
up | current value | the new value.

#### Validator
Migrations can have validators which will prevent the migration to be run up until all rules are met.
Implement the function `get_validation_rules` and return an array of rules and their parameters.

**PluginExists**
To check if a plugin is installed.
```php
return [
    'plugin_exists'  => 'debug-bar'
]
```

**PluginVersion**
To check if a plugin has a matching version, without complaining about a missing plugin.
The parameter uses [version matching](https://getcomposer.org/doc/articles/versions.md) from composer to check the version.
```php
return [
    'plugin_exists'  => 'debug-bar|0.8.*'
]
```

### How to find out what options where changed?
Having migrations is cool but it is still hard to find out what options you want to migrate.
For example you might not know what options are changed when saving the settings of a plugin.
For this purpose a page called 'Option Versions' is added in the admin under tools.
The options are tracked automatically from the moment the plugin is activated.
There you can view all the changes made to the wp_options in a chronological ordered table.

The table has the following columns:

Column name | Meaning
----------- | -------
Type | 'wordpress', 'plugin' or 'theme' depending on the location of the PHP script which saved the option
Group | 'wordpress' or name of plugin/theme which saved the option
Option | the option name as its stored in the wp_options table
Value | the actual value stored in wp_options table
User ID | the user who changed the option, perhaps useful to find changes on the production environment made by your client
Updated at | timestamp when the option was saved

## Contributing
You are most welcome to fork and send pull requests or simply submit issues and feature requests. 

## License
MIT