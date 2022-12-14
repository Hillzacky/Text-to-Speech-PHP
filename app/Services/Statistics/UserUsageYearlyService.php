<?php

namespace App\Services\Statistics;

use Illuminate\Support\Facades\Auth;
use App\Models\Result;
use DB;

class UserUsageYearlyService 
{
    private $year;

    public function __construct(int $year)
    {
        $this->year = $year;
    }


    public function getStandardCharsUsage($user = null)
    {
        $user_id = (is_null($user)) ? Auth::user()->id : $user;

        $standard_chars = Result::select(DB::raw("sum(characters) as data"), DB::raw("MONTH(created_at) month"))
                ->whereYear('created_at', $this->year)
                ->where('user_id', $user_id)
                ->where('voice_type', 'Standard')
                ->groupBy('month')
                ->orderBy('month')
                ->get()->toArray();  
        
        $data = [];

        for($i = 1; $i <= 12; $i++) {
            $data[$i] = 0;
        }

        foreach ($standard_chars as $row) {				            
            $month = $row['month'];
            $data[$month] = intval($row['data']);
        }
        
        return $data;
    }


    public function getNeuralCharsUsage($user = null)
    {
        $user_id = (is_null($user)) ? Auth::user()->id : $user;

        $neural_chars = Result::select(DB::raw("sum(characters) as data"), DB::raw("MONTH(created_at) as month"))
                ->whereYear('created_at', $this->year)
                ->where('user_id', $user_id)
                ->where('voice_type', 'Neural')
                ->groupBy('month')
                ->orderBy('month')
                ->get()->toArray();  
        
        $data = [];

        for($i = 1; $i <= 12; $i++) {
            $data[$i] = 0;
        }

        foreach ($neural_chars as $row) {				            
            $month = $row['month'];
            $data[$month] = intval($row['data']);
        }
        
        return $data;
    }


    public function getTotalStandardCharsUsage($user = null)
    {   
        $user_id = (is_null($user)) ? Auth::user()->id : $user;

        $standard_chars = Result::select(DB::raw("sum(characters) as data"))
                ->whereYear('created_at', $this->year)
                ->where('user_id', $user_id)
                ->where('voice_type', 'Standard')
                ->get();  
        
        return $standard_chars;
    }


    public function getTotalNeuralCharsUsage($user = null)
    {
        $user_id = (is_null($user)) ? Auth::user()->id : $user;

        $neural_chars = Result::select(DB::raw("sum(characters) as data"))
                ->whereYear('created_at', $this->year)
                ->where('user_id', $user_id)
                ->where('voice_type', 'Neural')
                ->get();  
        
        return $neural_chars;
    }

    
    public function getTotalAudioFiles($user = null)
    {
        $user_id = (is_null($user)) ? Auth::user()->id : $user;

        $audio_files = Result::select(DB::raw("count(id) as data"))
                ->whereYear('created_at', $this->year)
                ->where('user_id', $user_id)
                ->where('mode', 'file')
                ->get();  
        
        return $audio_files;
    }


    public function getTotalListenModes($user = null)
    {
        $user_id = (is_null($user)) ? Auth::user()->id : $user;

        $audio_files = Result::select(DB::raw("count(id) as data"))
                ->whereYear('created_at', $this->year)
                ->where('user_id', $user_id)
                ->where('mode', 'live')
                ->get();  
        
        return $audio_files;
    }


    public function getAllCharsUsage($user = null)
    {
        $user_id = (is_null($user)) ? Auth::user()->id : $user;

        $chars = Result::select(DB::raw("sum(characters) as data"))
                ->where('user_id', $user_id)
                ->get();  
        
        return $chars;
    }
}