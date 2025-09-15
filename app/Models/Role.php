<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    // This class extends the base Role model from the Spatie package.
    // By using this local model, we can ensure consistent policy resolution by Laravel's Gate.
}
