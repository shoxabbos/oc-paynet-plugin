<?php namespace Shohabbos\Paynet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateShohabbosPaynetTransactions extends Migration
{
    public function up()
    {
        Schema::create('shohabbos_paynet_transactions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('trans_id');
            $table->string('owner_id');
            $table->integer('amount');
            $table->integer('status');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('shohabbos_paynet_transactions');
    }
}
