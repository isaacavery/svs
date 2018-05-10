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
        $skip = 0;

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

                    // Skip all the ACP lines
                    if(!is_numeric($line[0])){
                        $skip++;
                        continue;
                    }

                    // Handle all the Confidential lines
                    if($line[6] == 'Confidential'){
                        $line[5] = $line[11] = $line[12] = $line[13] = $line[14] = $line[15] = $line[16] = $line[17] = $line[18] = $line[19] = $line[20] = $line[21] = $line[22] = $line[23] = $line[24] = $line[25] = null;
                    }

                    // Handle all the empty spaces and character encoding issues
                    foreach($line as $k => $v){
                        $line[$k] = iconv('Windows-1252', 'UTF-8//IGNORE', $v);
                        if($v == '')
                            $line[$k] = null;
                        switch($k){
                            case 5 :
                            case 13 :
                            case 24 :
                            case 25 :
                            case 32 :
                            case 33 :
                            case 36 :
                                // Handle Integer data type
                                if($line[$k] != null)
                                    $line[$k] = intval($line[$k]);
                                break;
                            case 7 :
                                // Handle Date data type
                                $line[$k] = date('Y-m-d', strtotime($line[$k]));
                                break;
                        }
                    }

                    $row++;
                    
                    $data = array_combine($headers,$line);

                    $insert_data[] = $data;


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
        echo "\nSuccessfully imported $total_voters voters. Skipped $skip";
    }

}
