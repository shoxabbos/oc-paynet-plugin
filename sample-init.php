<?php
use RainLab\User\Models\User as UserModel;
use Shohabbos\Paynet\Models\Transaction;

Event::listen('shohabbos.paynet.existsAccount', function ($userId, &$user) {
    // find order or account
	$user = UserModel::find($id);
});

// perform transaction and fill user balance
Event::listen('shohabbos.paynet.performTransaction', function (Transaction $transaction, &$parameters) {
	// fill user balance
    $user = UserModel::find($transaction->owner_id);

    if ($user) {
    	$user->balance += $transaction->amount;
    	$user->save();

    	$parameters = [];
    	$parameters['balance'] = $user->balance;
    }

});


// cancel transaction and set status = 0
Event::listen('shohabbos.paynet.cancelTransaction', function (Transaction $transaction, &$status) {
	// fill user balance
    $user = UserModel::find($transaction->owner_id);

    if ($user) {
    	$user->balance -= $transaction->amount;
    	$user->save();

    	$status = 0;
    }

});

// set additional params
Event::listen('shohabbos.paynet.getInformation', function ($userId, &$parameters) {

});
