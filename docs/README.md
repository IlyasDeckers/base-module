# BaseModule
## Introduction
When Implementing this BaseModule you will be using the reposity pattern heavily. Repositories are classes or components that encapsulate the logic required to access data sources. They centralize common data access functionality, providing better maintainability and decoupling the infrastructure or technology used to access databases from the domain model layer.

The BaseModule provides an implementation of this pattern. It contains classes and methods to build upon in an application. By implementing the module you gain access to powefull classes and methods to write code fast, clean and testable.

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
Using this module forces you to think into modules. An application is build using multiple modules working together while maintaining the DRY principle. (Don't Repeat Yourself) All modules build upon the base module. When using the BaseModule you can access all of the 'base methods'.
### Implementation
#### Controllers
The implementation of this module is by extending the BaseController on all your controllers. The methods provided by the BaseController create a simple CRUD that works (almost) straight out of the box. More on this later.

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

These methods return valid json responses using Laravel's resource classes to allow you to expressively and easily transform your models and model collections into JSON. These resources are defined in your controller's constructor.
```php
    public function __construct(...)
    {
        ...
        $this->resource = '\Clockwork\Module\Http\Resources\ModuleResource';
        ...
    }
```

### Application Structure
As an example we will be building a simple UserModule, this module handles all user logic. Begin by creating a file structure like this in your application's folder or as a new composer package. By creating a new composer package you will gain the benifits of reusing your modules in new applications, if applicable.

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

```

