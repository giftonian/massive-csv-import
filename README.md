# Massive CSV Import

- This package helps developer to upload csv files with millions of records efficiently with Laravel Queues.


## Prerequisites
- You must be using Laravel Queues and jobs table must exist in your database. If you are not using Queues, setup using this [link](https://laravel.com/docs/10.x/queues).
- Write privileges on `storage` directory of your Laravel project. You can change this location from configuration file of this package as well.
- By default, this package tries to search required Model class from `App\Models\` namespace. If you have placed Models in another directory, set its path in configuration file i.e., `vendor\ascentech\massive-csv-import\config\massive-csv-import.php`.

## Installation
- composer require ascentech/massive-csv-import
- Add `Ascentech\MassiveCsvImport\MassiveCsvImportServiceProvider::class,` into `providers` array of your project's `config\app.php` file.

## Usage
- Prepare a large csv file (without headers) to import.
- Prepare a file upload interface in your project and write following two lines in your Controller code:
- **use Ascentech\MassiveCsvImport\MassiveCsvImportFacade;**
- **$result = MassiveCsvImportFacade::import($path, $table_name, $columns);**
- *`$path`* refers to temp path of uploaded csv file
- *`$table_name`* is the database table name in which you want to import large csv file.
- *`$colums`* is the array of columns for the particular table e.g., *$columns = ['name','description','status'];* 
- This package will create multiple smaller csv files from the large file and save these files into `storage\table_name\` directory. By default the chunk size is 1000, you can edit `csv_chunk_size` variable's value in configuration file i.e., `vendor\ascentech\massive-csv-import\config\massive-csv-import.php`.
- A separate job is created for each smaller csv file for processing in the background.
- You will need to run `php artisan queue:work` command for the jobs processing.
- All processed files will be placed with `.csv-processed` extension in the same `storage\table_name\` directory.
- Remember! If a particular record (from smaller csv file) fails to insert into the database, an error message will be written in laravel.log file, but the remaining job will keep processing without failing. A separate directory `storage\table_name\failed` is automatically created which will have csv files with the failed records only. You can fix these and import later on in a separate csv file.


### License

- MIT
