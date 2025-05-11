<?php

namespace App\Console\Commands;

use App\Services\wireguard\WireGuardService;
use Illuminate\Console\Command;

class WireGuardAddClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:wire-guard-add-client';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        WireGuardService::addClient(1);
    }
}
