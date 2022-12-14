<?php

namespace App\Services\Statistics;

use App\Models\Result;
use DB;

class TTSService 
{
    private $year;
    private $month;

    public function __construct(int $year, int $month)
    {
        $this->year = $year;
        $this->month = $month;
    }


    public function getFreeCharsUsageYearly()
    {
        $free_chars = Result::select(DB::raw("sum(characters) as data"), DB::raw("MONTH(created_at) month"))
                ->whereYear('created_at', $this->year)
                ->where('plan_type', 'Free')
                ->groupBy('month')
                ->orderBy('month')
                ->get()->toArray();  
        
        $data = [];

        for($i = 1; $i <= 12; $i++) {
            $data[$i] = 0;
        }

        foreach ($free_chars as $row) {				            
            $month = $row['month'];
            $data[$month] = intval($row['data']);
        }
        
        return $data;
    }


    public function getPaidCharsUsageYearly()
    {
        $paid_chars = Result::select(DB::raw("sum(characters) as data"), DB::raw("MONTH(created_at) as month"))
                ->whereYear('created_at', $this->year)
                ->where('plan_type', 'Paid')
                ->groupBy('month')
                ->orderBy('month')
                ->get()->toArray();  
        
        $data = [];

        for($i = 1; $i <= 12; $i++) {
            $data[$i] = 0;
        }

        foreach ($paid_chars as $row) {				            
            $month = $row['month'];
            $data[$month] = intval($row['data']);
        }
        
        return $data;
    }


    public function getTotalFreeCharsUsageYearly()
    {   
        $free_chars = Result::select(DB::raw("sum(characters) as data"))
                ->whereYear('created_at', $this->year)
                ->where('plan_type', 'Free')
                ->get();  
        
        return $free_chars;
    }


    public function getTotalPaidCharsUsageYearly()
    {
        $paid_chars = Result::select(DB::raw("sum(characters) as data"))
                ->whereYear('created_at', $this->year)
                ->where('plan_type', 'Paid')
                ->get();  
        
        return $paid_chars;
    }


    public function getTotalStandardCharsUsageYearly()
    {   
        $standard_chars = Result::select(DB::raw("sum(characters) as data"))
                ->whereYear('created_at', $this->year)
                ->where('voice_type', 'Standard')
                ->get();  
        
        return $standard_chars;
    }


    public function getTotalNeuralCharsUsageYearly()
    {
        $neural_chars = Result::select(DB::raw("sum(characters) as data"))
                ->whereYear('created_at', $this->year)
                ->where('voice_type', 'Neural')
                ->get();  
        
        return $neural_chars;
    }

    
    public function getTotalAudioFilesYearly()
    {
        $audio_files = Result::select(DB::raw("count(result_url) as data"))
                ->whereYear('created_at', $this->year)
                ->get();  
        
        return $audio_files;
    }


    public function getTotalListenResultsYearly()
    {
        $audio_files = Result::select(DB::raw("count(id) as data"))
                ->whereYear('created_at', $this->year)
                ->where('mode', 'live')
                ->get();  
        
        return $audio_files;
    }


    public function getTotalAudioFilesMonthly()
    {
        $audio_files = Result::select(DB::raw("count(result_url) as data"))
                ->whereMonth('created_at', $this->month)
                ->whereYear('created_at', $this->year)
                ->get();  
        
        return $audio_files;
    }


    public function getTotalAudioFilesPastMonth()
    {   
        $date = \Carbon\Carbon::now();
        $pastMonth =  $date->subMonth()->format('m');

        $audio_files = Result::select(DB::raw("count(result_url) as data"))
                ->whereMonth('created_at', $pastMonth)
                ->whereYear('created_at', $this->year)
                ->get();  
        
        return $audio_files;
    }


    public function getTotalStandardCharsUsageMonthly()
    {   
        $standard_chars = Result::select(DB::raw("sum(characters) as data"))
                ->whereMonth('created_at', $this->month)
                ->whereYear('created_at', $this->year)
                ->where('voice_type', 'Standard')
                ->get();  
        
        return $standard_chars;
    }


    public function getTotalStandardCharsUsagePastMonth()
    {   
        $date = \Carbon\Carbon::now();
        $pastMonth =  $date->subMonth()->format('m');

        $standard_chars = Result::select(DB::raw("sum(characters) as data"))
                ->whereMonth('created_at', $pastMonth)
                ->whereYear('created_at', $this->year)
                ->where('voice_type', 'Standard')
                ->get();  
        
        return $standard_chars;
    }


    public function getTotalNeuralCharsUsageMonthly()
    {
        $neural_chars = Result::select(DB::raw("sum(characters) as data"))
                ->whereMonth('created_at', $this->month)
                ->whereYear('created_at', $this->year)
                ->where('voice_type', 'Neural')
                ->get();  
        
        return $neural_chars;
    }


    public function getTotalNeuralCharsUsagePastMonth()
    {   
        $date = \Carbon\Carbon::now();
        $pastMonth =  $date->subMonth()->format('m');

        $neural_chars = Result::select(DB::raw("sum(characters) as data"))
                ->whereMonth('created_at', $pastMonth)
                ->whereYear('created_at', $this->year)
                ->where('voice_type', 'Neural')
                ->get();  
        
        return $neural_chars;
    }


    public function getTotalFreeCharsUsageMonthly()
    {
        $free_chars = Result::select(DB::raw("sum(characters) as data"))
                ->whereMonth('created_at', $this->month)
                ->whereYear('created_at', $this->year)
                ->where('plan_type', 'Free')
                ->get();  
        
        return $free_chars;
    }


    public function getTotalFreeCharsUsagePastMonth()
    {   
        $date = \Carbon\Carbon::now();
        $pastMonth =  $date->subMonth()->format('m');

        $free_chars = Result::select(DB::raw("sum(characters) as data"))
                ->whereMonth('created_at', $pastMonth)
                ->whereYear('created_at', $this->year)
                ->where('plan_type', 'Free')
                ->get();  
        
        return $free_chars;
    }


    public function getTotalPaidCharsUsageMonthly()
    {
        $paid_chars = Result::select(DB::raw("sum(characters) as data"))
                ->whereMonth('created_at', $this->month)
                ->whereYear('created_at', $this->year)
                ->where('plan_type', 'Paid')
                ->get();  
        
        return $paid_chars;
    }


    public function getTotalPaidCharsUsagePastMonth()
    {   
        $date = \Carbon\Carbon::now();
        $pastMonth =  $date->subMonth()->format('m');

        $paid_chars = Result::select(DB::raw("sum(characters) as data"))
                ->whereMonth('created_at', $pastMonth)
                ->whereYear('created_at', $this->year)
                ->where('plan_type', 'Paid')
                ->get();  
        
        return $paid_chars;
    }


    public function getAWSUsageMonthly()
    {
        $vendor = Result::select(DB::raw("sum(characters) as data"))
                ->whereMonth('created_at', $this->month)
                ->whereYear('created_at', $this->year)
                ->where('vendor', 'aws')
                ->get();  
        
        return $vendor;
    }


    public function getAWSUsageYearly()
    {
        $vendor = Result::select(DB::raw("sum(characters) as data"))
                ->whereYear('created_at', $this->year)
                ->where('vendor', 'aws')
                ->get();  
        
        return $vendor;
    }


    public function getAWSStandardUsageYearly()
    {
        $standard = Result::select(DB::raw("sum(characters) as data"))
                ->whereYear('created_at', $this->year)
                ->where('vendor', 'aws')
                ->where('voice_type', 'Standard')
                ->get();  
        
        return $standard;
    }


    public function getAWSNeuralUsageYearly()
    {
        $neural = Result::select(DB::raw("sum(characters) as data"))
                ->whereYear('created_at', $this->year)
                ->where('vendor', 'aws')
                ->where('voice_type', 'Neural')
                ->get();  
        
        return $neural;
    }


    public function getAzureUsageMonthly()
    {
        $vendor = Result::select(DB::raw("sum(characters) as data"))
                ->whereMonth('created_at', $this->month)
                ->whereYear('created_at', $this->year)
                ->where('vendor', 'azure')
                ->get();  
        
        return $vendor;
    }


    public function getAzureUsageYearly()
    {
        $vendor = Result::select(DB::raw("sum(characters) as data"))
                ->whereYear('created_at', $this->year)
                ->where('vendor', 'azure')
                ->get();  
        
        return $vendor;
    }


    public function getAzureStandardUsageYearly()
    {
        $standard = Result::select(DB::raw("sum(characters) as data"))
                ->whereYear('created_at', $this->year)
                ->where('vendor', 'azure')
                ->where('voice_type', 'Standard')
                ->get();  
        
        return $standard;
    }


    public function getAzureNeuralUsageYearly()
    {
        $neural = Result::select(DB::raw("sum(characters) as data"))
                ->whereYear('created_at', $this->year)
                ->where('vendor', 'azure')
                ->where('voice_type', 'Neural')
                ->get();  
        
        return $neural;
    }


    public function getGCPUsageMonthly()
    {
        $vendor = Result::select(DB::raw("sum(characters) as data"))
                ->whereMonth('created_at', $this->month)
                ->whereYear('created_at', $this->year)
                ->where('vendor', 'gcp')
                ->get();  
        
        return $vendor;
    }


    public function getGCPUsageYearly()
    {
        $vendor = Result::select(DB::raw("sum(characters) as data"))
                ->whereYear('created_at', $this->year)
                ->where('vendor', 'gcp')
                ->get();  
        
        return $vendor;
    }


    public function getGCPStandardUsageYearly()
    {
        $standard = Result::select(DB::raw("sum(characters) as data"))
                ->whereYear('created_at', $this->year)
                ->where('vendor', 'gcp')
                ->where('voice_type', 'Standard')
                ->get();  
        
        return $standard;
    }


    public function getGCPNeuralUsageYearly()
    {
        $neural = Result::select(DB::raw("sum(characters) as data"))
                ->whereYear('created_at', $this->year)
                ->where('vendor', 'gcp')
                ->where('voice_type', 'Neural')
                ->get();  
        
        return $neural;
    }


    public function getIBMUsageMonthly()
    {
        $vendor = Result::select(DB::raw("sum(characters) as data"))
                ->whereMonth('created_at', $this->month)
                ->whereYear('created_at', $this->year)
                ->where('vendor', 'ibm')
                ->get();  
        
        return $vendor;
    }


    public function getIBMUsageYearly()
    {
        $vendor = Result::select(DB::raw("sum(characters) as data"))
                ->whereYear('created_at', $this->year)
                ->where('vendor', 'ibm')
                ->get();  
        
        return $vendor;
    }


    public function getIBMNeuralUsageYearly()
    {
        $neural = Result::select(DB::raw("sum(characters) as data"))
                ->whereYear('created_at', $this->year)
                ->where('vendor', 'ibm')
                ->where('voice_type', 'Neural')
                ->get();  
        
        return $neural;
    }
}