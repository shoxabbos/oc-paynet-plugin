<?php namespace Shohabbos\Paynet\Controllers;

use SoapServer;
use Backend\Classes\Controller;
use Shohabbos\Paynet\ProviderWebService;


class Paynet extends Controller
{
    
    public function test() {
    	phpinfo();
    }

	public function index() {		
		// ***************************************************************************************
		ini_set("soap.wsdl_cashe_enabled","0");
		$server = new SoapServer("http://mangu.itmaker.uz/paynet-wsdl", [
			'soap_version' => SOAP_1_1, 
			'cache_wsdl' => WSDL_CACHE_NONE, 
			'encoding' => 'UTF-8'
		]);

		$server->setClass("ProviderWebService");
		$server->handle();
	}



	public function client() {
		// создаем объект для отправки на сервер
		$req = new Request();
		$req->username = 'paynet';
		$req->password = 'maxiDrom101';
		$req->amount = $_GET['cache'];
		$req->serviceId = 1;
		$req->transactionId = 132731;
		$req->transactionTime = time();
		$req->parameters['paramValue'] = 21;
		$req->parameters['paramKey'] = 1;

		$client = new SoapClient("http://gw.kuponi.uz/ProviderWebService.wsdl", array('soap_version' => SOAP_1_2));

		var_dump($client->PerformTransaction($req));
	}


	public function wsdl() {
        echo $this->renderFile('../components/paynet/ProviderWebService.wsdl.php');
    }

    public function xsd() {
        echo $this->renderFile('./../components/paynet/ProviderWebService.xsd.php');
    }

}
