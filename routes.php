<?php 
	Route::any('/paynet-index', 'Shohabbos\Paynet\Controllers\Paynet@index');
	Route::any('/paynet-wsdl', 'Shohabbos\Paynet\Controllers\Paynet@wsdl');
	Route::any('/paynet-xsd', 'Shohabbos\Paynet\Controllers\Paynet@xsd');
?>
