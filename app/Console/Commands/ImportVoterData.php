<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Voter;

class ImportVoterData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voters:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the voter data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    public function handle()
    {
        echo "Importing voter data ...\n";
        $dir = storage_path('voter_data');
        $list_files = scandir(storage_path('voter_data/list'));
        $total_voters = 0;
        
        // Parse and write Voter List to the database
        echo "Loading " . count($list_files) . " files from " . storage_path('voter_data/list') . " ...\n";
        foreach ($list_files as $k => $value) {
            echo " - FILE $k ( $value ) beginning at " . date('H:i:s:a') . ":\n";
            $headers = false;
            $row = 0;
            if(!preg_match('/^.*\.txt$/', $value)){
                echo "  - Skipping: invalid file type!\n";
                continue;
            }
            $file = fopen("$dir/list/$value",'r');
            while(($line = fgetcsv($file, 0, "\t")) !== false) {
                $row++;
                if(!$headers) {
                    $headers = $line;
                    if($headers[count($headers)-1 == ''])
                        unset($headers[count($headers)-1]);
                    foreach ($headers as $key => $value) {
                        $headers[$key] = strtolower($value);
                    }   
                } else {
                    if($row % 100 === 0)
                        echo "  - $row \r";
                    $line_write = array_combine($headers,$line);
                    DB::table('voters')->insert($line_write);
                }   
            }
            $total_voters += $row;
            echo "  - Completed $row records at " . date('H:i:s:a') . ".\n";
        }
        echo "\nSuccessfully imported $total_voters voters.";
    }
}