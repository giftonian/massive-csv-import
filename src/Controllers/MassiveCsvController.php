<?php

namespace Ascentech\MassiveCsvImport\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Ascentech\MassiveCsvImport\Models\Category;

class MassiveCsvController extends Controller
{
    public function add($a, $b)
    {
        $result = $a + $b;

        $msg = '';
    //     try {
    //         Category::create([            
    //            'name' => 'Category'.rand(1, 1000),
    //            'description' => 'Category from Package',
    //            'status' => 1
    //        ]);
          
    //        $msg = "Category has been saved successfully!";
    //    } catch (\Exception $ex) {
    //        $msg = 'Something goes wrong!!';
    //    }

        return view('MassiveCsvImport::add', compact('result', 'msg'));
    }

    public function subtract($a, $b)
    {
        $result = $a - $b;
        $msg = '';
        return view('MassiveCsvImport::add', compact('result', 'msg'));
    }

    public function csvChunks($chunk_size)
    {
        echo 'Splitting large csv into '.$chunk_size.' equal size files';
    }
}
