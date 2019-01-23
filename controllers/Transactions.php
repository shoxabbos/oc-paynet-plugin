<?php namespace Shohabbos\Paynet\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Transactions extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController'    ];
    
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = [
        'manage_transactions' 
    ];

    public function __construct()
    {
        parent::__construct();
    }
}
