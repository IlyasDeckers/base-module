# BaseModule
## Introduction
When Implementing this BaseModule you will be using the repository pattern heavily. Repositories are classes or components that encapsulate the logic required to access data sources. They centralize common data access functionality, providing better maintainability and decoupling the infrastructure or technology used to access databases from the domain model layer.

The BaseModule provides an implementation of this pattern. It contains classes and methods to build upon in an application. By implementing the module you gain access to powerful classes and methods to write code fast, clean and testable.

The whole implementation looks like this:

```
  CustomModule                  BaseModule                    Laravel

+--------------+           +----------------+           +----------------+
|              |  extends  |                |  extends  |                |
|  Controller  +-----------> BaseController +----------->   Controller   |
|              |           |                |           |                |
+-------+------+           +----------------+           +----------------+
        |
        |
        |
+-------v------+           +----------------+
|              |  extends  |                |
|  Repository  +-----------> BaseRepository |
|              |           |                |
+-------+------+           +----------------+
        |
        |
        |
+-------v------+
|              |
|     Model    |
|              |
+--------------+
```
## Installation
```sh
composer require 3d-ict/base-module
```

## Usage
Using this module forces you to think into modules. An application is build using multiple modules working together while maintaining the DRY principle. (Don't Repeat Yourself) All modules are built upon the base module. When using the BaseModule you can access all of the 'base methods'.
### Implementation
#### Application Structure
Mentioned before your application will be divided into modules. As an example we will be building a simple UserModule, this module handles all user logic while maintaining as little code as possible.

Begin by creating a file structure like this in your application's folder or as a new composer package. By creating a new composer package you will gain the benefit of reusing your modules in new applications, if applicable.

```
Users/
├── Http/
│   ├── Controllers
│   │   └── UserController.php
│   ├── Resources
│   │   └── UserResource.php
│   └── ...
└── Interfaces/
│   └── UserRepositoryInterface.php
├── Model/
│   └── User.php
├── Providers/
│   └── UserProvider.php
├── Repositories/
│   └── UserRepository.php
└── routes.php

```
#### Controllers
By extending the BaseController on your module's controllers you gain access to all the methods provided by the BaseController to create a simple CRUD that works (almost) straight out of the box. More on this later.

```                            
+--------------+           +----------------+           +----------------+
|              |  extends  |                |  extends  |                |
|  Controller  +-----------> BaseController +----------->   Controller   |
|              |           |                |           |                |
+-------+------+           +----------------+           +----------------+
```

The methods accessible are:
* index
* show
* create
* update
* delete

These methods return valid JSON responses using Laravel's resource classes to allow you to expressively and easily transform your models and model collections into JSON. These resources are defined in your controller's constructor.
```php
    public function __construct(...)
    {
        ...
        $this->resource = '\Clockwork\Users\Http\Resources\UserResource';
        ...
    }
```
#### Repositories
Repositories are used to create methods that are reusable in the other parts of the application by doing Dependency Injection (DI). In this implementation of repositories we create an abstraction layer between our Controllers and Models, to extend and/or modify (eloquent) models.
```
+--------------+           +----------------+
|              |  extends  |                |
|  Repository  +-----------> BaseRepository |
|              |           |                |
+--------------+           +----------------+
```
Repositories are bound to their interfaces as a contract in the `ModuleProvider.php`
```php
    $this->app->bind(
        ModuleRepositoryInterface::class,
        Modulepository::class
    );
```
To use a repository you can inject the interface into you controller's interface. By doing this the `ModuleRepository.php` can access the repository and therefore the appropriate model to interact with the database.
```php
    public function __construct(ModuleRepositoryInterface $module)
    {
        $this->model = $module;
        $this->resource = '\Clockwork\Contracts\Http\Resources\ContractResource';
    }
```
The repository that is being used extends `BaseRepository.php` to gain access to a set of powerful methods to eager load relationships and to apply scopes to the model.

Relationships and are defined in the query string of the API call.
```
    /api/users/1?with=vehicle,supplier,contracts&scopes=admin
```

Accessible methods are:
* with
* scopes
* paginate
* sort
* search
#### Models
