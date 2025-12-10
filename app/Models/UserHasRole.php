<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserHasRole extends Pivot
{
    protected $table = 'user_has_roles';
    protected $fillable = [
        'user_id',
        'role_id',
    ];
    
    public $incrementing = false;
    public $timestamps = false;

}
