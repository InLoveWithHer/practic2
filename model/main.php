<?php
/**
 * User Controller
 *
 * @author Serhii Shkrabak
 * @global object $CORE->model
 * @package Model\Main
 */
namespace Model;
class Main
{
	use \Library\Shared;

	public function formsubmitAmbassador(array $data):?array {
		
		
		//$KeysFile = ROOT . 'model/config/keys.php';    // Файл с установленым ключем
		//if(file_exists(KeysFile));
		//	  include $KeysFile;
		

		$result = null;
		$chat = ;     // Кому бот отсывает сообщение
		$key = ''; // Ключ API телеграм
		if(empty($key))     // Если ключа нет
			  throw new \Exception("Где Ваш ключ?");          // Сообщение про ошибку

		
		if (!empty($data['position']))          				// Если должность указана
		$text = "Нова заявка в *Цифрові Амбасадори*:\n" . $data['firstname'] . ' '. $data['secondname']. ', '. $data['position'] . "\n*Зв'язок*: " . $data['phone'];
		else 																// Если должность не указана
		$text = "Нова заявка в *Цифрові Амбасадори*:\n" . $data['firstname'] . ' '. $data['secondname']. ', ' . "\n*Зв'язок*: " . $data['phone'];
		$text = urlencode($text);
		$answer = file_get_contents("https://api.telegram.org/bot$key/sendMessage?parse_mode=markdown&chat_id=$chat&text=$text");
		$answer = json_decode($answer, true);
		$result = ['message' => $answer['result']];
		return $result;
	}

	public function __construct() {

	}
}