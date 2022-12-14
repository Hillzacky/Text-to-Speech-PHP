<?php

namespace App\Services\Statistics;

use App\Models\User;
use DB;

class UserRegistrationMonthlyService 
{
    private $month;
    private $year;

    public function __construct(int $year, int $month)
    {
        $this->month = $month;
        $this->year = $year;
    }


    public function getRegisteredUsers()
    {
        $users = User::select(DB::raw("count(id) as data"), DB::raw("DAY(created_at) day"))
                ->whereMonth('created_at', $this->month)
                ->whereYear('created_at', $this->year)
                ->groupBy('day')
                ->orderBy('day')
                ->get()->toArray();  
        
        $data = [];

        for($i = 1; $i <= 31; $i++) {
            $data[$i] = 0;
        }

        foreach ($users as $row) {				            
            $month = $row['day'];
            $data[$month] = intval($row['data']);
        }
        
        return $data;
    }







}