<?php namespace Shohabbos\Paynet;

/**
* GenericArguments
*/
class GenericArguments {
	/**
	 * @access public
	 * @var string
	 */
	public $password;
	/**
	 * @access public
	 * @var string
	 */
	public $username;
}
/**
 * GenericResult
 */
class GenericResult {
	/**
	 * @access public
	 * @var string
	 */
	public $errorMsg;
	/**
	 * @access public
	 * @var integer
	 */
	public $status;
	/**
	 * @access public
	 * @var dateTime
	 */
	public $timeStamp;
}
/**
 * GenericParam
 */
class GenericParam {
	/**
	 * @access public
	 * @var string
	 */
	public $paramKey;
	/**
	 * @access public
	 * @var string
	 */
	public $paramValue;
}
/**
 * CancelTransactionArguments
 */
class CancelTransactionArguments extends GenericArguments {
	/**
	 * @access public
	 * @var integer
	 */
	public $serviceId;
	/**
	 * @access public
	 * @var integer
	 */
	public $transactionId;
}
/**
 * CancelTransactionResult
 */
class CancelTransactionResult extends GenericResult {
	/**
	 * @access public
	 * @var integer
	 */
	public $transactionState;
}
/**
 * CheckTransactionArguments
 */
class CheckTransactionArguments extends GenericArguments {
	/**
	 * @access public
	 * @var integer
	 */
	public $serviceId;
	/**
	 * @access public
	 * @var integer
	 */
	public $transactionId;
}
/**
 * CheckTransactionResult
 */
class CheckTransactionResult extends GenericResult {
	/**
	 * @access public
	 * @var integer
	 */
	public $providerTrnId;
	/**
	 * @access public
	 * @var integer
	 */
	public $transactionState;
	public $transactionStateErrorStatus;
	public $transactionStateErrorMsg;
}
/**
 * GetInformationArguments
 */
class GetInformationArguments extends GenericArguments {
	/**
	 * @access public
	 * @var GenericParam[]
	 */
	public $parameters;
	/**
	 * @access public
	 * @var integer
	 */
	public $serviceId;
}
/**
 * GetInformationResult
 */
class GetInformationResult extends GenericResult {
	/**
	 * @access public
	 * @var GenericParam[]
	 */
	public $parameters;
}
/**
 * GetStatementArguments
 */
class GetStatementArguments extends GenericArguments {
	/**
	 * @access public
	 * @var dateTime
	 */
	public $dateFrom;
	/**
	 * @access public
	 * @var dateTime
	 */
	public $dateTo;
	/**
	 * @access public
	 * @var integer
	 */
	public $serviceId;
}
/**
 * TransactionStatement
 */
class TransactionStatement {
	/**
	 * @access public
	 * @var integer
	 */
	public $amount;
	/**
	 * @access public
	 * @var integer
	 */
	public $providerTrnId;
	/**
	 * @access public
	 * @var integer
	 */
	public $transactionId;
	/**
	 * @access public
	 * @var dateTime
	 */
	public $transactionTime;
}
/**
 * GetStatementResult
 */
class GetStatementResult extends GenericResult {
	/**
	 * @access public
	 * @var TransactionStatement[]
	 */
	public $statements;
}
/**
 * PerformTransactionArguments
 */
class PerformTransactionArguments extends GenericArguments {
	/**
	 * @access public
	 * @var integer
	 */
	public $amount;
	/**
	 * @access public
	 * @var GenericParam[]
	 */
	public $serviceId;
	/**
	 * @access public
	 * @var integer
	 */
	public $transactionId;
	/**
	 * @access public
	 * @var dateTime
	 */
	public $transactionTime;
}
/**
 * PerformTransactionResult
 */
class PerformTransactionResult extends GenericResult {
	/**
	 * @access public
	 * @var GenericParam[]
	 */
	public $parameters;
	/**
	 * @access public
	 * @var integer
	 */
	public $providerTrnId;
}
