<?php namespace Shohabbos\Paynet\Models;

use Model;
use Illuminate\Filesystem\Filesystem;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'shohabbos_paynet_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';

    protected $files;

    public function __construct()
    {
        parent::__construct();
        $this->files = new Filesystem;
    }

	public function afterSave()
	{
        // write code
        $code = self::get('code', null);
        if ($code) {
            $this->files->put(__DIR__ . '/'.'../init.php', $code);
        }
	}
    
}