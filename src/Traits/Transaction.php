<?php
namespace Clockwork\Base\Traits;

use DB;
use Exception;

// Soon to be deprecated
trait Transaction
{
    /**
     * Call the private function with database transactions
     * for the methods store, update and delete.
     *
     * When the called function is set to public, __call will
     * be omitted and no transactions are perfomed.
     *
     * @param string $method
     * @param array $args
     * @return void
     */
    public function __call(string $method, array $args)
    {
        try {
            DB::beginTransaction();
            // Check if the method exists on the class this trait 
            // has been implemented in. Next we call this function.
            if (!method_exists($this, $method)) {
                throw new Exception("Method '{$method}' doesn't exist");
            }

            $response = call_user_func_array([$this, $method], $args);
            DB::commit();
        } catch (Exception $e) {
            // If the method call throws an exception rollback the 
            // database queries and format the exception.
            DB::rollback();
            throw new Exception($e->getMessage());
        }

        // Check if the method exists on the class this trait 
        // has been implemented in. Next we call this function.
        if (!method_exists($this, $method)) {
            throw new Exception("Method '{$method}' doesn't exist");
        }

        $response = call_user_func_array([$this, $method], $args);

        return $response;
    }
}
