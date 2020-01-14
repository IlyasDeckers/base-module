# Repositories
1. Introduction
2. Registering repositories
3. Database transactions
4. BaseModule API references

## Introduction
Repositories are classes or components that encapsulate the logic required to access data sources. With the implementation of repositories we create an abstraction layer between our Controllers and Models, to create business logic to interact with our (eloquent) models.

The `BaseRepository` is used as a starting point for all repositories. This enables us to create and reuse methods shared by all repositories in our application.
```
+--------------+           +----------------+
|              |  extends  |                |
|  Repository  +-----------> BaseRepository |
|              |           |                |
+--------------+           +----------------+
```

## Registering repositories
Repositories are bound to their interfaces as a contract in the Service Provider e.g. `ModuleProvider.php`
```php
    $this->app->bind(
        ModuleRepositoryInterface::class,
        Modulepository::class
    );
```

## Using repositories
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

## Covariance and contravariance in repositories
As of PHP 7.4.3, covariance and contravariance is supported. (https://www.php.net/manual/de/language.oop5.variance.php) In the `BaseRepository` we find return types on the methods. These return types can be overriden in the child class with the appropriate return types. (repositories return insances of models eg. User) 

Covariance allows a child's method to return a more specific type than the return type of its parent's method. This is better illustrated with an example.

```php
<?php

interface AnimalShelter
{
    public function adopt(string $name): Animal;
}

class CatShelter implements AnimalShelter
{
    public function adopt(string $name): Cat // instead of returning class type Animal, it can return class type Cat
    {
        return new Cat($name);
    }
}

class DogShelter implements AnimalShelter
{
    public function adopt(string $name): Dog // instead of returning class type Animal, it can return class type Dog
    {
        return new Dog($name);
    }
}
```

Not or wrongly specifying return types on the child classes will provoke a PHP runtime error.

>PHP Fatal error:  Declaration of UserRepository::getAll(Request $request) must be compatible with BaseRepository::getAll(Request $request): Collection in UserRepository.php on line 41

## Repository example
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
    private function getAll(object $request) : Collection // instead of returning class type object, it can return class type Collection
    {
        parrent::getAll($request)
    }

    /**
     * Find a user 
     * 
     * @param array $attributes
     * @return mixed
     */
    private function find(object $request) : User
    {
        parrent::find($request)
    }

    /**
     * Store a user 
     * 
     * @param array $attributes
     * @return mixed
     */
    private function store(object $request) : User
    {
        // store logic here
        return $this->itemResponse(
            $this->model->where('id', $result->id)
        );
    }

    /**
     * Update a user
     *
     * @param object $request
     * @return void
     */
    private function update(object $request) : User
    {
        // update logic here
        return $this->itemResponse(
            $this->model->where('id', $request->id) // or $updated->refresh()
        );
    }

    /**
     * Delete an invoice
     *
     * @param integer $id
     * @return void
     */
    private function delete(int $id) : void
    {
        // No default logic
        return;
    }
}
```
## BaseModule API references
### Properties

|    access modifiers              |   property     |                | 
|------------------|--------|----------------|
| protected object | $model | The Model used in the class |

### Methods


