<?php namespace Shohabbos\Paynet\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;

class Transactions extends Controller
{
    public $implement = [
    	'Backend\Behaviors\ListController'
    ];
    
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = [
        'manage_transactions' 
    ];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Shohabbos.Paynet', 'transactions');
    }
}
