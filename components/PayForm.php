<?php namespace Shohabbos\Paynet\Components;

use Cms\Classes\ComponentBase;
use Shohabbos\Paynet\Models\Settings;

/**
 * User session
 *
 * This will inject the user object to every page and provide the ability for
 * the user to sign out. This can also be used to restrict access to pages.
 */
class PayForm extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Payform component',
            'description' => 'Will be show pay form component'
        ];
    }


    public function onRun() {
    	$this->page['parentPayerClient'] = Settings::get('percent_payer_client');
    	$this->page['paynetPercent'] = Settings::get('percent');
    }

    
}
