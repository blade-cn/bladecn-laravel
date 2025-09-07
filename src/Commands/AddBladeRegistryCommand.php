<?php

declare(strict_types=1);

namespace Bladecn\Commands;

use Bladecn\Actions\ExtractRemoteUrl;
use Bladecn\Actions\RegistryClient;
use Bladecn\Actions\Validations\ValidateConfigData;
use Bladecn\Actions\Validations\ValidateRegistryUrl;
use Bladecn\Exceptions\AlreadyExistsRemoteUrlException;
use Bladecn\Exceptions\InvalidRemoteUrlException;
use Bladecn\RegistryConfig;
use Bladecn\Services\Cache;
use Bladecn\ValueObjects\RemoteRegistry;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\note;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;

class AddBladeRegistryCommand extends Command
{
    protected $signature = 'bladecn:add-registry
        {url? : The URL of the registry (Git repository).}';

    protected $description = 'Add bladecn registry to your project.';

    public function __construct(
        protected ValidateRegistryUrl $validateRegistryUrl,
        protected ValidateConfigData $validateConfigData,
        protected Cache $cache,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        RegistryConfig::flush();

        /** @var ?string $url */
        $url = $this->argument('url');

        if (! is_null($url) && ($this->validateRegistryUrl)($url)->fails()) {
            error(($this->validateRegistryUrl)($url)->errors()->first('url'));

            return Command::FAILURE;
        }

        if (empty($url)) {
            $url = text(
                label: 'Please enter your BladeCN registry URL:',
                placeholder: 'https://github.com/vendor/repo',
                hint: 'The URL must point to a Git repository containing the registry configuration file. Supported platforms: GitHub, GitLab, Bitbucket.',
                required: true,
                validate: function (string $value) {
                    $validator = ($this->validateRegistryUrl)($value);

                    if ($validator->fails()) {
                        return $validator->errors()->first('url');
                    }

                    return null;
                },
            );
        }

        try {
            if (RegistryConfig::make()->existsRegistry($url)) {
                throw new AlreadyExistsRemoteUrlException($url);
            }

            $resolvedRepoUrl = app(ExtractRemoteUrl::class)(trim($url));
            $response = RegistryClient::make($resolvedRepoUrl->rawContentUrl(), $resolvedRepoUrl->accessToken())
                ->get(RegistryConfig::JSON_REGISTRY_CONFIG_NAME);

            $data = ($this->validateConfigData)($response->json())->validated();
            $registry = RemoteRegistry::from($data);

            note('Registry Information:');

            table(
                headers: ['Name', 'Registry', 'Authors', 'Description', 'Version', 'Last Updated'],
                rows: [
                    [$registry->name, $url, implode(', ', $registry->authors), $registry->description, $registry->version, now()->toDateTimeString()],
                ],
            );

            confirm(
                label: sprintf('Confirm Registry URL: %s', $url),
                hint: 'Do you want to proceed?',
                default: true,
            ) || exit(self::FAILURE);

            $this->cache->put($url, $registry);

            RegistryConfig::make()->persist($url);

            note(sprintf('Registry [%s] added successfully.', $registry->name));

            return self::SUCCESS;
        } catch (RequestException $e) {
            if ($e->response->notFound()) {
                error(sprintf('The registry file was not found at the specified URL [%s].', $url));
            } elseif ($e->response->clientError()) {
                error('The request to the registry resulted in a client error. Please check the URL and permissions.');
            } elseif ($e->response->serverError()) {
                error('The registry server is experiencing issues. Please try again later.');
            }

            return self::FAILURE;
        } catch (AlreadyExistsRemoteUrlException|InvalidRemoteUrlException $e) {
            error($e->getMessage());

            return self::FAILURE;
        } catch (ValidationException $e) {
            error(sprintf('The registry configuration file is invalid or malformed: %s', $e->getMessage()));

            return self::FAILURE;
        }
    }
}
