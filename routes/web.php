<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('test', function () {
	event(new App\Events\ServerStateChanged('stopped'));
});

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->middleware(['auth'])->name('home');

Route::get('start-server', function () {

	$InstanceIds = [env('AWS_INSTANCE_ID')];

	$client = app('ec2-client');

	$client->startInstances(compact('InstanceIds'));

	return ['message' => true];

})->name('start-server');

Route::get('shutdown-server', function () {

	$InstanceIds = [env('AWS_INSTANCE_ID')];

	$client = app('ec2-client');

	$client->stopInstances(compact('InstanceIds'));

	return ['message' => true];

})->name('shutdown-server');

Route::get('get-server-ip', function () {

	$InstanceIds = [env('AWS_INSTANCE_ID')];

	$client = app('ec2-client');

	$result = $client->describeInstances(compact('InstanceIds'))->toArray();

	$instance = $result['Reservations'][0]['Instances'][0];

	if (array_key_exists('PublicIpAddress', $instance)) {

		return ['ip' => $instance['PublicIpAddress']];

	}

	return ['ip' => 'Server is not running.'];

})->name('get-server-ip');

Route::get('get-server-state', function () {
	$InstanceIds = [env('AWS_INSTANCE_ID')];

	$client = app('ec2-client');

	$result = $client->describeInstances(compact('InstanceIds'))->toArray();

	$instance = $result['Reservations'][0]['Instances'][0];

	$state = $instance['State']['Name'];

	return compact('state');
})->middleware(['auth'])->name('get-server-state');