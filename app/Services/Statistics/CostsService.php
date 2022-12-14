<?php

namespace App\Services\Statistics;

use DB;

class CostsService 
{
    private $year;
    private $month;

    public function __construct(int $year = null, int $month = null) 
    {
        $this->year = $year;
        $this->month = $month;
    }


    public function getCostPerText($id)
    {   
        $cost = DB::table('results')
                    ->join('vendors', 'results.vendor_id', '=', 'vendors.vendor_id')
                    ->where('results.id', $id)
                    ->select(DB::raw('(results.characters * vendors.cost) as data'))              
                    ->get();  

        return $cost;
    }


    public function getSpending()
    {
        $spending = DB::table('results')
                ->join('vendors', 'results.vendor_id', '=', 'vendors.vendor_id')
                ->whereYear('results.created_at', $this->year)
                ->select(DB::raw('sum(results.characters * vendors.cost) as data'), DB::raw("MONTH(results.created_at) month"))
                ->groupBy('month')
                ->orderBy('month')
                ->get(); 
        
        $data = [];

        for($i = 1; $i <= 12; $i++) {
            $data[$i] = 0;
        }

        foreach ($spending as $row) {	
            $month = $row->month;
            $data[$month] = number_format((float)$row->data, 2, '.', '');            	   
        }
        
        return $data;
    }


    public function getTotalCostForTextYearly()
    {   
        $data = DB::table('results')
                    ->join('vendors', 'results.vendor_id', '=', 'vendors.vendor_id')
                    ->whereYear('results.created_at', $this->year)
                    ->select(DB::raw('sum(results.characters * vendors.cost) as data'))
                    ->get();  

        $cost = get_object_vars($data[0]);
             
        return $cost['data'];

       
    }


    public function getTotalCostForTextCurrentMonth()
    {   
        $data= DB::table('results')
                        ->join('vendors', 'results.vendor_id', '=', 'vendors.vendor_id')
                        ->whereMonth('results.created_at', $this->month)
                        ->select(DB::raw('sum(results.characters * vendors.cost) as data'))
                        ->get();   
        
        $cost = get_object_vars($data[0]);

        return $cost['data'];
    }


    public function getTotalCostForTextPastMonth()
    {   
        $date = \Carbon\Carbon::now();
        $pastMonth =  $date->subMonth()->format('m');

        $data= DB::table('results')
                        ->join('vendors', 'results.vendor_id', '=', 'vendors.vendor_id')
                        ->whereMonth('results.created_at', $pastMonth)
                        ->select(DB::raw('sum(results.characters * vendors.cost) as data'))
                        ->get();   
        
        $cost = get_object_vars($data[0]);       

        return $cost['data'];
    }


    public function getTotalAWSCostCurrentMonth()
    {   
        $data= DB::table('results')
                        ->join('vendors', 'results.vendor_id', '=', 'vendors.vendor_id')
                        ->where('results.vendor', 'aws')
                        ->whereMonth('results.created_at', $this->month)
                        ->select(DB::raw('sum(results.characters * vendors.cost) as data'))
                        ->get();   
        
        $cost = get_object_vars($data[0]);

        return $cost['data'];
    }


    public function getTotalAWSCostCurrentYear()
    {   
        $data= DB::table('results')
                        ->join('vendors', 'results.vendor_id', '=', 'vendors.vendor_id')
                        ->where('results.vendor', 'aws')
                        ->whereYear('results.created_at', $this->year)
                        ->select(DB::raw('sum(results.characters * vendors.cost) as data'))
                        ->get();   
        
        $cost = get_object_vars($data[0]);

        return $cost['data'];
    }


    public function getTotalAzureCostCurrentMonth()
    {   
        $data= DB::table('results')
                        ->join('vendors', 'results.vendor_id', '=', 'vendors.vendor_id')
                        ->where('results.vendor', 'azure')
                        ->whereMonth('results.created_at', $this->month)
                        ->select(DB::raw('sum(results.characters * vendors.cost) as data'))
                        ->get();   
        
        $cost = get_object_vars($data[0]);

        return $cost['data'];
    }


    public function getTotalAzureCostCurrentYear()
    {   
        $data= DB::table('results')
                        ->join('vendors', 'results.vendor_id', '=', 'vendors.vendor_id')
                        ->where('results.vendor', 'azure')
                        ->whereYear('results.created_at', $this->year)
                        ->select(DB::raw('sum(results.characters * vendors.cost) as data'))
                        ->get();   
        
        $cost = get_object_vars($data[0]);

        return $cost['data'];
    }


    public function getTotalGCPCostCurrentMonth()
    {   
        $data= DB::table('results')
                        ->join('vendors', 'results.vendor_id', '=', 'vendors.vendor_id')
                        ->where('results.vendor', 'gcp')
                        ->whereMonth('results.created_at', $this->month)
                        ->select(DB::raw('sum(results.characters * vendors.cost) as data'))
                        ->get();   
        
        $cost = get_object_vars($data[0]);

        return $cost['data'];
    }


    public function getTotalGCPCostCurrentYear()
    {   
        $data= DB::table('results')
                        ->join('vendors', 'results.vendor_id', '=', 'vendors.vendor_id')
                        ->where('results.vendor', 'gcp')
                        ->whereYear('results.created_at', $this->year)
                        ->select(DB::raw('sum(results.characters * vendors.cost) as data'))
                        ->get();   
        
        $cost = get_object_vars($data[0]);

        return $cost['data'];
    }


    public function getTotalIBMCostCurrentMonth()
    {   
        $data= DB::table('results')
                        ->join('vendors', 'results.vendor_id', '=', 'vendors.vendor_id')
                        ->where('results.vendor', 'ibm')
                        ->whereMonth('results.created_at', $this->month)
                        ->select(DB::raw('sum(results.characters * vendors.cost) as data'))
                        ->get();   
        
        $cost = get_object_vars($data[0]);

        return $cost['data'];
    }


    public function getTotalIBMCostCurrentYear()
    {   
        $data= DB::table('results')
                        ->join('vendors', 'results.vendor_id', '=', 'vendors.vendor_id')
                        ->where('results.vendor', 'ibm')
                        ->whereYear('results.created_at', $this->year)
                        ->select(DB::raw('sum(results.characters * vendors.cost) as data'))
                        ->get();   
        
        $cost = get_object_vars($data[0]);

        return $cost['data'];
    }

}