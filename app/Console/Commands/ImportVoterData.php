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

    function convertString(&$val)
    {
        $val = iconv('Windows-1252','UTF-8',$val);
    }
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
            $insert_data = array();
            $row = 0;
            if(!preg_match('/^.*\.txt$/', $value)){
                echo "  - Skipping: invalid file type!\n";
                continue;
            }
            $file = fopen("$dir/list/$value",'r');
            $start_time = time();
            while(($line = fgetcsv($file, 0, "\t")) !== false) {
                if(!$headers) {
                    $headers = $line;
                    foreach ($headers as $key => $value) {
                        $headers[$key] = strtolower($value);
                    }
                } else {
                    $row++;
                    foreach($line as $k => $v){
                        $line[$k] = iconv('Windows-1252', 'UTF-8', $v);
                    }
                    $insert_data[] = array_combine($headers,$line);


                    if($row % 100 === 0){
                        echo "  - " . count($insert_data) . ":$row records using " . memory_get_usage() . " bytes of memory\r";
                        Voter::insert($insert_data);
                        $insert_data = array();
                    }
                }   
            }  
            fclose($file);
            echo "\n  - data parsing complete: $row records at " . memory_get_usage() . " bytes of memory.\n  - Beginning query ... ";
            
            echo "complete!\n  - Inserted $row records.\n";
            $total_voters += $row;

        }
        echo "\nSuccessfully imported $total_voters voters.";
    }

}