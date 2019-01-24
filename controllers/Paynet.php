<?php namespace Shohabbos\Paynet\Controllers;

use StdClass;
use SoapServer;
use SoapClient;
use Backend\Classes\Controller;
use Shohabbos\Paynet\Classes\ProviderWebService;


class Paynet extends Controller
{

	public function index() {
		date_default_timezone_set("Asia/Samarkand");
		ini_set("soap.wsdl_cashe_enabled","0");

		$server = new SoapServer("http://mangu.itmaker.uz/paynet-wsdl", [
			'soap_version' => SOAP_1_2,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'encoding' => 'UTF-8'
		]);

		$server->setClass(ProviderWebService::class);
		$server->handle();
	}


	public function wsdl() {
		$file = \File::get(__DIR__."/../schemes/ProviderWebService.wsdl");

		return response($file)
	        ->withHeaders([
	            'Content-Type' => 'text/xml; charset=utf-8',
	            'Charset' => 'utf-8'
	        ]);
    }

    public function xsd() {
        $file = \File::get(__DIR__."/../schemes/ProviderWebService.xsd");

		return response($file)
	        ->withHeaders([
	            'Content-Type' => 'text/xml; charset=utf-8',
	            'Charset' => 'utf-8'
	        ]);
    }

}
