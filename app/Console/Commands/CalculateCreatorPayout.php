<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\AdminConroller;

class CalculateCreatorPayout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'creator:payout';

    protected $description =
        'Calculate creator monthly payout';

    public function handle()
    {
        app(AdminConroller::class)
            ->calculateCreatorPayout();

        $this->info(
            'Creator payout calculated successfully'
        );
    }
}
