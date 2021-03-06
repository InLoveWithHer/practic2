<?php
/**
 * Core bootloader
 *
 * @author Serhii Shkrabak
 */

/* RESULT STORAGE */
$RESULT = [
	'state' => 0,
	'data' => [],
	'debug' => []
];

define('ERRORS', [                           //Сообщения об ошибках
	1 => [
		'error' => 'REQUEST_INCOMPLETE',
		'message' => 'Неповний запит'
	],
	2 => [
		'error' => 'REQUEST_INCORRECT',
		'message' => 'Некоректний запит'
	],
	3 => [
		'error' => 'ACCESS_DENIED',
		'message' => 'Доступ заборонено'
	],
	4 => [
		'error' => 'RESOURCE_LOST',
		'message' => 'Ресурс не знайдено'
	],
	5 => [
		'error' => 'REQUEST_UNKNOWN',
		'message' => 'Метод не підтримується'
	],
	6 => [
		'error' => 'INTERNAL_ERROR',
		'message' => 'Внутрішня помилка'
	]
]);


/* ENVIRONMENT SETUP */
define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/'); // Unity entrypoint;

register_shutdown_function('shutdown', 'OK'); // Unity shutdown function

spl_autoload_register('load'); // Class autoloader

set_exception_handler('handler'); // Handle all errors in one function

/* HANDLERS */

/*
 * Class autoloader
 */
function load (String $class):void {
	$class = strtolower(str_replace('\\', '/', $class));
	$file = "$class.php";
	if (file_exists($file))
		include $file;
}

/*
 * Error logger
 */
function handler (Throwable $e):void {
	global $RESULT;
	$codes = ['RESOURCE_LOST' => 4, 'INTERNAL_ERROR' => 6];
	$message = $e -> getMessage();
	$RESULT['state'] = (isset($codes[$message])) ? $codes[$message] : 6;
	$RESULT[ 'debug' ][] = [
		'type' => get_class($e),
		'details' => $message,
		'file' => $e -> getFile(),
		'line' => $e -> getLine(),
		'trace' => $e -> getTrace()
	];
}

/*
 * Shutdown handler
 */
function shutdown():void {
	global $RESULT;
	$error = error_get_last();
	if ( ! $error ) {
		header("Content-Type: application/json");
		echo json_encode($GLOBALS['RESULT'], JSON_UNESCAPED_UNICODE);
	}
}

$CORE = new Controller\Main;
$data = $CORE->exec();

if(isset($data['state'])) {               // Если указан код ошибки
	$RESULT = $data;								// Записать полученые данные в результат
	$RESULT['error'] = ERRORS[$data['state']]['error'];			// Обозначение ошибки
	$RESULT['message'] = ERRORS[$data['state']]['message'];		// Сообщение
}
else if ($data !== null)				// Нет ошибки и есть ответ
			$RESULT['data'] = $data; 				// Вывод результата
else { 										// Есть ошибка 
	$RESULT['state'] = 6;          // Код ошибки
	$RESULT['errors'] = ERROR[$RESULT['state']]['error'];      	// Обозначение ошибки
	$RESULT['message'] = ERROR[$RESULT['state']]['message'];		// Сообщение
	unset($RESULT['data']);
}