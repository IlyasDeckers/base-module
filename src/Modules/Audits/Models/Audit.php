<?php

namespace IlyasDeckers\BaseModule\Audits\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model implements \OwenIt\Auditing\Contracts\Audit
{
    use \OwenIt\Auditing\Audit;

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'old_values'   => 'json',
        'new_values'   => 'json',
        'auditable_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(\Clockwork\Users\Models\User::class, 'user_id');
    }

    public function auditable()
    {
        return $this->morphTo('auditable');
    }

    public function scopeFromManager($query)
    {
        return $query->whereHas('user', function ($q) {
            return $q->where('role', 'manager');
        });
    }

    public function scopeByMonth($query)
    {
        return $query->where('created_at', '<=', request()->end)
            ->where('created_at', '>=', request()->start);
    }
}
