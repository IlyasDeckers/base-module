# Controllers

1. Introduction
2. Request validation
3. JSON responses

## Introduction
Controllers are group related request handling logic into a single class.

Our take on controllers is to keep the classes as clean, minimal and understandable as possible. While designing API's I often noticed how we repeat certain logic over and over again. Some examples:

- controllers use request validation
- controllers return a JSON response
- often have the same methods

By extending the BaseController on your controllers you gain access to all the methods provided by the BaseController to create a simple CRUD that works (almost) straight out of the box.

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

The controller then passes the request to the provided repository to get/store/update/... the requested resource. 

## Request validation
To implement request validations on your controllers, simply add an array with the method names as the key and the request class as the value. If no validation is required on one of the controller's methods you can define the validation class as null in the `$rules` array.

```php
    protected array $rules = [
        'update' => UpdateRequest::class,
        'create' => null,
    ];
```

The logic handling the validations is handled by the `ValidatorMiddleware`. Where the validator method handles our validation implementation.

```php
    public function validator(string $function, Request $request)
    {
        if (isset($this->rules[$function]) && !is_null($this->rules[$function])) {
            $this->validate($request,
                (new $this->rules[$function])->rules()
            );
        }
    }
```
In the request class we define our rules as normal Laravel request classes. These rules are resolved in the `ValidatorMiddleware` and applied accordingly. In contrast to Laravel's method we do not use dependency injection (DI) to achieve this. It is not possible to provide the request validation via DI without overwriting the parent's methods in the child controller.

If validation fails an HTTP response with a 422 status code will be returned to the user including a JSON representation of the validation errors.

The validated fields are added to the request instance under the property `validated`. These validated fields are passed to our repositories via our controllers. This is extremely important to only send the `validated` properties to our repositories as we want to sanitize faulty user input for security.

```php
    var_dump(request()->validated);

    array:19 [
        "id" => 612
        ...
        "email" => "test@mail.com"
        "telephone" => "0412312312"
        "company" => "Comp BV"
        "company_email" => null
        "company_telephone" => null
        "vat_number" => "BE012312312"
        "vat_validated" => 0
        "standard_base" => 0
        ...
    ]
```

An example `Request` class for validation. (https://laravel.com/docs/5.0/validation#form-request-validation)

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

## JSON responses

The abstract methods provided by the `BaseController` return valid JSON responses using Laravel's API resources to allow you to expressively and easily transform your models and model collections into JSON. It acts as a transformation layer that sits between our repositories and the JSON responses that are returned by our API via our controllers. API resources are made of two entities: a resource class and a resource collection. A resource class represents a single model that needs to be transformed into a JSON structure, while a resource collection is used for transforming collections of models into a JSON structure.

To use these resources you will have to create a resource class and define this on your controller as a string representation on the property `$resource`.

```php
    /**
     * Api resource
     *
     * @var Clockwork\Users\Http\Resources\UserResource
     */
    protected $resource = UserResource::class;

    public function __construct(...)
    {
        // ...
    }
```

Not defining this resource will return a `FatalThrowableError`!

> Class name must be a valid object or a string
