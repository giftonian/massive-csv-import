<?php

namespace Ascentech\MassiveCsvImport\Jobs;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class MassiveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $file, $table, $columns;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $file, string $table, array $columns)
    {
        $this->file = $file;
        $this->table = $table;
        $this->columns = $columns;        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = array_map('str_getcsv', file($this->file));
        // Extract the directory path and file name
        $directoryPath  = pathinfo($this->file, PATHINFO_DIRNAME);
        $currentName    = pathinfo($this->file, PATHINFO_BASENAME);
        $nameWoExt      = pathinfo($this->file, PATHINFO_FILENAME);
        $extension      = pathinfo($this->file, PATHINFO_EXTENSION);
        
        $col_length = sizeof($this->columns);
        $className = config('massive-csv-import.models_path') . $this->getModelName($this->table);
        $modelObj = '';
        if (class_exists($className)) {
            $modelObj = new $className;
        } else {
            Log::error('MassiveCsvImport Error: Model name '.$className.' not found for '.$this->table.' table'); 
        }
        $handle = '';
        $failedAny = false;
        foreach ($data as $row) {            
            $data_arr = [];
            for ($i=0; $i < $col_length; $i++) {
                $data_arr[$this->columns[$i]] = $row[$i];
            }      
            
            try {
                $modelObj::create($data_arr);
            }
            catch (\Throwable $e) {    // If you're using PHP 7 and above, it's recommended to catch \Throwable to handle both exceptions and errors.            
                Log::error('MassiveCsvImport Job Exception: Failed to import record: ' . $e->getMessage());               
                // Creating csv of failed records                
                $failed_dir_path = $directoryPath.'/'.'failed';
                if (!File::exists($failed_dir_path)) {            
                    try {
                        $dirFlag = File::makeDirectory($failed_dir_path, 0755, true);
                        if (!$dirFlag) {
                            throw new \RuntimeException('Error while creating failed directory: ' . $failed_dir_path);
                        }
                    } catch (\Throwable $e) {                               
                        Log::error('Error while creating failed directory: ' . $failed_dir_path." ".$e->getMessage());                        
                    }          
                }

                $handle = fopen($failed_dir_path.'/'.$nameWoExt.'-failed.'.$extension, 'a+');
                $failedAny = true;
                try {
                    fputcsv($handle, $row);           
                } catch(\Throwable $e) {                           
                    Log::error("Failed to write failed records for  ".$this->file." ".$e->getMessage());                                                  
                }

                // ./ Creating csv of failed records
                continue;
            }           
        }    
        
        if ($failedAny) {
            fclose($handle);
        }

        //unlink($this->file);
        // Renaming processed file
        $newName = $currentName.'-processed';

        // Generate the new file path with the desired new name
        $newFilePath = $directoryPath . '/' . $newName;

        // Rename the file
        try {
            File::move($this->file, $newFilePath);
        } catch (\Throwable $e) {
            Log::error("Unable to rename the processed file ".$this->file." - ".$e->getMessage()); 
        }
        // ./Renaming processed file
    }


    function getModelName($table)
    {
        return Str::studly(Str::singular($table));
    }
}
