<?php
namespace Clockwork\Base;

use DB;
use Closure;
Use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TransactionMiddleware
{
    /**
     * Apply database transactions on POST, PUT and DELETE
     * requests.
     *
     * @param Request $method
     * @param Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next) : Response
    {
        if ($request->isMethod('get')) {
            return $next($request);
        }

        try {
            DB::beginTransaction();
            $response = $next($request);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        if ($response instanceof Response && $response->getStatusCode() > 399) {
            DB::rollBack();
        } else {
            DB::commit();
        }

        return $response;
    }
}