<?php

return [
   
    /*
    |--------------------------------------------------------------------------
    | Configuration Values for Massive CSV Import Package
    | Author: Waqas Tariq Dar
    |--------------------------------------------------------------------------
    |
    | This value sets the path for testing purpose
    |
    */

    'csv_chunk_size' => 1000,
    'models_path' => 'App\\'.'Models\\', // Path for your Model classes in laravel project
    'files_path' => storage_path('massive-csv-files'), // path where chunks of large csv files will be saved for Jobs, along with any failed records csvs

    
    'massive-array' => [        
        'directory' => 'testing',   // Example: 'tmp'               
        'allowed_types' => [   // Supported file types for temporary usage - not using this value currently in package
            'csv', 'xls'
        ],        
    ],   

];
