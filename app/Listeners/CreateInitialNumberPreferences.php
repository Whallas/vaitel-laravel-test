<?php

namespace App\Listeners;

use App\Events\NumberCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateInitialNumberPreferences implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param  NumberCreated  $event
     * @return void
     */
    public function handle($event)
    {
        $event->number->preferences()->createMany([
            [
                'name'  => 'auto_attendant',
                'value' => '1',
            ],
            [
                'name'  => 'voicemail_enabled',
                'value' => '1',
            ],
        ]);
    }
}
