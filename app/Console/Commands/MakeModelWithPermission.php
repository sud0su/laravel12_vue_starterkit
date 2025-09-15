<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class MakeModelWithPermission extends Command
{
    protected $signature = 'make:model-with-permission {name} {--migrate}';
    protected $description = 'Generate model, migration, and auto create permissions with fillable from DB';

    public function handle()
    {
        $name = $this->argument('name');
        $modelName = Str::studly($name);
        $table = Str::snake(Str::pluralStudly($name)); // ex: Post => posts

        // 1. Generate model (plus migration kalau diminta)
        $params = ['name' => $modelName];
        if ($this->option('migrate')) {
            $params['--migration'] = true;
        }
        $this->call('make:model', $params);

        // 2. Generate permissions
        $permissions = [
            "view {$table}",
            "create {$table}",
            "edit {$table}",
            "delete {$table}",
            "approve {$table}",
            "publish {$table}",
            "archive {$table}",
            "restore {$table}",
            "export {$table}",
            "import {$table}",
            "manage {$table}",
            "assign {$table}",
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Pastikan tabel sudah ada
        if (!Schema::hasTable($table)) {
            $this->warn("⚠️  Table '{$table}' belum ada. Jalankan migrate dulu, baru jalankan command ini lagi untuk update fillable.");
            return;
        }

        // 4. Ambil kolom tabel & filter default
        $columns = Schema::getColumnListing($table);
        $ignore = ['id', 'created_at', 'updated_at', 'deleted_at'];
        $fillable = array_values(array_diff($columns, $ignore));

        // 5. Update file model
        $modelPath = app_path("Models/{$modelName}.php");
        if (file_exists($modelPath)) {
            $content = file_get_contents($modelPath);

            // Sisipkan fillable kalau belum ada
            if (!str_contains($content, '$fillable')) {
                $fillableCode = "    protected \$fillable = " . var_export($fillable, true) . ";\n";
                $content = preg_replace('/\{/', "{\n\n$fillableCode", $content, 1);
                file_put_contents($modelPath, $content);
                $this->info("✅ Fillable ditambahkan ke model {$modelName}.");
            } else {
                $this->warn("⚠️ Model {$modelName} sudah punya fillable.");
            }
        }

        $this->info("✅ Model {$modelName}, permissions, dan fillable selesai dibuat.");
    }
}
