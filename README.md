# nx-tiny-franken

[FrankenPHP](https://frankenphp.dev) worker mode adapter for [nx-tiny](https://github.com/veasin/nx-tiny) micro-framework.

## Install

```bash
composer require veasin/nx-tiny-franken
```

## Usage

```php
<?php
// franken.php - FrankenPHP worker entry point
include __DIR__ . '/vendor/autoload.php';

use function nx\{container, franken, from, log, output, route};

container('count', 0);

franken(function () {
    log(from('uri', 'input'));

    route([
        'get:/'         => fn() => output('hello world!', 'http'),
        'get:/hi'       => fn() => output('hi~', 'http'),
        'get:/count'    => function () {
            $count = container('count') + 1;
            output("count: $count", 'http');
            container('count', $count);
        },
    ]);
}, function () {
    // cleanup on each request cycle end
});
```

### Caddyfile

```caddy
localhost {
    log
    root demo/
    php_server {
        try_files {path} franken.php
        worker {
            file ./demo/franken.php
            watch ./src/**/*.php
            watch ./demo/**/*.php
            num 4
        }
    }
}
```

Run with:

```bash
frankenphp run --config demo/Caddyfile
```

## API

### `franken(callable $worker, ?callable $clear): void`

Wraps the FrankenPHP worker loop:

- Sets up a PSR-3 compatible logger via `frankenphp_log()` on first call.
- Calls `$worker()` on each request. If `$worker()` returns a truthy value, subsequent output/cleanup is skipped.
- Calls `$clear()` after each request cycle ends (if callable).

## License

LGPL-3.0-or-later
