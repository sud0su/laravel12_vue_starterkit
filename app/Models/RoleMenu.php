<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role;

class RoleMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'title',
        'href',
        'icon',
        'order',
        'parent_id',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the role that owns the menu item.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the parent menu item.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(RoleMenu::class, 'parent_id');
    }

    /**
     * Get the child menu items.
     */
    public function children(): HasMany
    {
        return $this->hasMany(RoleMenu::class, 'parent_id')->orderBy('order');
    }

    /**
     * Scope to get menu items for specific roles.
     */
    public function scopeForRoles($query, array $roleIds)
    {
        return $query->whereIn('role_id', $roleIds)->orderBy('order');
    }

    /**
     * Scope to get top-level menu items (no parent).
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}
