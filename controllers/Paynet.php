<?php namespace Shohabbos\Paynet\Controllers;

use StdClass;
use SoapServer;
use SoapClient;
use Backend\Classes\Controller;
use Shohabbos\Paynet\Classes\ProviderWebService;


class Paynet extends Controller
{
    
    public function test() {
    	$req = new StdClass();
		$req->username = 'paynet';
		$req->password = 'maxiDrom101';
		$req->amount = 10000;
		$req->serviceId = 1;
		$req->transactionId = 132731;
		$req->transactionTime = time();

		$param = new StdClass();
		$param->paramValue = 21;
		$param->paramKey = 'id';
		$req->parameters[] = $param;

    	$service = new ProviderWebService();

    	dump($service->PerformTransaction($req));
    }

	public function index() {		
		ini_set("soap.wsdl_cashe_enabled","0");
		$server = new SoapServer("http://mangu.itmaker.uz/paynet-wsdl", [
			'soap_version' => SOAP_1_1, 
			'cache_wsdl' => WSDL_CACHE_NONE, 
			'encoding' => 'UTF-8'
		]);

		$server->setObject(new ProviderWebService());
		$server->handle();
	}



	public function client() {
		// создаем объект для отправки на сервер
		$req = new StdClass();
		$req->username = 'paynet';
		$req->password = 'maxiDrom101';
		$req->amount = 10000;
		$req->serviceId = 1;
		$req->transactionId = 132731;
		$req->transactionTime = time();
		$req->parameters['paramValue'] = 21;
		$req->parameters['paramKey'] = 1;

		$client = new SoapClient("http://mangu.itmaker.uz/paynet-wsdl", [
			'soap_version' => SOAP_1_2
		]);

		$client->PerformTransaction($req);
	}



	public function wsdl() {
		$file = \File::get(__DIR__."/../schemes/ProviderWebService.wsdl");

		return response($file)
	        ->withHeaders([
	            'Content-Type' => 'text/xml',
	            'Charset' => 'utf-8'
	        ]);
    }

    public function xsd() {
        $file = \File::get(__DIR__."/../schemes/ProviderWebService.xsd");

		return response($file)
	        ->withHeaders([
	            'Content-Type' => 'text/xml',
	            'Charset' => 'utf-8'
	        ]);
    }

}
