SlimServices
============

SlimServices is a service manager for the [Slim PHP microframework](http://github.com/codeguy/slim) based on [Laravel 4](http://laravel.com) service providers and DI container, allowing you to use core and many third-party Laravel packages in Slim based projects.

For example, to add Eloquent ORM to your Slim app:

```php
require 'vendor/autoload.php';

use SlimServices\ServiceManager;

$app = new Slim\Slim(array(
	// paths
	'path' => __DIR__,
	// database
    'database.fetch' => PDO::FETCH_CLASS,
    'database.default' => 'main',
    'database.connections' => array(
        'main' => array(
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'database'  => 'my_database',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ),
    )
));

$services = new ServiceManager($app);
$services->registerServices(array(
	'Illuminate\Events\EventServiceProvider',
	'Illuminate\Database\DatabaseServiceProvider'
));

// Laravel database component is now available in Slim's DI container

$app->get('/users', function()
{
	$app->render('users.html', array(
		// Load user list using Laravel database fluent query builder
		'users' => $app->db->table('users')->where('active', 1)->get()
	));
})

$app->run();
```

You can find more information about service providers in the [Laravel documentation](http://laravel.com/docs/ioc#service-providers).

## Installation

To install the latest version simply add this to your `composer.json`:

```javascript
"itsgoingd/slim-services": "dev-master"
```

Once the package is installed, you need to create a ServiceManager and register the services you'd like to use, configuration for the services is shared with the Slim instance itself:

```php
use SlimServices\Service;

$app = new Slim(...);

$services = new ServiceManager($app);
$services->registerServices(array(
	'Illuminate\Events\EventServiceProvider',
	'Illuminate\Database\DatabaseServiceProvider',
	'Illuminate\Filesystem\FilesystemServiceProvider',
	'Illuminate\Translation\TranslationServiceProvider',
	'Illuminate\Validation\ValidationServiceProvider',
	'Mailer\MailerServiceProvider',
	'Upload\UploadServiceProvider',
	...
));
```

Configuration examples for some popular components:

### Illuminate/Database

```javascript
"require": {
    "slim/slim": ">=2.3.0",
    "itsgoingd/slim-services": "dev-master",
    "illuminate/database": "4.1.*"
}
```

```php
require 'vendor/autoload.php';

use SlimServices\ServiceManager;

$app = new Slim\Slim(array(
	// paths
	'path' => __DIR__,
	// database
    'database.fetch' => PDO::FETCH_CLASS,
    'database.default' => 'main',
    'database.connections' => array(
        'main' => array(
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'database'  => 'my_database',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ),
    )
));

$services = new ServiceManager($app);
$services->registerServices(array(
	'Illuminate\Events\EventServiceProvider',
	'Illuminate\Database\DatabaseServiceProvider'
));

$users = $app->db->table('users')->select('login')->get();

class User extends Illuminate\Database\Eloquent\Model { public $table = 'users'; }

$users = User::all();
```

### Illuminate/Validation

```javascript
"require": {
    "slim/slim": ">=2.3.0",
    "itsgoingd/slim-services": "dev-master",
	"illuminate/validation": "4.1.*",
    "illuminate/filesystem": "4.1.*",
    "illuminate/translation": "4.1.*"
}
```

```php
require 'vendor/autoload.php';

use SlimServices\ServiceManager;

$app = new Slim\Slim(array(
	// paths
	'path' => __DIR__,
	// app
	'app.locale' => 'en'
));

$services = new ServiceManager($app);
$services->registerServices(array(
	'Illuminate\Filesystem\FilesystemServiceProvider',
	'Illuminate\Translation\TranslationServiceProvider',
	'Illuminate\Validation\ValidationServiceProvider'
));

$validator = $app->validator->make(
    array(
        'name' => 'Igor',
        'password' => 'noname',
        'email' => 'igor@no.name'
    ),
    array(
        'name' => 'required',
        'password' => 'required|min:8',
        'email' => 'required|email|unique:users'
    )
);

if ($validator->fails()) { ... }
```

### Custom service providers

You can create custom service providers simply by extending the `Illuminate\Support\ServiceProvider` class and registering them with the ServiceManager.

```php
class MailerServiceProvider extends Illuminate\Support\ServiceProvider
{
	public function register()
	{
		$this->app->bindShared('mailer', function($app)
		{
			return new Mailer($app['config']);
		});
	}
}

$services->registerServices(array(
	...,
	'MailerServiceProvider'
));

$app->mailer->send(...);
```

## Licence

Copyright (c) 2014 Miroslav Rigler

MIT License

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
