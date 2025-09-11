<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role;

class RoleMenu extends Model
{
    protected $fillable = [
        'role_id',
        'title',
        'href',
        'icon',
        'order',
        'parent_id',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function parent()
    {
        return $this->belongsTo(RoleMenu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(RoleMenu::class, 'parent_id');
    }
}
