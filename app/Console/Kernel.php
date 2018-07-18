<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->call(function () {
            
            $InstanceIds = [env('AWS_INSTANCE_ID')];

            $client = app('ec2-client');

            $result = $client->describeInstances(compact('InstanceIds'))->toArray();

            $instance = $result['Reservations'][0]['Instances'][0];

            $new_state = $instance['State']['Name'];

            $current_server_state = cache('current_server_state');

            if (is_null($current_server_state) || $current_server_state !== $new_state) {

                cache(['current_server_state' => $new_state], now()->addMinutes(5));

                event(new \App\Events\ServerStateChanged($new_state));

            }

        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
