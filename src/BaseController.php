<?php
namespace IlyasDeckers\BaseModule;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use IlyasDeckers\BaseModule\Interfaces\BaseControllerInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

abstract class BaseController extends Controller implements BaseControllerInterface
{
    use AuthorizesRequests;

    /**
     * Model being used by the controller
     *
     * @var object
     */
    protected $model;

    /**
     * Class reference to the API resource being used on the 
     * controller's methods.
     *
     * @var object
     */
    protected $resource;

    /**
     * Class references that contain the validation rules 
     * for the given request defined in an array that uses 
     * the key as method name and a Request class.
     *
     * @var array
     */
    protected array $rules = [];

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index(Request $request) : object
    {
        return $this->resource::collection(
            $this->model->getAll($request)
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function show(Request $request) : object
    {
        return new $this->resource(
            $this->model->find($request)
        );
    }

    /**
     * Store a newly created resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function store(Request $request) : object
    {
        $result = $this->model->store($request);

        if ($result instanceof Collection) {
            return $this->resource::collection($result);
        }

        return new $this->resource($result);
    }

    /**
     * Update the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function update(Request $request) : object
    {
        return new $this->resource(
            $this->model->update($request->validated)
        );
    }

    /**
     * Remove the specified resource.
     *
     * @param  int  $id
     * @return mixed
     */
    public function destroy(int $id) : void
    {
        $this->model->destroy($id);
    }

    /**
     * Returns an array with the validation rules
     * 
     * @return array
     */
    public function getRules() : array
    {
        return $this->rules;
    }
}
