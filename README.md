# BaseModule
1. Introduction
2. Installation
3. Usage

## Introduction
When Implementing the BaseModule you must use the repository pattern. Repositories are classes or components that encapsulate the logic required to access data sources. They centralize common data access functionality, providing better maintainability and decoupling the infrastructure or technology used to access databases from the domain model layer.

The BaseModule provides an implementation of this pattern. It contains classes and methods to build upon in an application. By using the module you gain access to powerful classes and methods to write code fast, clean and testable.

This module has the following traits/capabilities:

- write API's fast
- proper JSON responses
- request validation
- database transactions
- minimal code
- build upon Laravel

Using this package, it forces you to think in modules while using Domain Driven Design. An application is build using multiple modules working together while maintaining the DRY principle. (Don't Repeat Yourself) All modules are built upon the base module. When using the package you can access all of the 'base methods'.

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
+-------v------+           +----------------+           +------------------+
|              |  extends  |                |           |                  |
|  Repository  +-----------> BaseRepository |-----------> BaseQueryBuilder |
|              |           |                |           |                  |
+-------+------+           +----------------+           +------------------+
        |
        |
        |
+-------v------+
|              |
|     Model    |
|              |
+--------------+
```

Aside from providing the base structure and design patterns for your application it also handles authentication, factories generator and a configwriter. These functionalities can be found under `src/Modules`.
## Installation
WIP

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
