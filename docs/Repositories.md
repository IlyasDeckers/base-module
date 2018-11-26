# Repositories

## Introduction
Repositories are used to create methods are reusable in the other parts of the application by doing Dependency Injection (DI). In this implementation of repositories we create a layer between our Controllers and Models, to extend and/or modify eloquent models.

## 1. BaseRepository
### 1.1 Introduction
The `BaseRepository` is used as a starting point for all repositories. This enables us to create and reuse methods shared by all repositories in our application.

### 1.2 Methods
The BaseRepository contains some methods that the BaseController uses on the index and show methods.
#### `find()`
Returns a single item from the database. With relations and scopes loaded.

#### `getAll()`
Returns a collection from the database. With relations and scopes loaded.

### 1.4 Loading relations and scopes
We extend the query builder in the BaseRepository to load in relations and scopes dynamically.

* `collectionResponse()` - This method loads in relations and scopes on the requested model by extending the `Builder` for a Collection.

* `itemResponse()` - This method loads in relations and scopes on the requested model by extending the `Builder` for a single  item.

*For example:*
In the `getAll()` method we find the following code.

```php
    return $this->collectionResponse(
        $request, // The request
        $this->model // An instance of the model e.g. \Clockwork\Models\User
    );
```

In the method `collectionResponse()` we find a bunch of statements to append the query. In these statements we check if a property exists, if it exists we execute a callback function to load relationships, apply scopes,... After we have applied these methods, we return the Eloquent collection.

```php
    $query = $query
        ->when($request->has('filter'), [$this, 'filter'])
        ->when($request->has('with'), [$this, 'with']);

    return $query->get();
```
## 2. Database Transactions
Database transactions are applied by the trait `Transactions` on all our queries. When an (unhandled) exception happens during one of the database queries the transaction is rolled back to prevent data corruption.
```php
    public function __call(string $method, array $args)
    {
        try {
            DB::beginTransaction();
            // Check if the method exists on the class this trait 
            // has been implemented in. Next we call this function.
            $this->methodExists($method);
            $response = call_user_func_array([$this, $method], $args);
            DB::commit();
        } catch (Exception $e) {
            // If the method call throws an exception rollback the 
            // database queries and format the exception.
            DB::rollback();
            throw new Exception($e->getMessage());
        }

        return $response;
    }
```