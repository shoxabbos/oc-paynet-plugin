<?php namespace Shohabbos\Paynet;

use Backend;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function registerComponents()
    {
        return [
            \Shohabbos\Paynet\Components\PayForm::class => 'paynetPayform',
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'category'    => 'PAYNET',
                'label'       => 'Settings',
                'description' => 'Paynet payment system',
                'icon'        => 'icon-cog',
                'class'       => 'Shohabbos\Paynet\Models\Settings',
                'order'       => 1,
                'keywords'    => 'paynet payments',
                'permissions' => ['manage_paynet_settings']
            ],
            'transactions' => [
                'category'    => 'PAYNET',
                'label'       => 'Transactions',
                'description' => 'History of transactions',
                'icon'        => 'icon-list-alt',
                'url'         => Backend::url('shohabbos/paynet/transactions'),
                'order'       => 2,
                'permissions' => ['manage_paynet_transactions']
            ],
        ];
    }

}
