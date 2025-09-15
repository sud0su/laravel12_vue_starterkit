<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Spatie\Permission\Models\Permission;

class GenerateResourceCommand extends Command
{
    protected $signature = 'resource:generate {name}';
    protected $description = 'Generate a resource with controller, policy, permissions, route, and CRUD views.';
    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $studlyName = Str::studly($name);
        $pluralName = Str::plural($studlyName);
        $lowerPluralName = Str::lower($pluralName);
        $lowerName = Str::lower($name);

        // 1. Generate Controller
        Artisan::call('make:controller', [
            'name' => "{$studlyName}Controller",
            '--resource' => true,
        ]);
        $this->info("Controller [app/Http/Controllers/{$studlyName}Controller.php] created successfully.");

        // 2. Generate Policy
        Artisan::call('make:policy', [
            'name' => "{$studlyName}Policy",
            '--model' => $studlyName,
        ]);
        $this->info("Policy [app/Policies/{$studlyName}Policy.php] created successfully.");

        // 3. Generate Permissions
        $this->createPermissionsForModel($lowerName);
        $this->info("Permissions for [{$lowerName}] created successfully.");

        // 4. Add Route
        $route = "Route::resource('/{$lowerPluralName}', App\Http\Controllers\\{$studlyName}Controller::class);";
        $marker = '// {{GEMINI_RESOURCE_ROUTES}}';
        $routesFile = base_path('routes/web.php');
        $content = $this->files->get($routesFile);

        if (str_contains($content, $marker)) {
            $newContent = str_replace($marker, "    {$route}\n    {$marker}", $content);
            $this->files->put($routesFile, $newContent);
            $this->info("Route for [{$lowerPluralName}] added to routes/web.php.");
        } else {
            $this->warn("Marker not found in routes/web.php. Please add `{$marker}` manually.");
        }

        // 5. Generate CRUD views
        $this->createCrudViews($studlyName, $lowerPluralName, $lowerName);
        $this->info("CRUD views for [{$studlyName}] created successfully.");

        $this->info("Resource [{$name}] generated successfully!");

        return 0;
    }

    private function createPermissionsForModel(string $model): void
    {
        $permissions = [
            "view {$model}",
            "create {$model}",
            "edit {$model}",
            "delete {$model}",
            "approve {$model}",
            "publish {$model}",
            "archive {$model}",
            "restore {$model}",
            "export {$model}",
            "import {$model}",
            "manage {$model}",
            "assign {$model}",
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }

    private function createCrudViews(string $studlyName, string $lowerPluralName, string $lowerName)
    {
        $path = resource_path("js/pages/{$studlyName}");
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true);
        }

        $this->createView($path, 'Index.vue', $this->getIndexViewStub($studlyName, $lowerPluralName));
        $this->createView($path, 'Create.vue', $this->getCreateViewStub($studlyName, $lowerPluralName, $lowerName));
        $this->createView($path, 'Edit.vue', $this->getEditViewStub($studlyName, $lowerPluralName, $lowerName));
    }

    private function createView($path, $fileName, $stub)
    {
        $this->files->put("{$path}/{$fileName}", $stub);
    }

    private function getIndexViewStub($studlyName, $lowerPluralName)
    {
        return <<<EOD
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

interface Props {
  {$lowerPluralName}: {
    data: any[];
  };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: '{$studlyName}', href: '/{$lowerPluralName}' },
];
</script>

<template>
  <Head title="{$studlyName} Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
        <div>
          <h1 class="text-2xl font-semibold">{$studlyName} Management</h1>
          <p class="text-sm text-muted-foreground">A list of all the {$lowerPluralName}.</p>
        </div>
        <Button as-child>
          <Link href="/{$lowerPluralName}/create">Create {$studlyName}</Link>
        </Button>
      </div>
      <div>
        <!-- Add your table or list of {$lowerPluralName} here -->
        <pre>{{ props.{$lowerPluralName}.data }}</pre>
      </div>
    </div>
  </AppLayout>
</template>
EOD;
    }

    private function getCreateViewStub($studlyName, $lowerPluralName, $lowerName)
    {
        return <<<EOD
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: '{$studlyName}', href: '/{$lowerPluralName}' },
  { title: 'Create', href: '/{$lowerPluralName}/create' },
];

const form = useForm({
  // Add your form fields here
});

function submit() {
  form.post('/{$lowerPluralName}');
}
</script>

<template>
  <Head title="Create {$studlyName}" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="flex items-center justify-end">
            <Button variant="ghost" as-child>
                <Link href="/{$lowerPluralName}">Back to {$studlyName} &rarr;</Link>
            </Button>
        </div>
        <div class="mx-auto w-full max-w-4xl">
            <Card>
                <form @submit.prevent="submit">
                    <CardHeader>
                        <CardTitle>Create New {$studlyName}</CardTitle>
                        <CardDescription>Fill in the details to create a new {$lowerName}.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <!-- Add your form fields here -->
                        <p>Add your form fields here.</p>
                    </CardContent>
                    <CardFooter class="flex justify-end gap-4 border-t pt-6">
                        <Button variant="outline" as-child><Link href="/{$lowerPluralName}">Cancel</Link></Button>
                        <Button type="submit" :disabled="form.processing">Create</Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </div>
  </AppLayout>
</template>
EOD;
    }

    private function getEditViewStub($studlyName, $lowerPluralName, $lowerName)
    {
        return <<<EOD
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';

const props = defineProps<{ {$lowerName}: any }>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: '{$studlyName}', href: '/{$lowerPluralName}' },
  { title: 'Edit', href: `/{$lowerPluralName}/\${props.{$lowerName}.id}/edit` },
];

const form = useForm({
  // Add your form fields here, initialized with props.{$lowerName}
});

function submit() {
  form.put(`/{$lowerPluralName}/\${props.{$lowerName}.id}`);
}
</script>

<template>
  <Head title="Edit {$studlyName}" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="flex items-center justify-end">
            <Button variant="ghost" as-child>
                <Link href="/{$lowerPluralName}">Back to {$studlyName} &rarr;</Link>
            </Button>
        </div>
        <div class="mx-auto w-full max-w-4xl">
            <Card>
                <form @submit.prevent="submit">
                    <CardHeader>
                        <CardTitle>Edit {$studlyName}</CardTitle>
                        <CardDescription>Update the details of the {$lowerName}.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <!-- Add your form fields here -->
                        <p>Add your form fields here.</p>
                    </CardContent>
                    <CardFooter class="flex justify-end gap-4 border-t pt-6">
                        <Button variant="outline" as-child><Link href="/{$lowerPluralName}">Cancel</Link></Button>
                        <Button type="submit" :disabled="form.processing">Update</Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </div>
  </AppLayout>
</template>
EOD;
    }
}
