<?php

namespace Ascentech\MassiveCsvImport\Jobs;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

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
        //dump('Processing file: '.$this->file);
        $col_length = sizeof($this->columns);
        $className = 'App\\'.'Models\\' . $this->getModelName($this->table);
        $modelObj = '';
        if (class_exists($className)) {
            $modelObj = new $className;
        } else {
            Log::error('MassiveCsvImport Error: Model name '.$className.' not found for '.$this->table.' table'); 
        }

        foreach ($data as $row) {            
            $data_arr = [];
            for ($i=0; $i < $col_length; $i++) {
                $data_arr[$this->columns[$i]] = $row[$i];
            }      
            
            try {
                $modelObj::create($data_arr);
            }
            catch (QueryException $e) {                
                Log::error('MassiveCsvImport Error: Failed to import record: ' . $e->getMessage());               
                // throw $e;
                continue;
            }           
        }
            
        //dump('Done processing file: '.$this->file);

        unlink($this->file);
    }


    function getModelName($table)
    {
        return Str::studly(Str::singular($table));
    }
}
