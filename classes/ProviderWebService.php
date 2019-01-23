<?php namespace Shohabbos\Paynet;

class ProviderWebService {
	var $exit=5;
	var $msg = array(
			0=>"Ok",
			100=>"Услуга временно не поддерживается",
			102=>"Системная ошибка",
			103=>"Неизвестная ошибка",
			201=>"Транзакция существует",
			202=>"Транзакция уже отменена",
			303=>"Пользователь не найден",
			411=>"Не задан один или несколько обязательных параметров",
			412=>"Пользователь не найден",
			413=>"Неверная сумма",
			502=>"Клиент вне зоны обслуживания провайдера",
			601=>"Доступ запрещен"
			);
	var $username;
	var $password;
	var $allowedIps = array();//array("213.230.106.115");
	//var $debugEmail = "shvabauer@arsenal-d.uz";
	var $config;

	public function __construct() {
		global $config;
		$this->config = $config;
		$this->class_name = $class_name;
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
		$parameters = array();
		// тут идет получение данных из SOAP
		$username = $arguments->username;
		$password = $arguments->password;
		$amount = $arguments->amount/100;
		$parameters = $arguments->parameters;
		$serviceId = $arguments->serviceId;
		$transactionId = $arguments->transactionId;
		$transactionTime = $arguments->transactionTime;
		if (count($parameters)>1) {
			$userId = $parameters[0]->paramValue;
		} else
			$userId= $parameters->paramValue;
		$log5=print_r($GLOBALS, true);
		$log5='<pre>'.$log5.'</pre>';
		// Установим предпарамметры
		$isdn=0; // номер нашей транзакции
		$st=103; // стутус траназакции
		$balance=0; // баланс плательщика
		$this->exit=5;
		// теперь получим ип адрес запроса
		$ip=getenv('REMOTE_ADDR');
		// теперь проверим на ликвидность
		if(in_array($ip, $this->allowedIps) || empty($this->allowedIps)){
			$ipflag=5;
		};
		// ип адрес из списка запрещенных
		if($ipflag!=5) {
			$st=601;
			$this->exit=2;
		} else {
			// *******************************************
			// теперь проверим логин на правильность
			if($username!=$this->username) {
				$st=412;
				$this->exit=2;
			};
			// теперь проверим пароль на правильность
			if($password!=$this->password) {
				$st=601;
				$this->exit=2;
			};
			// теперь проверим провайдера
			if($serviceId!=1) {
				$st=502;
				$this->exit=2;
			};
			// теперь проверим есть ли все остальные параметры
			if($amount=="" or $userId=="" or $transactionId=="" or $transactionTime==""){
				$st=411;$this->exit=2;
			};
			// here is coming business logic
			if ($this->exit!=2) {
				$res = mysql_query($q="select * from paynet where transid = {$transactionId} ");
				if (mysql_num_rows($res)) {
					$st=201;$this->exit=2;
				}
				if ($this->exit!=2) {
					$res = mysql_query($q="select * from 19941001_users where id = {$userId} ");
					if (!mysql_num_rows($res)) {
						$st=303;$this->exit=2;
					} else {
						$user = mysql_fetch_assoc($res);
					}
				}
			}
			if ($this->exit!=2) {
				mysql_query("INSERT INTO `paynet` (transid, userid, amount, status) VALUES ('{$transactionId}', '{$userId}', '{$amount}', 1)");
				$orderId = mysql_insert_id();
				mysql_query("update 19941001_users set _cache = _cache + ($amount/1000) where id = {$userId}");
				
				$parameters = array();
				$parameter = new GenericParam();
				$parameter->paramKey = "bonus";
				$parameter->paramValue = $user["_cache"] + $amount/1000;
				$parameters[] = $parameter;
				
				$st = 0;
			}
		}
		// тут мы выдаем ответ на запрос SOAP
		$result = new PerformTransactionResult();
		
		$result->providerTrnId = $orderId;
		$result->errorMsg = $this->msg[$st];
		$result->status = $st;
		$result->timeStamp = date("c");
		$result->parameters = $parameters;
		// тут отправка почты логов
		$log1=print_r($arguments, true);
		$log2=print_r($result, true);
		$log.='<pre>'.$log1.'<br><br>'.$log2.'</pre>';

		//smtpSend($this->debugEmail, "PAYNET PAY", $log);
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
		$isdn=0; // cвойства транзакции
		$st=100; // стутус траназакции
		$this->exit=5;
		// теперь получим ип адрес запроса
		$ip=getenv('REMOTE_ADDR');
		// теперь проверим на ликвидность
		if(in_array($ip, $this->allowedIps) || empty($this->allowedIps)){
			$ipflag=5;
		}
		// ип адрес из списка запрещенных
		if($ipflag!=5) {
			$st=601;
			$this->exit=2;
		} else {
			// *******************************************
			// теперь проверим логин на правильность
			if($username!=$this->username) {
				$st=412;
				$this->exit=5;
			};
			// теперь проверим пароль на правильность
			if($password!=$this->password) {
				$st=601;
				$this->exit=2;
			};
			// теперь проверим провайдера
			if($serviceId!=1) {
				$st=502;
				$this->exit=2;
			};
			// теперь проверим есть ли все остальные параметры
			if(!$transactionId){
				$st=411;$this->exit=2;
			}
			
			// here will coming business logic
			if ($this->exit!=2) {
				$res = mysql_query("select *, unix_timestamp(dateCreated) as ut  from paynet where transid = '{$transactionId}' order by id");
				if (!mysql_num_rows($res)) {
					$st=201;$this->exit=2;
					$isdn = 0;
				} else {
					$row = mysql_fetch_assoc($res);
					$st = 0;
					$isdn = $row['id'];
					
					$transactionState = 1;
					$transactionStateErrorStatus = 0;
					if ($row['status']<1) {
						$transactionState = 2;
						$transactionStateErrorStatus = 1;
					}
				}
			}
		}
		// тут мы выдаем ответ на запрос SOAP
		$result = new CheckTransactionResult();
		$result->errorMsg = $this->msg[$st];
		$result->status = $st;
		$result->timeStamp = date('c');
		$result->providerTrnId = $isdn;
		$result->transactionState = $transactionState;
		$result->transactionStateErrorStatus = $transactionStateErrorStatus;
		$result->transactionStateErrorMsg = "Success";
		// тут отправка почты логов
		$log1=print_r($arguments, true);
		$log2=print_r($result, true);
		$dataz=$transactionId.'-'.$rss;
		$log='<pre>'.$log1.'<br><br>'.$dataz.'<br><br>'.$log2.'</pre>';
		//smtpSend($this->debugEmail, "PAYNET CHECK", $log);
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
		$isdn=0; // cвойства транзакции
		$st=202; // стутус траназакции
		$this->exit=5;
		// теперь получим ип адрес запроса
		$ip=getenv('REMOTE_ADDR');
		// теперь проверим на ликвидность
		if(in_array($ip, $this->allowedIps) || empty($this->allowedIps)){
			$ipflag=5;
		};

		$res = mysql_query("select * from paynet where transid = '{$transactionId}' order by id");
		if (!mysql_num_rows($res)) {
			$st=201;
			$this->exit=2;
		} else {
			$row = mysql_fetch_assoc($res);
		}
		
		if ($row['status']=='0') {
			$st=202;
			$this->exit=2;
		} else {
			$st = 0;
			mysql_query("update paynet set status = 0 where transid = '{$transactionId}' ");
			mysql_query("update 19941001_users set _cache = _cache - ({$row['amount']}/1000) where id = {$row['userid']}");
		}
		
//		$st=601;
		$this->exit=2;
		// тут мы выдаем ответ на запрос SOAP
		$result = new CancelTransactionResult();
		$result->errorMsg = $this->msg[$st];
		$result->status = $st;
		$result->timeStamp = date("c");
		$result->transactionState = 2;
		// тут отправка почты логов
		$log1=print_r($arguments, true);
		$log2=print_r($result, true);
		$dataz=$transactionId.'-'.$rss;
		$log='<pre>'.$log1.'<br><br>'.$dataz.'<br><br>'.$log2.'</pre>';
		//smtpSend($this->debugEmail, "PAYNET CANCEL", $log);
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
		$st=103; // стутус траназакции
		$this->exit=5;
		// теперь получим ип адрес запроса
		$ip=getenv('REMOTE_ADDR');
		// теперь проверим на ликвидность
		if(in_array($ip, $this->allowedIps) || empty($this->allowedIps)){
			$ipflag=5;
		};
		// ип адрес из списка запрещенных
		if($ipflag!=5) {
			$st=601;
			$this->exit=2;
		} else {
			// *******************************************
			// теперь проверим логин на правильность
			if($username!=$this->username) {
				$st=412;
				$this->exit=2;
			};
			// теперь проверим пароль на правильность
			if($password!=$this->password) {
				$st=601;
				$this->exit=2;
			};
			// теперь проверим провайдера
			if($serviceId!=1) {
				$st=502;
				$this->exit=2;
			};
			// теперь проверим есть ли все остальные параметры
			if($dateFrom=="" or $dateTo==""){
				$st=411;$this->exit=2;
			};
			
			$statements = array();
			// here will coming business logic
			if ($this->exit!=2) {
				$st = 0;
				$res = mysql_query($q="select *, unix_timestamp(dateCreated) as ut from paynet where (dateCreated>='$dateFrom' and dateCreated<='$dateTo' ) and status = 1 ");
				while ($row = mysql_fetch_assoc($res)) {
					$statement = new TransactionStatement();
					$statement->amount = $row['amount']*100;
					$statement->providerTrnId = $row['id'];
					$statement->transactionId = $row['transid'];
					$statement->transactionTime = date('c',$row['ut']);
					$statements[] = $statement;
				}
			}
		}
		// тут мы выдаем ответ на запрос SOAP
		$result = new GetStatementResult();
		$result->errorMsg = $this->msg[$st];
		$result->status = $st;
		$result->timeStamp = date("c");
		$result->statements = $statements;
		// тут отправка почты логов
		$log1=print_r($arguments, true);
		$log2=print_r($result, true);
		$log3=var_export($statements, true);
		$log4=var_export($result, true);
		$dataz=$transactionId.'-'.$rss;
		$log='<pre>1 '.$log1.'<br><br>2 '.$dataz.'<br><br>3 '.$log2.'<br><br>4 '.$log3.'<br><br>5 '.$log4.'</pre>';
		//smtpSend($this->debugEmail, "PAYNET GetStatement", $log);
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
		
		if (count($parameters)>1) {
			$userId = $parameters[0]->paramValue;
		} else
			$userId= $parameters->paramValue;

		$name= $parameters->paramKey;
		// Установим предпарамметры
		$st=103; // стутус траназакции
		$balance=0; // баланс плательщика
		$this->exit=5;
		// теперь получим ип адрес запроса
		$ip=getenv('REMOTE_ADDR');
		// теперь проверим на ликвидность
		if(in_array($ip, $this->allowedIps) || empty($this->allowedIps)){
			$ipflag=5;
		};
		// ип адрес из списка запрещенных
		if($ipflag!=5) {
			$st=601;
			$this->exit=2;
		} else {
			// *******************************************
			// теперь проверим логин на правильность
			if($username!=$this->username) {
				$st=412;
				$this->exit=2;
			};
			// теперь проверим пароль на правильность
			if($password!=$this->password) {
				$st=601;
				$this->exit=2;
			};
			// теперь проверим провайдера
			if($serviceId!=1) {
				$st=502;
				$this->exit=2;
			};
			// теперь проверим есть ли все остальные параметры
			if($userId==""){
				$st=411;$this->exit=2;
			}
			
			$res = mysql_query($q="select * from 19941001_users where id = {$userId} ");
			if (!mysql_num_rows($res)) {
				$st=303;$this->exit=2;
			} else {
				$user = mysql_fetch_assoc($res);
			}
			
			// here will coming business logic
			if ($this->exit!=2) {
				$st = 0;
				$this->exit=5;

				$parameters = array();
				$parameter = new GenericParam();
				$parameter->paramKey = "userid";
				$parameter->paramValue = $userId;
				$parameters[] = $parameter;
				$parameter = new GenericParam();
				$parameter->paramKey = "bonus";
				$parameter->paramValue = $user['_cache'];
				$parameters[] = $parameter;
			}
		}
		// тут мы выдаем ответ на запрос SOAP
		$result = new GetInformationResult();
		$result->errorMsg = $this->msg[$st];
		$result->status = $st;
		$result->timeStamp = date("c");
		$result->parameters = $parameters;
		
		$log1=print_r($arguments, true);
		$log2=print_r($result, true);
		$dataz=$transactionId.'-'.$rss;
		$log='<pre>'.$log1.'<br><br>'.$dataz.'<br><br>'.$log2.'</pre>';
		//smtpSend($this->debugEmail, "PAYNET GetInformation", $log);
		return $result;
	}
}
