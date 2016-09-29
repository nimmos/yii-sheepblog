Sheepblog
============================

This is ought to be a functional blog with access control and all the features 
of a typical blog.


DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      data/               contains database dumps
      mail/               contains view files for e-mails
      models/             contains model classes
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources



REQUIREMENTS
------------

The minimum requirement by this project is that your Web server supports PHP 5.4.0.


INSTALLATION
------------

### Install from an Archive File

Extract the archive file to a directory named `yii-sheepblog` that is directly under the Web root.

You can then access the application through the following URL:

~~~
http://localhost/yii-sheepblog/web/
~~~


### Install via Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this project using the following command:

~~~
composer install
~~~

Now you should be able to access the application through the following URL,
assuming `yii-sheepblog` is the directory directly under the Web root.

~~~
http://localhost/yii-sheepblog/web/
~~~


CONFIGURATION
-------------

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=blogdb',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```
