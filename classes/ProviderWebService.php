<?php namespace Shohabbos\Paynet\Classes;

use Event;
use Shohabbos\Paynet\Models\Transaction;
use Shohabbos\Paynet\Classes\Types\PerformTransactionResult;
use Shohabbos\Paynet\Classes\Types\CancelTransactionResult;

class ProviderWebService {

	public $messages = [
		0   => "Проведено успешно",
		77  => "Недостаточно средств на счету клиента для отмены платежа",
		100 => "Услуга временно не поддерживается",
		101 => "Квота исчерпана",
		102 => "Системная ошибка",
		103 => "Неизвестная ошибка",
		201 => "Транзакция существует",
		202 => "Транзакция уже отменена",
		303 => "Пользователь не найден",
		411 => "Не задан один или несколько обязательных параметров",
		412 => "Неверный логин",
		413 => "Неверная сумма",
		502 => "Клиент вне зоны обслуживания провайдера",
		601 => "Доступ запрещен"
	];

	public $username;

	public $password;

	public $serviceId = 1;

	public $allowedIps = []; //["213.230.106.115"];


	public function __construct() {
		$this->username = "paynet";
		$this->password = "maxiDrom101";
	}
	

	/**
	 * Service Call: PerformTransaction
	 * Parameter options:
	 * @param mixed arguments PerformTransactionArguments
	 * @return PerformTransactionResult
	 */
	public function PerformTransaction($arguments) {
		$parameters = [];
		$username = $arguments->username;
		$password = $arguments->password;
		$amount = $arguments->amount/100;
		$parameters = $arguments->parameters;
		$serviceId = $arguments->serviceId;
		$transactionId = $arguments->transactionId;
		$transactionTime = $arguments->transactionTime;
		$userId = is_array($parameters) ? $parameters[0]->paramValue : $parameters->paramValue;

		$isdn = 0; // номер нашей транзакции
		$status = 103; // стутус траназакции
		$balance = 0; // баланс плательщика
		$this->exit = 5;

		// теперь получим ип адрес запроса
		$ip = getenv('REMOTE_ADDR');
		if(in_array($ip, $this->allowedIps) || empty($this->allowedIps)) {
			$ipflag = 5;
		}


		// ип адрес из списка запрещенных
		if($ipflag !=5 ) {
			$status = 601;
			$this->exit = 2;
		} else {
			// *******************************************
			// теперь проверим логин на правильность
			if($username != $this->username) {
				$status = 412;
				$this->exit = 2;
			}

			// теперь проверим пароль на правильность
			if($password != $this->password) {
				$status = 601;
				$this->exit = 2;
			}

			// теперь проверим провайдера
			if($serviceId != 1) {
				$status = 502;
				$this->exit = 2;
			}

			// теперь проверим есть ли все остальные параметры
			if(empty($amount) or empty($userId) or empty($transactionId) or empty($transactionTime)) {
				$status = 411;
				$this->exit = 2;
			}
			
			// here is coming business logic
			if ($this->exit!=2) {
				$transaction = Transaction::where('trans_id', $transactionId)->first();
				if ($transaction) {
					$status = 201;
					$this->exit = 2;
				}

				if ($this->exit != 2) {
					$user = null;
			        Event::fire('shohabbos.paynet.existsAccount', [$userId, &$user]);

					if (!$user) {
						$status = 303;
						$this->exit = 2;
					}
				}
			}

			if ($this->exit != 2) {
				$transaction = Transaction::create([
					'trans_id' => $transactionId,
					'owner_id' => $userId,
					'amount'   => $amount,
					'status'   => 1,
				]);

				$parameters = [];
				Event::fire('shohabbos.paynet.performTransaction', [$transaction, &$parameters]);

				foreach ($parameters as $key => $value) {
					$parameter = new GenericParam();
					$parameter->paramKey = $key;
					$parameter->paramValue = $value;
					$parameters[] = $parameter;
				}
				
				$status = 0;
			}
		}

		// тут мы выдаем ответ на запрос SOAP
		$result = new PerformTransactionResult();
		$result->providerTrnId = isset($transaction) ? $transaction->id : 0;
		$result->errorMsg = $this->messages[$status];
		$result->status = $status;
		$result->timeStamp = date("c");
		$result->parameters = $parameters;
		
		return $result;
	}


	/**
	 * Service Call: CheckTransaction
	 * Parameter options:
	 * @param mixed arguments CheckTransactionArguments
	 * @return CheckTransactionResult
	 */
	public function CheckTransaction($arguments) {
		// тут идет получение данных из SOAP
		$password = $arguments->password;
		$username = $arguments->username;
		$serviceId = $arguments->serviceId;
		$transactionId = $arguments->transactionId;
		
		// Установим предпарамметры
		$isdn = 0; // cвойства транзакции
		$status = 100; // стутус траназакции
		$this->exit = 5;
		$ip=getenv('REMOTE_ADDR');
		if(in_array($ip, $this->allowedIps) || empty($this->allowedIps)){
			$ipflag = 5;
		}

		// ип адрес из списка запрещенных
		if($ipflag != 5) {
			$status = 601;
			$this->exit = 2;
		} else {
			// *******************************************
			// теперь проверим логин на правильность
			if($username != $this->username) {
				$status = 412;
				$this->exit = 5;
			}

			// теперь проверим пароль на правильность
			if($password != $this->password) {
				$status = 601;
				$this->exit = 2;
			}

			// теперь проверим провайдера
			if($serviceId!=1) {
				$st=502;
				$this->exit = 2;
			}

			// теперь проверим есть ли все остальные параметры
			if(!$transactionId){
				$status = 411;
				$this->exit = 2;
			}
			
			// here will coming business logic
			if ($this->exit != 2) {
				$transaction = Transaction::where('trans_id', $transactionId)->orderBy('id')->first();

				
				if (!$transaction) {
					$status = 201;
					$this->exit = 2;
					$isdn = 0;
				} else {
					$status = 0;
					$isdn = $transaction->id;
					
					$transactionState = 1;
					$transactionStateErrorStatus = 0;
					if ($transaction->status < 1) {
						$transactionState = 2;
						$transactionStateErrorStatus = 1;
					}
				}
			}
		}

		// тут мы выдаем ответ на запрос SOAP
		$result = new CheckTransactionResult();
		$result->errorMsg = $this->messages[$status];
		$result->status = $status;
		$result->timeStamp = date('c');
		$result->providerTrnId = $isdn;
		$result->transactionState = $transactionState;
		$result->transactionStateErrorStatus = $transactionStateErrorStatus;
		$result->transactionStateErrorMsg = "Success";

		return $result;
	}

	/**
	 * Service Call: CancelTransaction
	 * Parameter options:
	 * @param mixed arguments CancelTransactionArguments
	 * @return CancelTransactionResult
	 */
	public function CancelTransaction($arguments) {
		// тут идет получение данных из SOAP
		$password = $arguments->password;
		$username = $arguments->username;
		$serviceId = $arguments->serviceId;
		$transactionId = $arguments->transactionId;

		// Установим предпарамметры
		$isdn = 0; // cвойства транзакции
		$status = 202; // стутус траназакции
		$this->exit = 5;
		$ip=getenv('REMOTE_ADDR');

		// теперь проверим на ликвидность
		if(in_array($ip, $this->allowedIps) || empty($this->allowedIps)){
			$ipflag = 5;
		}

		$transaction = Transaction::where('trans_id', $transactionId)->orderBy('id')->first();
		if (!$transaction) {
			$status = 201;
			$this->exit = 2;
		}
		
		if ($transaction && $transaction->status == '0') {
			$status = 202;
			$this->exit = 2;
		} else {
			$status = 77;
			Event::fire('shohabbos.paynet.cancelTransaction', [$transaction, &$status]);

			if ($status == 0) {
				$transaction->status = 0;
				$transaction->save();
			}
		}
		
		// тут мы выдаем ответ на запрос SOAP
		$result = new CancelTransactionResult();
		$result->errorMsg = $this->messages[$status];
		$result->status = $status;
		$result->timeStamp = date("c");
		$result->transactionState = 2;
		
		return $result;
	}

	/**
	 * Service Call: GetStatement
	 * Parameter options:
	 * @param mixed arguments GetStatementArguments
	 * @return GetStatementResult
	 */
	public function GetStatement($arguments) {
		// тут идет получение данных из SOAP
		$dateFrom = $arguments->dateFrom;
		$dateTo = $arguments->dateTo;
		$password = $arguments->password;
		$serviceId = $arguments->serviceId;
		$username = $arguments->username;

		// Установим предпарамметры
		$status = 103; // стутус траназакции
		$this->exit = 5;
		// теперь получим ип адрес запроса
		$ip=getenv('REMOTE_ADDR');
		// теперь проверим на ликвидность
		if(in_array($ip, $this->allowedIps) || empty($this->allowedIps)){
			$ipflag = 5;
		}

		// ип адрес из списка запрещенных
		if($ipflag != 5) {
			$status = 601;
			$this->exit = 2;
		} else {
			// *******************************************
			// теперь проверим логин на правильность
			if($username != $this->username) {
				$status = 412;
				$this->exit = 2;
			}

			// теперь проверим пароль на правильность
			if($password != $this->password) {
				$status = 601;
				$this->exit = 2;
			}

			// теперь проверим провайдера
			if($serviceId != 1) {
				$status = 502;
				$this->exit = 2;
			}

			// теперь проверим есть ли все остальные параметры
			if(empty($dateFrom) or empty($dateTo)){
				$status = 411;
				$this->exit = 2;
			}
			
			$statements = [];
			// here will coming business logic
			if ($this->exit != 2) {
				$status = 0;
				$transactions = Transaction::whereBetween('created_at', [$dateFrom, $dateTo])->where('status', 1)->get();
				
				foreach ($transactions as $key => $value) {
					$statement = new TransactionStatement();
					$statement->amount = $value->amount * 100;
					$statement->providerTrnId = $value->id;
					$statement->transactionId = $value->trans_id;
					$statement->transactionTime = date('c', $transaction->created_at);
					$statements[] = $statement;
				}
			}
		}

		// тут мы выдаем ответ на запрос SOAP
		$result = new GetStatementResult();
		$result->errorMsg = $this->messages[$status];
		$result->status = $status;
		$result->timeStamp = date("c");
		$result->statements = $statements;
		
		return $result;
	}

	/**
	 * Service Call: GetInformation
	 * Parameter options:
	 * @param mixed arguments GetInformationArguments
	 * @return GetInformationResult
	 */
	public function GetInformation($arguments) {
		// тут идет получение данных из SOAP
		$username = $arguments->username;
		$password = $arguments->password;
		$parameters = $arguments->parameters;
		$serviceId = $arguments->serviceId;
		$userId = isset($parameters[0]->paramValue) ? $parameters[0]->paramValue : $parameters->paramValue;

		// Установим предпарамметры
		$status = 103; // стутус траназакции
		$balance = 0; // баланс плательщика
		$this->exit = 5;
		// теперь получим ип адрес запроса
		$ip=getenv('REMOTE_ADDR');
		// теперь проверим на ликвидность
		if(in_array($ip, $this->allowedIps) || empty($this->allowedIps)){
			$ipflag=5;
		}

		// ип адрес из списка запрещенных
		if($ipflag != 5) {
			$status = 601;
			$this->exit = 2;
		} else {

			// *******************************************
			// теперь проверим логин на правильность
			if($username != $this->username) {
				$status = 412;
				$this->exit = 2;
			}

			// теперь проверим пароль на правильность
			if($password != $this->password) {
				$status = 601;
				$this->exit = 2;
			}

			// теперь проверим провайдера
			if($serviceId != 1) {
				$status = 502;
				$this->exit = 2;
			}

			// теперь проверим есть ли все остальные параметры
			if(empty($userId)){
				$status = 411;
				$this->exit = 2;
			}
			
			$user = null;
			Event::fire('shohabbos.paynet.existsAccount', [$userId, &$user]);

			if (!$user) {
				$status = 303;
				$this->exit = 2;
			}
			
			// here will coming business logic
			if ($this->exit != 2) {
				$status = 0;
				$this->exit = 5;

				$parameters = [];
				Event::fire('shohabbos.paynet.getInformation', [$userId, &$parameters]);

				foreach ($parameters as $key => $value) {
					$parameter = new GenericParam();
					$parameter->paramKey = $key;
					$parameter->paramValue = $value;
					$parameters[] = $parameter;
				}
			}
		}

		// тут мы выдаем ответ на запрос SOAP
		$result = new GetInformationResult();
		$result->errorMsg = $this->messages[$status];
		$result->status = $status;
		$result->timeStamp = date("c");
		$result->parameters = $parameters;
		
		return $result;
	}






}
