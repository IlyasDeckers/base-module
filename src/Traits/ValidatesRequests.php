<?php
namespace Clockwork\Base\Traits;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\ValidationException;

use Illuminate\Http\JsonResponse;
use Clockwork\Exceptions\EnforcementException;

trait ValidatesRequests
{
    public function validator(string $function, object $request)
    {
        if (isset($this->rules[$function]) && !is_null($this->rules[$function])) {
            $this->validate($request,
                (new $this->rules[$function])->rules()
            );
        }
    }
    
    /**
     * Run the validation routine against the given validator.
     *
     * @param  \Illuminate\Contracts\Validation\Validator|array  $validator
     * @param  \Illuminate\Http\Request|null  $request
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateWith($validator, Request $request = null)
    {
        $request = $request ?: request();

        if (is_array($validator)) {
            $validator = $this->getValidationFactory()->make($request->all(), $validator);
        }

        return $validator->validate();
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(Request $request, array $rules,
                             array $messages = [], array $customAttributes = [])
    {
        return $this->getValidationFactory()->make(
            $request->all(), $rules, $messages, $customAttributes
        )->validate();
    }

    /**
     *  Apply rules to the given request
     * 
     * @param $rules
     * @throws ValidationException
     */
    public function enforce($rules, $status = 412)
    {
        $messages = tap(collect(), function ($messages) use ($rules) {
            collect($rules)->each(function ($rule) use ($messages) {
                if (!$rule->passes()) {
                    $messages->push([
                        'code' => $rule->code(),
                        'message' => $rule->message()
                    ]);
                    if ($rule->break()) {
                        return false;
                    }
                }
            });
        });

        if (!$messages->isEmpty()) {
            throw new EnforcementException(
                new JsonResponse([
                    'messages' => $messages
                ], $status)
            );
        }
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param  string  $errorBag
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateWithBag($errorBag, Request $request, array $rules,
                                    array $messages = [], array $customAttributes = [])
    {
        try {
            return $this->validate($request, $rules, $messages, $customAttributes);
        } catch (ValidationException $e) {
            $e->errorBag = $errorBag;

            throw $e;
        }
    }

    /**
     * Get a validation factory instance.
     *
     * @return \Illuminate\Contracts\Validation\Factory
     */
    protected function getValidationFactory()
    {
        return app(Factory::class);
    }
}