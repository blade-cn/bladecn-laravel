<?php

declare(strict_types=1);

namespace Bladecn\Commands;

use Bladecn\Actions\ExtractRemoteUrl;
use Bladecn\Actions\ValidateRemoteRegistryData;
use Bladecn\Enums\SourceControl;
use Bladecn\Exceptions\AlreadyExistsRemoteUrlException;
use Bladecn\Exceptions\InvalidRemoteUrlException;
use Bladecn\RegistryConfig;
use Bladecn\ValueObjects\RemoteRegistry;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\error;
use function Laravel\Prompts\note;
use function Laravel\Prompts\text;

#[AsCommand(name: 'bladecn:add-registry', description: 'Add bladecn registry to your project.')]
class AddBladeRegistryCommand extends Command
{
    public function handle(): int
    {
        $url = text(
            label: 'Please enter your BladeCN registry URL:',
            placeholder: 'https://github.com/vendor/repo',
            hint: 'The URL must point to a Git repository containing the registry configuration file. Supported platforms: GitHub, GitLab, Bitbucket.',
            required: true,
            validate: fn (string $value) => match (true) {
                ! filter_var($value, FILTER_VALIDATE_URL) => 'The URL is invalid.',
                parse_url($value, PHP_URL_SCHEME) !== 'https' => 'The URL must use HTTPS scheme.',
                ! collect(SourceControl::toArray())->contains(fn ($platform) => str_contains(parse_url($value, PHP_URL_HOST) ?? '', $platform)) => 'The URL must be from a supported source control platform (GitHub, GitLab, Bitbucket).',
                default => null
            }
        );

        $resolvedRepoUrl = app(ExtractRemoteUrl::class)(trim($url));
        $requestUrl = sprintf('%s/%s', $resolvedRepoUrl->rawContentUrl(), RegistryConfig::JSON_REGISTRY_CONFIG_NAME);

        try {
            $response = Http::timeout(5)
                ->retry(3, 100)
                ->withHeader('Accept', 'application/json')
                ->get($requestUrl);

            $data = app(ValidateRemoteRegistryData::class)($response->json())->validated();
            $registry = RemoteRegistry::from($data);

            RegistryConfig::make()->persist($registry);

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
