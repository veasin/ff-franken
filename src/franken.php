<?php
namespace nx;
/**
 * FrankenPHP worker模式调用。设置log，worker执行后进行输出并清理。
 * $worker()返回truly时屏蔽后续处理
 * @param callable      $worker
 * @param callable|null $clear
 * @return void
 */
function franken(callable $worker, callable $clear = null): void{
	if(!container('^#log')){
		container('^#log', fn($level, $message, $context) => frankenphp_log($message, [
			'emergency' => 8,
			'alert' => 8,
			'critical' => 8,
			'error' => 8,//FRANKENPHP_LOG_LEVEL_ERROR
			'warning' => 4,//FRANKENPHP_LOG_LEVEL_WARN
			'notice' => 0,
			'info' => 0,//FRANKENPHP_LOG_LEVEL_INFO
			'debug' => -4,//FRANKENPHP_LOG_LEVEL_DEBUG
		][$level] ?? 0, $context));
	}
	while(frankenphp_handle_request(function() use ($worker){
		if(!$worker()){
			output();
			container('#in', null);
			container('#out', null);
		}
	})){
		if(is_callable($clear)) $clear();
	}
}