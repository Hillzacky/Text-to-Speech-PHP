<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class TTSVoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $languages = DB::table('languages')
                ->orderBy('language', 'asc')
                ->get();

        $voices = DB::table('voices')
                ->where('language_code', 'en-US')
                ->get();

        $data['data'] = json_encode($voices);

        return view('user.tts-voices.index', compact('languages', 'data'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function change(Request $request)
    {
        $voices = DB::table('voices')
                ->where('language_code', request('code'))
                ->get();

        return response()->json($voices);
    }

}
