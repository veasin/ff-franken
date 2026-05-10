<?php
// frankenphp.exe run --config demo/Caddyfile
include __DIR__ . "/../vendor/autoload.php";

use function nx\{container, franken, from, log, output, route};

container('count', 0);
franken(function(){
	log(from('uri', 'input'));
	route([
		'get:/favicon.ico' => fn() => output(null, 404),
		'get:/' => fn() => output('hello world!', 'http'),
		'get:/hi' => fn() => output('hi~', 'http'),
		'get:/count' => function(){
			$count = container('count') + 1;
			output("count: $count", 'http');
			container('count', $count);
		},
	]);
}, function(){
	//container(null);
});
