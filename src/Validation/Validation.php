<?php

namespace Clockwork\Base\Validation;

use Illuminate\Http\Request;

/**
 * Class Validation
 *
 * @package App\Validation
 */
class Validation
{
    /**
     * @var array ignore
     */
    protected $ignore = [];

    protected $rules;

    /**
     * Specify laravel style rules
     *
     * @return array
     */
    public function rules(array $rules) : array 
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Specify rules you want to ignore for the following validation
     *
     * @param ...$ignore
     *
     * @return $this
     */
    public final function ignore(...$ignore)
    {
        $this->ignore = $ignore;

        return $this;
    }

    /**
     * Perform validation
     *
     * @param array|Request $input
     *
     * @throws \App\Exceptions\Errors
     */
    public final function run($input)
    {
        if ($input instanceof Request) {
            $input = $input->all();
        } elseif (!is_array($input)) {
            $input = (array) $input;
        }

        if (method_exists($this, 'before')) {
            $this->before($input);
        }

        validate($input, $this->rules);

        if (method_exists($this, 'after')) {
            $this->after($input);
        }
    }
}