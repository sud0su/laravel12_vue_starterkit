<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckRoleMenus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-role-menus {--fix : Remove duplicate menu entries}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for duplicate menu access in role_menus table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for duplicate menu access...');

        $menus = \App\Models\RoleMenu::all();

        // Group by role_id, title, and href to find duplicates
        $duplicates = $menus->groupBy(function ($item) {
            return $item->role_id . '|' . $item->title . '|' . $item->href;
        })->filter(function ($group) {
            return $group->count() > 1;
        });

        if ($duplicates->isEmpty()) {
            $this->info('No duplicate menu access found.');
            return;
        }

        $this->warn('Duplicate menu access found:');
        foreach ($duplicates as $key => $group) {
            list($roleId, $title, $href) = explode('|', $key);
            $this->line("Role ID: {$roleId}, Title: {$title}, Href: {$href}, Count: {$group->count()}");
        }

        $this->info('Total duplicate groups: ' . $duplicates->count());

        if ($this->option('fix')) {
            $this->fixDuplicates($duplicates);
        }
    }

    private function fixDuplicates($duplicates)
    {
        $this->info('Fixing duplicates...');

        foreach ($duplicates as $group) {
            // Keep the first item, delete the rest
            $keep = $group->first();
            $deleteIds = $group->skip(1)->pluck('id');
            \App\Models\RoleMenu::whereIn('id', $deleteIds)->delete();
            $this->line("Kept ID: {$keep->id}, Deleted: " . $deleteIds->implode(', '));
        }

        $this->info('Duplicates fixed.');
    }
}
