<?php

/*
 * This file is part of the Laravel Settings project.
 *
 * All copyright for project Laravel Settings are held by Meletios Flevarakis, 2021.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meletisf\Settings\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Meletisf\Settings\Models\Setting;
use Meletisf\Settings\Settings;

class SyncSettings extends Command
{
    protected $signature = 'settings:sync';

    protected $description = 'Sync the settings which do not exists in the database';

    public function handle()
    {
        $this->info('Syncing settings...');
        $entries = $this->getExistingEntries();
        $requiredConfiguration = config('laravel-settings.required_configuration');

        $existingKeys = [];

        /** @var Setting $entry */
        foreach ($entries as $entry) {
            $existingKeys[] = $entry->key;
        }

        /** @var Settings $service */
        $service = resolve('laravel-settings');

        $log = [];

        foreach ($requiredConfiguration as $k => $v) {
            if (! in_array($k, $existingKeys)) {
                $service->set($k, $v);
                $log[] = [$k, $service->get($k, true), 'created'];
            }
            else {
                $log[] = [$k, $service->get($k, true), 'ignored, existing value'];
            }
        }

        $this->info('Settings synced:');
        $this->table(['Key', 'Value', 'Action'], $log);
    }

    private function getExistingEntries(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getNewModel()::all();
    }

    private function getNewModel(): Model
    {
        $class = config('laravel-settings.settings_model');
        return new $class;
    }
}