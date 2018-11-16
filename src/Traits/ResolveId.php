<?php
namespace Clockwork\Base\Traits;

<<<<<<< HEAD
use Auth;

=======
>>>>>>> implement editable & resolveid traits
trait ResolveId
{
    /**
     * Resolve the ID of the logged User.
     *
     * @return mixed|null
     */
    public static function resolveId()
    {
        return Auth::check() ? Auth::user()->getAuthIdentifier() : null;
    }
}