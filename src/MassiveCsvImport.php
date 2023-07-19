<?php

namespace Ascentech\MassiveCsvImport;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Ascentech\MassiveCsvImport\Jobs\MassiveJob;
use Illuminate\Support\Facades\Log;

class MassiveCsvImport
{
    public function import($filePath, $table, $columns = [])
    {          
        $result = [];         
        $className = config('massive-csv-import.models_path') . $this->getModelName($table);

        $modelObj = '';
        if (class_exists($className)) {
            $modelObj = new $className;            
        } else {            
            $result['status'] = 0;
            $result['message'] = $className.' Model not found for the table '.$table;         
            Log::error($className.' Model not found for the table '.$table);

            return $result;
        }

        // Creating necessary Directories

        $pending_files_path = config('massive-csv-import.files_path');
        if (!File::exists($pending_files_path)) {            
            try {
                $dirFlag = File::makeDirectory($pending_files_path, 0755, true);
                if (!$dirFlag) {
                    throw new \RuntimeException('Error while creating directory: ' . $pending_files_path);
                }
            } catch (\Throwable $e) {
                 $result['status'] = 0;
                 $result['message'] = "Error while creating directory ".$pending_files_path;         
                 Log::error("Error while creating directory ".$pending_files_path." ".$e->getMessage());
                
                 return $result;
            }
        }

        $table_dir_path = $pending_files_path.'/'.$table;
        if (!File::exists($table_dir_path)) {            
            try {
                $dirFlag = File::makeDirectory($table_dir_path, 0755, true);
                if (!$dirFlag) {
                    throw new \RuntimeException('Error while creating directory: ' . $table_dir_path);
                }
            } catch (\Throwable $e) {
                 $result['status'] = 0;
                 $result['message'] = "Error while creating directory ".$table_dir_path;         
                 Log::error("Error while creating directory ".$table_dir_path." ".$e->getMessage());
                
                 return $result;
            }          
        }
        
        $file = '';
        try {
            $file = file($filePath);            
        } catch(\Throwable $e) {
            $result['status'] = 0;
            $result['message'] = "Invalid file path ".$filePath;         
            Log::error("Invalid file path ".$filePath." ".$e->getMessage());            
            return $result;
        }

        $data = $file; 

        $parts = (array_chunk($data, config('massive-csv-import.csv_chunk_size'))); // returns chunks of specified number

        
        foreach ($parts as $index => $part) {
            $fileName = $table_dir_path.'/'.date('y-m-d-H-i-s').'-'.$index.'.csv';            
            try {
                file_put_contents($fileName, $part);            
            } catch(\Throwable $e) {
                $result['status'] = 0;
                $result['message'] = "Failed to write in the file ".$fileName;         
                Log::error("Failed to write in the file ".$fileName." ".$e->getMessage());            
                return $result;
            }
            //break;
        }

        // DB Entries

        return $this->importToDB($table_dir_path, $table, $columns);
       
    }

    function importToDB($table_dir_path, $table, $columns)
    {
        $result = [];
        $path = $table_dir_path.'/*.csv';        

        $files = glob($path); // getting all files from the path specified                
       
        foreach ($files as $file) { // array_slice($files, 0, 1) getting 1 file at a time
            
            MassiveJob::dispatch($file, $table, $columns);                     
        }
        $result['status'] = 1;
        $result['message'] = "Multiple CSV files added to Jobs Queue successfully. Make sure to run the Laravel queue work command and also check the ".$table_dir_path."/failed directory for any unsuccessful records.";         
        Log::info('Jobs running successfully!');
        return $result;
    }

    function getModelName($table)
    {
        return Str::studly(Str::singular($table));
    }

    
    
}
