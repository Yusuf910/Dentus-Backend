<?php

namespace App\Console\Commands;
use App\Models\AstroShopProduct; 
use Illuminate\Support\Facades\DB;

use Illuminate\Console\Command;

class CleanJsonData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-json-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean backslashes from JSON data in the database';

    /**
     * Execute the console command.
     */

     public function __construct()
    {
        parent::__construct();
    }

     public function handle()
    {
        //
        $records = AstroShopProduct::all(); 

        foreach ($records as $record) {
            $cleanedData = json_decode($record->quality_rati, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $jsonString = json_encode($cleanedData);

                $record->quality_rati = $jsonString;
                $record->save();

                $this->info('Updated record ID ' . $record->id);
            } else {
                $this->error('Invalid JSON for record ID ' . $record->id);
            }
        }

        $this->info('All records have been processed.');
    }
    
}
