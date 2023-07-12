<?php

namespace Ascentech\MassiveCsvImport;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Str;
use Ascentech\MassiveCsvImport\Jobs\MassiveJob;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class MassiveCsvImport
{
    // Build your great package.
    public function csvChunks($chunk_size)
    {
        echo 'Splitting large csv into '.$chunk_size.' equal size files using MassiveCsvImport <br>';
        echo 'Configuration value = '.config('massive-csv-import.val1');
        //print_r(config('massive-csv-import.val1'));
    }

    public function import($filePath, $table, $columns = [])
    {       
        
        //$modelClass = Schema::getConnection()->getDoctrineSchemaManager()->listTableDetails($table)->getClass()->getName();
        $className = 'App\\'.'Models\\' . $this->getModelName($table);

        $modelObj = '';
        if (class_exists($className)) {
            $modelObj = new $className;

            // $modelObj::create([
            //     'name'     => 'TEST',
            //     'small_description'    => 'teststs', 
            //     'description' => 'sdfsdf',
            //     'original_price' => 77787,
            //     'selling_price' => 465454,
            //     'status' => 1,                    
            // ]);

            // dd('Yes', $className);
        } else {
            //dd('Model not found');
            echo 'Model not found';
        }

        // Creating necessary Directories

        $pending_files_path = resource_path('pending-files');
        if (!File::exists($pending_files_path)) {
            File::makeDirectory($pending_files_path, 0777, true, true);
        }

        $table_dir_path = resource_path('pending-files/'.$table);
        if (!File::exists($table_dir_path)) {
            File::makeDirectory($table_dir_path, 0777, true, true);
        }
        
        $file = file($filePath);
        $data = $file; 

        $parts = (array_chunk($data, 500)); // returns chunks of specified number

        foreach ($parts as $index => $part) {
            $fileName = $table_dir_path.'/'.date('y-m-d-H-i-s').'-'.$index.'.csv';  
                    
            file_put_contents($fileName, $part);
            //break;
        }

        // DB Entries

        $this->importToDB($table_dir_path, $table, $columns);
       
    }

    function importToDB($table_dir_path, $table, $columns)
    {
        $path = $table_dir_path.'/*.csv';        

        $files = glob($path); // getting all files from the path specified                
       
        foreach ($files as $file) { // array_slice($files, 0, 1) getting 1 file at a time
            
            MassiveJob::dispatch($file, $table, $columns);                     
        }
        //dd('All done. Check jobs in Queue!');
        //echo 'All done. Check jobs in Queue!';
    }

    function getModelName($table)
    {
        return Str::studly(Str::singular($table));
    }

    
    
}
