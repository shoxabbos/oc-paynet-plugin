# Quyidagi kodni pluginning sozlamalar bulimidagi kod qismiga quying ushbu kod yordami siz plagin ishlash logikasini uzgartirishingiz mumkin

```
<?php
use RainLab\User\Models\User as UserModel;

Event::listen('shohabbos.paynet.existsAccount', function ($userId, &$user) {
    // find order or account
	$user = UserModel::find($userId);
});

// perform transaction and fill user balance
Event::listen('shohabbos.paynet.performTransaction', function ($transaction, &$parameters) {
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
Event::listen('shohabbos.paynet.cancelTransaction', function ($transaction, &$status) {
	// fill user balance
    $user = UserModel::find($transaction->owner_id);

    if ($user) {
    	$user->balance -= $transaction->amount;
    	$user->save();

    	$status = 0;
    }

});

// set additional params
Event::listen('shohabbos.paynet.getInformation', function ($ownerId, &$parameters) {
    $user = UserModel::find($ownerId);
    
    if ($user) {
        $parameters['name'] = $user->name;
        $parameters['phone'] = $user->phone;
        $parameters['balance'] = $user->balance;
    }
    
});
```
