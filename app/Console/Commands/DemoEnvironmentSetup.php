<?php

namespace App\Console\Commands;

use App\Models\Carrier;
use App\Models\Facility;
use App\Models\Location;
use App\Models\Shipper;
use Illuminate\Console\Command;

class DemoEnvironmentSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:setup {organization_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uses faker to create data to support a demo environment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $organizationId = intval($this->argument('organization_id'));

        run_with_organization($organizationId, function () {
            Facility::factory(10)->create();
            $this->comment('Created 10 facilities');

            Carrier::factory(10)->create();
            $this->comment('Created 10 carriers');

            Shipper::factory(10)->create();
            $this->comment('Created 10 shippers');

            Location::factory(10)->create();
            $this->comment('Created 10 additional locations');
        });

        $this->newLine();
        $this->info('Demo environment setup complete');
    }
}
