<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $role_id
 * @property string $title
 * @property string $href
 * @property string|null $icon
 * @property int $order
 * @property int|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, RoleMenu> $children
 * @property-read int|null $children_count
 * @property-read RoleMenu|null $parent
 * @property-read \Spatie\Permission\Models\Role $role
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenu query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenu whereHref($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenu whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenu whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenu whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenu whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenu whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleMenu whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRoleMenu {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

