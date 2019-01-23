<?php namespace Shohabbos\Paynet\Models;

use Model;

/**
 * Model
 */
class Transaction extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'shohabbos_paynet_transactions';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
