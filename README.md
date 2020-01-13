# BaseModule
1. Introduction
2. Installation
3. Usage

## Introduction
When Implementing the BaseModule you will be using the repository pattern. Repositories are classes or components that encapsulate the logic required to access data sources. They centralize common data access functionality, providing better maintainability and decoupling the infrastructure or technology used to access databases from the domain model layer.

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
WIP

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
+------------------+           +----------------+           +----------------+
|                  |  extends  |                |  extends  |                |
|  UserController  +-----------> BaseController +----------->   Controller   |
|                  |           |                |           |                |
+-------+----------+           +----------------+           +----------------+
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
##### Request validation
To implement request validations on your controllers, simply add an array with the method names as the key and the request class as value. If no validation is required on on of the controller's methods you can define the validation class as null in the `$rules` array.
```php
    protected array $rules = [
        'update' => UpdateRequest::class,
        'create' => null,
    ];
```
In the request class we define our rules as a normal laravel request class. These rules are resolved in the ValidatorMiddleware and applied accordingly.

```php
class UpdateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|unique:posts|max:255',
            'body' => 'required',
        ];
    }
}
```

The logic handling the validations is handled by the `ValidatorMiddleware`. Where the validator method handles our validation implementation.

```php
    public function validator(string $function, object $request)
    {
        if (isset($this->rules[$function]) && !is_null($this->rules[$function])) {
            $this->validate($request,
                (new $this->rules[$function])->rules()
            );
        }
    }
```
#### Repositories
Repositories are classes or components that encapsulate the logic required to access data sources. In this implementation of repositories we create an abstraction layer between our Controllers and Models, to extend and/or modify (eloquent) models.

The `BaseRepository` is used as a starting point for all repositories. This enables us to create and reuse methods shared by all repositories in our application.
```
+--------------+           +----------------+
|              |  extends  |                |
|  Repository  +-----------> BaseRepository |
|              |           |                |
+--------------+           +----------------+
```
##### Registering repositories
Repositories are bound to their interfaces as a contract in the Service Provider e.g. `ModuleProvider.php`
```php
    $this->app->bind(
        ModuleRepositoryInterface::class,
        Modulepository::class
    );
```
##### Database Transactions
Database transactions are applied automatically via middleare. When an (unhandled) exception occurs during one of the database queries the query is rolled back to prevent data corruption.

When a request hits the middleware, the request method is detirmined. If this method is `POST`, `PUT` or `DELETE` the transactions are enabled for this request.
##### Repository example
```php
<?php
namespace Clockwork\Users\Repositories;

use Clockwork\Users\Models\User;
use Clockwork\Base\BaseRepository;
use Clockwork\Base\Traits\Transaction;
use Clockwork\Users\Interfaces\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    use Transaction;

    protected $model;
  
    /**
     * UserRepository constructor.
     * 
     * @param User $invoice
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * Get all users 
     * 
     * @param array $attributes
     * @return mixed
     */
    private function getAll(object $request) : object
    {
        parrent::getAll($request)
    }

    /**
     * Find a user 
     * 
     * @param array $attributes
     * @return mixed
     */
    private function find(object $request) : object
    {
        parrent::find($request)
    }

    /**
     * Store a user 
     * 
     * @param array $attributes
     * @return mixed
     */
    private function store(object $request) : object
    {
        // store logic here
        return $this->itemResponse(
            $request,
            $this->model->where('id', $result->id)
        );
    }

    /**
     * Update a user
     *
     * @param object $request
     * @return void
     */
    private function update(object $request) : object
    {
        // update logic here
        return $this->itemResponse(
            $request,
            $this->model->where('id', $request->id) // or $updated->refresh()
        );
    }

    /**
     * Delete an invoice
     *
     * @param integer $id
     * @return void
     */
    private function delete(int $id) : object
    {
        // No default logic
        return;
    }
}
```
##### Using repositories
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
