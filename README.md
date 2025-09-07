# BladeCN

BladeCN is a project inspired by [shadcn](https://shadcn.com), aiming to provide a similar experience for Blade components. It allows you to host your own or extend existing Blade components through a registry system. This makes it easy to share and reuse Blade components across different projects.

> **Note:** This package is in early development. Please use with caution.

## Installation

You can install the package via composer:

```bash
composer require --dev blade-cn/bladecn-laravel
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="bladecn-laravel-config"
```

This is the contents of the published config file:

```php
return [
    'source' => [
        'github' => [
            'branch' => env('BLADECN_GITHUB_DEFAULT_BRANCH', 'main'),
            'token' => env('BLADECN_GITHUB_ACCESS_TOKEN', null),
        ],

        'gitlab' => [
            'branch' => env('BLADECN_GITLAB_DEFAULT_BRANCH', 'main'),
            'token' => env('BLADECN_GITLAB_ACCESS_TOKEN', null),
        ],

        'bitbucket' => [
            'branch' => env('BLADECN_BITBUCKET_DEFAULT_BRANCH', 'main'),
            'token' => env('BLADECN_BITBUCKET_ACCESS_TOKEN', null),
        ],
    ],

    'cache_ttl' => env('BLADECN_CACHE_TTL', 86400), // 24 hours
];
```

## Usage

### Adding a Registry

To add a new registry, you can use the `bladecn:add-registry` Artisan command. You can either run it interactively or provide the URL directly.

```bash
php artisan bladecn:add-registry
```

or

```bash
php artisan bladecn:add-registry https://github.com/my-registry/my-blade-components
```

This command will create or update the `bladecn-registry.json` file in the root of your project. This file contains a list of all the registries you have added.

It's important that your registry repository contains a valid `bladecn-registry.json` file in the root of the repository.

## `bladecn-registry.json`

The `bladecn-registry.json` file is the heart of the BladeCN registry system. It defines the metadata for your registry and lists all the available components.

### Schema

Here is the full schema for the `bladecn-registry.json` file:

```json
{
    "$schema": "https://github.com/blade-cn/bladecn-laravel/schemas/bladecn-registry.schema.json",
    "name": "string",
    "homepage": "string (optional)",
    "authors": "array (optional)",
    "description": "string (optional)",
    "owner": "string (optional)",
    "version": "string",
    "last_updated": "string (date-time)",
    "items": [
        {
            "name": "string",
            "type": "enum (component | block | page | ui)",
            "description": "string",
            "tags": "array (optional)",
            "nodeDependencies": "object (optional)",
            "dependencies": "object (optional)",
            "files": "array",
            "cssVars": "array (optional)"
        }
    ]
}
```

### Registry Metadata

| Field          | Type     | Required | Description                                           |
| -------------- | -------- | -------- | ----------------------------------------------------- |
| `name`         | `string` | ✅       | The name of your registry.                            |
| `homepage`     | `string` | ❌       | The URL of your registry's homepage. (optional)       |
| `authors`      | `array`  | ❌       | A list of authors. (optional)                         |
| `description`  | `string` | ❌       | A short description of your registry. (optional)      |
| `owner`        | `string` | ❌       | The owner of the registry. (optional)                 |
| `version`      | `string` | ✅       | The version of your registry.                         |
| `last_updated` | `string` | ✅       | The date and time when the registry was last updated. |

### Items

The `items` array contains all the components, blocks, pages, and UI elements available in your registry.

| Field              | Type     | Required | Description                                                         |
| ------------------ | -------- | -------- | ------------------------------------------------------------------- |
| `name`             | `string` | ✅       | The name of the item.                                               |
| `type`             | `enum`   | ✅       | The type of the item. Can be `component`, `block`, `page`, or `ui`. |
| `description`      | `string` | ❌       | A short description of the item. (optional)                         |
| `tags`             | `array`  | ❌       | A list of tags for searching and organizing. (optional)             |
| `nodeDependencies` | `object` | ❌       | A list of npm/node dependencies required by the item. (optional)    |
| `dependencies`     | `object` | ❌       | A list of Composer dependencies required by the item. (optional)    |
| `files`            | `array`  | ✅       | A list of files that make up the item.                              |
| `cssVars`          | `array`  | ❌       | A list of CSS variables used by the item. (optional)                |

### Example

Here is an example of a `bladecn-registry.json` file:

```json
{
  "$schema": "https://github.com/blade-cn/bladecn-laravel/schemas/bladecn-registry.schema.json",
  "name": "BladeCN UI",
  "homepage": "https://github.com/blade-cn",
  "authors": ["Username", "Second user"],
  "description": "Community-driven Blade & Livewire components",
  "owner": "blade-cn",
  "version": "1.0.0",
  "last_updated": "2025-09-05T20:45:00Z",
  "items": [
    {
      "name": "Hello World",
      "type": "component",
      "description": "A simple hello-world Blade component",
      "tags": ["starter", "example"],
      "nodeDependencies": {
        "alpinejs": "^3.14.0"
      },
      "dependencies": {
        "livewire/livewire": "^3.0"
      },
      "files": [
        { "path": "resources/views/components/hello-world.blade.php", "type": "component" },
        { "path": "resources/css/hello-world.css", "type": "style" }
      ],
      "cssVars": [
          "--color-custom": "VALUE",
          "--spacing-xl": "VALUE"
      ]
    }
  ]
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [G4b0rDev](https://github.com/G4b0rDev)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

