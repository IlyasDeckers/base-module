# BaseController
## Introduction
The base controller is used as a starting point for all your controllers. The base controller extends `use Clockwork\Http\Controllers\Controller`.

When implementing the BaseController it is important to create a constructor method on your Controller. This constructor contains a type hinted interface and the model's API resource.

## Usage
### `$model`
The model gets set in the base controller's constructor. This model is an interface that is implemented by the repository to access your eloquent methods. The interface is type hinted in the controller's constructor that implements the `BaseController`. 

Read more about repositories

https://laravel.com/docs/5.7/container#automatic-injection
https://medium.com/@jsdecena/refactor-the-simple-tdd-in-laravel-a92dd48f2cdd
https://medium.com/employbl/use-the-repository-design-pattern-in-a-laravel-application-13f0b46a3dce

### `$resource`
The API resource the controller must use. This resource is defined in the controller's constructor. 

> An API resource must be used for formating your API responses. Resources. These resources can be found/created in `Clockwork\ModuleName\Http\Resources`

### `$request`
WIP

`$request` contains all validation rules for the controller implementing the `BaseController`.

### Example usage
In the following example we will create a `ContractController` that provides a simple CRUD for storing, retrieving, deleteing and updating a contract.

```php
<?php
namespace Clockwork\Contracts\Http\Controllers;

use Clockwork\Contracts\Interfaces\ContractRepositoryInterface;
use Clockwork\Contracts\Http\Resources\ContractResource;
use Clockwork\Base\BaseController;

class ContractController extends BaseController
{
    public function __construct(ContractRepositoryInterface $contract)
    {
        $this->model = $contract;
        $this->resource = '\Clockwork\Contracts\Http\Resources\ContractResource';
    }
}
```
## Extending BaseController
Because not all the BaseController's methods can be used out of the box for every situation, it is possible to extend the methods in your own controllers that implements the `BaseController`.

### Example
The index function returns a collection. The response must be an instance of object (Collection)
```php
    return $this->resource::collection(
        $this->model->getAll($request)
    );
```
If we want to add a new method to our query `getAll()` in our `ContractController`. We could simply create an index method in `ContractController` and perform our custom logic there.

```php
    return $this->resource::collection(
        $this->model->getAll($request)
        // Here we filter our results if `$request` has the month variable.
        ->when($request->get('month') !== 'all', function ($collection) use ($request) {
            return $collection->filter(function ($item) use ($request) {
                return $item->getRemaining($request->get('month')) > 0;
            });
        })->values()
    );
```