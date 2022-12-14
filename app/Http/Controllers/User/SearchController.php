<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Result;
use DataTables;

class SearchController extends Controller
{
    /**
     * Show search results
     */
    public function index(Request $request)
    {
        $results = Result::where('user_id', Auth::user()->id)->where( 'title', 'LIKE', '%' . $request->keyword . '%' )->orWhere( 'text', 'LIKE', '%' . $request->keyword . '%' )->latest()->get();
        
        $data =  Datatables::of($results)
                ->addIndexColumn()
                ->addColumn('created-on', function($row){
                    $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                    return $created_on;
                })
                ->addColumn('custom-voice-type', function($row){
                    $custom_voice = '<span class="cell-box voice-'.strtolower($row["voice_type"]).'">'.ucfirst($row["voice_type"]).'</span>';
                    return $custom_voice;
                })                   
                ->addColumn('result', function($row){
                    $result = ($row['storage'] == 'local') ? URL::asset($row['result_url']) : $row['result_url'];
                    return $result;
                })
                ->addColumn('vendor', function($row){
                    $path = URL::asset($row['vendor_img']);
                    $vendor = '<div class="vendor-image-sm overflow-hidden"><img alt="vendor" class="rounded-circle" src="' . $path . '"></div>';
                    return $vendor;
                })
                ->rawColumns(['created-on', 'custom-voice-type', 'result', 'vendor'])
                ->make(true);
        

        $searchValue = $request->keyword;
        $data = json_encode($data);

        return view('user.search.index', compact('searchValue', 'data'));
    }
}
