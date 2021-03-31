<?php
/**
 * User Controller
 *
 * @author Serhii Shkrabak
 * @global object $CORE
 * @package Controller\Main
 */
namespace Controller;
class Main
{
	use \Library\Shared;

	private $model;

	public function exec():?array {
		$result = null;
		$url = $this->getVar('REQUEST_URI', 'e');
		$path = explode('/', $url);

		if (isset($path[2]) && !strpos($path[1], '.')) { // Disallow directory changing
			$file = ROOT . 'model/config/methods/' . $path[1] . '.php';
			if (file_exists($file)) {
				include $file;
				if (isset($methods[$path[2]])) {
					$details = $methods[$path[2]];
					$request = [];
					foreach ($details['params'] as $param) {
						$var = $this->getVar($param['name'], $param['source']);	// Получение введеных данных в параметр
						
						if (isset($var)) {
							$patternFile = ROOT . 'model/config/patterns.php';
							include $patternFile;

							if (isset($param['pattern'])){		// Если есть Паттерн
								if (preg_match($patterns[$param['pattern']]['regex'], $var)){			// Проверка на соответствие с Паттерном
									if(isset($patterns[$param['pattern']]['callback'])){
										$var = preg_replace_callback($patterns[$param['pattern']]['replacement'], $patterns[$param['pattern']]['callback'], $var);
									}
										$request[$param['name']] = $var;			//Если всё отлично, добавить к параметрам запроса
								} else {
									$state = 2;			 // Установка кода ошибки, для обновления однотипных ошибок
									if (!isset($result)) {
										$result = [								// Создание ответа с кодом ошибки
											'state' => $state,
											'data' => [],
										];
									}
									if ($result['state'] === $state) { 				// Если однотипная ошибка, добавляем в список
										array_push($result['data'], $param['name']);
									}	
								}
							}
							else
								$request[$param['name']] = $var;				// Иначе просто записываем его
						}
						else if(!$param['required'])					// Если параметр не обязательный
								$request[$param['name']] = $param['default'] ?: ''; 				// Устанавливаем имя по умолчанию
						else{
									$state = 1;			// Код ошибки
									if (!isset($result)) {
										$result = [						// Создание ответа с кодом ошибки
											'state' => $state,
											'data' => [],
										];
									}
									// Добавление в список однотипных ошибок
						if($result['state'] === $state) 
							array_push($result['data'], $param['name']);
						}
						
					}
					if (isset($result['state'])) 			// Если записали код ошибки
						return $result; 				// Возвращаем информацию про ошибку на сервер
					if (method_exists($this->model, $path[1] . $path[2])) {     	// Если Метод реализован
						$method = [$this->model, $path[1] . $path[2]];
						$result = $method($request);			// Отправить запрос боту
					} else {					// Иначе - ошибка про поддержку метода
							$result = [
								'state' => 5
							];
					}

				}
				else { 		// Если метод не описан в модели - ошибка про поддержку метода
					$result = [
					'state' => 5
					];
				}

			}
		}

		return $result;
	}

	public function __construct() {
		// CORS configuration
		$origin = $this -> getVar('HTTP_ORIGIN', 'e');
		$front = $this -> getVar('FRONT', 'e');

		foreach ( [$front] as $allowed )
			if ( $origin == "https://$allowed") {
				header( "Access-Control-Allow-Origin: $origin" );
				header( 'Access-Control-Allow-Credentials: true' );
			}
		$this->model = new \Model\Main;
	}
}