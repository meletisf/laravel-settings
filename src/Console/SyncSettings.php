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

    protected $description = 'Sync the settings which do not exist in the database';

    public function handle(): int
    {
        $this->info('Syncing settings...');

        /** @var Settings $service */
        $service = resolve('laravel-settings');

        $entries = $this->getExistingEntries($service->getModelFqn());

        $requiredConfiguration = config('laravel-settings.required_configuration');

        $existingKeys = [];

        /** @var Setting $entry */
        foreach ($entries as $entry) {
            $existingKeys[] = $entry->key;
        }

        $log = [];

        foreach ($requiredConfiguration as $k => $v) {
            // only sync settings that do not already exist in the database
            if (!in_array($k, $existingKeys)) {
                // if a value is an array, then the is_immutable flag has been set too
                if (is_array($v)) {
                    $service->set($k, $v[0], $v[1]);
                } else {
                    $service->set($k, $v);
                }
                $log[] = [$k, $service->get($k, true), 'created'];
            } else {
                $log[] = [$k, $service->get($k, true), 'existing value'];
            }
        }

        $this->info('Settings synced:');
        $this->table(['Key', 'Value', 'Action'], $log);

        return self::SUCCESS;
    }

    private function getExistingEntries(string $model): \Illuminate\Database\Eloquent\Collection
    {
        return $model::all();
    }
}
