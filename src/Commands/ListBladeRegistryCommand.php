<?php

declare(strict_types=1);

namespace Bladecn\Commands;

use Bladecn\Actions\ExtractRemoteUrl;
use Bladecn\RegistryConfig;
use Bladecn\RegistryManager;
use Bladecn\Services\Cache;
use Bladecn\ValueObjects\RemoteRegistry;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\note;
use function Laravel\Prompts\table;

class ListBladeRegistryCommand extends Command
{
    protected $signature = 'bladecn:list-registry';

    protected $description = 'List all configured registries.';

    public function __construct(
        protected Cache $cache,
        protected RegistryManager $registryManager,
        protected ExtractRemoteUrl $extractRemoteUrl,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        note('ℹ️ Registry Information:');

        $registryConfig = RegistryConfig::make();

        $registries = collect($registryConfig->config->registries)
            ->map(function (string $url) {
                /** @var ?RemoteRegistry $registry */
                $registry = $this->cache->get($url);

                $repo = ($this->extractRemoteUrl)($url);
                $driver = $this->registryManager->driver($repo->source->value);

                if ($registry === null) {
                    $registry = $driver->fetchRegistry($repo);
                    $this->cache->put($url, $registry);
                }

                $isAvailable = $driver->checkIsAvailable($repo);

                return [
                    'name' => $registry->name,
                    'registry' => $url,
                    'authors' => empty($registry->authors) ? implode(', ', $registry->authors) : 'N/A',
                    'description' => $registry->description ?? 'N/A',
                    'version' => $registry->version ?? 'N/A',
                    'lastUpdated' => $registry->lastUpdated->format('d/m/y'),
                    'cached_at' => $this->cache->getTimestamp($url)?->format('d/m/y') ?? 'N/A',
                    'available' => $isAvailable,
                ];
            });

        $registryList = $registries->map(
            fn (array $registry) => array_merge($registry, ['available' => ($registry['available']) ? '✅' : '❌'])
        );

        table(
            headers: ['Name', 'Registry', 'Authors', 'Description', 'Version', 'Last Updated', 'Cached At', 'Available'],
            rows: $registryList->toArray(),
        );

        $unavailableRegistries = $registries->where('available', false);

        if ($unavailableRegistries->isNotEmpty()) {
            table(
                headers: ['Name', 'Registry', 'Authors', 'Description', 'Version', 'Last Updated', 'Cached At'],
                rows: $unavailableRegistries->toArray(),
            );

            if (confirm('Do you want to remove the unavailable registries?', false)) {
                $unavailableRegistries->each(fn (array $registry) => $this->cache->flush($registry['registry']));

                $registryConfig->removeRegistries($unavailableRegistries->pluck('registry')->all());

                note('Unavailable registries have been removed.');
            }
        }

        return self::SUCCESS;
    }
}
