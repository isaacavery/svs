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
        echo "Retrieve and parse voter data\n";
        $dir = storage_path('voter_data');
        $precinct_path = storage_path('voter_data') . '/precinct.txt';
        $list_files = scandir(storage_path('voter_data/list'));
        $history_files = scandir(storage_path('voter_data/history'));

        // Parse and write Voter List to the database
        echo "Retrieving Voter List ...\n";
        foreach ($list_files as $value) {
            $lines = [];
            $headers = false;
            if(!preg_match('/^.*\.txt$/', $value))
                continue;
            $file = fopen("$dir/list/$value",'r');
            while(($line = fgetcsv($file, 0, "\t")) !== false) {
                if(!$headers) {
                    $headers = $line;
                    if($headers[count($headers)-1 == ''])
                        unset($headers[count($headers)-1]);
                    foreach ($headers as $key => $value) {
                        $headers[$key] = strtolower($value);
                    }
                } else {
                    $lines[] = array_combine($headers,$line);
                }
            }
            echo date('H:i:a:s') . "\n";
            echo(count($lines) . " Voters found in file $value.\nWriting voters to the database ...");

            // Write the lines to the database
            foreach($lines as $line){
                try {
                    DB::table('voters')->insert($line);
                } catch( Exception $e ) {
                    dd($e);
                }
            }
            echo date('H:i:a:s') . "\n";
            echo "COMPLETE!\n";
        }
    }
}
