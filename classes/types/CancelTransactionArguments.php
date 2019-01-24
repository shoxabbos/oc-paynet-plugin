<?php namespace Shohabbos\Paynet\Classes\Types;

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