<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\Project;
use App\Models\Result;
use App\Models\User;
use Yajra\DataTables\DataTables;
use DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        # Today's TTS Results for Datatable
        if ($request->ajax()) {
            $data = Result::where('project', Auth::user()->project)->where('mode', 'file')->where('user_id', Auth::user()->id)->latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div>
                                            <a href="'. route("user.projects.show", $row["id"] ). '"><i class="fa-solid fa-list-music voice-action-buttons rename-voice" title="View Result"></i></a>
                                            <a class="deleteResultButton" id="'. $row["id"] .'" href="#"><i class="fa-solid fa-trash-xmark voice-action-buttons deactivate-voice" title="Delete Result"></i></a> 
                                        </div>';
                        return $actionBtn;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-voice-type', function($row){
                        $custom_voice = '<span class="cell-box voice-'.strtolower($row["voice_type"]).'">'.ucfirst($row["voice_type"]).'</span>';
                        return $custom_voice;
                    })
                    ->addColumn('download', function($row){
                        $url = ($row['storage'] == 'local') ? URL::asset($row['result_url']) : $row['result_url'];
                        $result = '<a class="" href="' . $url . '" download><i class="fa fa-cloud-download result-download fs-20"></i></a>';
                        return $result;
                    })
                    ->addColumn('single', function($row){
                        $url = ($row['storage'] == 'local') ? URL::asset($row['result_url']) : $row['result_url'];
                        $result = '<button type="button" class="result-play" onclick="resultPlay(this)" src="' . $url . '" type="'. $row['audio_type'].'" id="'. $row['id'] .'"><i class="fa fa-play"></i></button>';
                        return $result;
                    })
                    ->addColumn('result', function($row){ 
                        $result = ($row['storage'] == 'local') ? URL::asset($row['result_url']) : $row['result_url'];
                        return $result;
                    })
                    ->addColumn('custom-language', function($row) {
                        $language = '<span class="vendor-image-sm overflow-hidden"><img class="mr-2" src="' . URL::asset($row['language_flag']) . '">'. $row['language'] .'</span> ';            
                        return $language;
                    })
                    ->rawColumns(['actions', 'created-on', 'custom-voice-type', 'result', 'download', 'single', 'custom-language'])
                    ->make(true);
                    
        }

        $projects = Project::where('user_id', auth()->user()->id)->latest()->get();

        $data = DB::table('results')->where('project', auth()->user()->project)->where('user_id', auth()->user()->id)
                        ->select(DB::raw('sum(results.characters) as chars'), DB::raw('count(results.id) as results'))
                        ->get();
        $data = get_object_vars($data[0]); 

        return view('user.projects.index', compact('projects', 'data'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function change(Request $request)
    {
        if ($request->group == 'all') {
            if ($request->ajax()) {
                $data = Result::where('user_id', auth()->user()->id)->where('mode', 'file')->get();
                return Datatables::of($data)
                        ->addIndexColumn()
                        ->addColumn('actions', function($row){
                            $actionBtn = '<div>
                                                <a href="'. route("user.projects.show", $row["id"] ). '"><i class="fa-solid fa-list-music voice-action-buttons rename-voice" title="View Result"></i></a>
                                                <a class="deleteResultButton" id="'. $row["id"] .'" href="#"><i class="fa-solid fa-trash-xmark voice-action-buttons deactivate-voice" title="Delete Result"></i></a> 
                                            </div>';
                            return $actionBtn;
                        })
                        ->addColumn('created-on', function($row){
                            $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                            return $created_on;
                        })
                        ->addColumn('custom-voice-type', function($row){
                            $custom_voice = '<span class="cell-box voice-'.strtolower($row["voice_type"]).'">'.ucfirst($row["voice_type"]).'</span>';
                            return $custom_voice;
                        })
                        ->addColumn('download', function($row){
                            $url = ($row['storage'] == 'local') ? URL::asset($row['result_url']) : $row['result_url'];
                            $result = '<a class="" href="' . $url . '" download><i class="fa fa-cloud-download result-download fs-20"></i></a>';
                            return $result;
                        })
                        ->addColumn('single', function($row){
                            $url = ($row['storage'] == 'local') ? URL::asset($row['result_url']) : $row['result_url'];
                            $result = '<button type="button" class="result-play" onclick="resultPlay(this)" src="' . $url . '" type="'. $row['audio_type'].'" id="'. $row['id'] .'"><i class="fa fa-play"></i></button>';
                            return $result;
                        })
                        ->addColumn('result', function($row){ 
                            $result = ($row['storage'] == 'local') ? URL::asset($row['result_url']) : $row['result_url'];
                            return $result;
                        })
                        ->addColumn('custom-language', function($row) {
                            $language = '<span class="vendor-image-sm overflow-hidden"><img class="mr-2" src="' . URL::asset($row['language_flag']) . '">'. $row['language'] .'</span> ';            
                            return $language;
                        })
                        ->rawColumns(['actions', 'created-on', 'custom-voice-type', 'result', 'download', 'single', 'custom-language'])
                        ->make(true);          
            }

        } else {

            if ($request->ajax()) {
                $data = Result::where('project', $request->group)->where('user_id', auth()->user()->id)->where('mode', 'file')->get();
                return Datatables::of($data)
                        ->addIndexColumn()
                        ->addColumn('actions', function($row){
                            $actionBtn = '<div>
                                                <a href="'. route("user.projects.show", $row["id"] ). '"><i class="fa-solid fa-list-music voice-action-buttons rename-voice" title="View Result"></i></a>
                                                <a class="deleteResultButton" id="'. $row["id"] .'" href="#"><i class="fa-solid fa-trash-xmark voice-action-buttons deactivate-voice" title="Delete Result"></i></a> 
                                            </div>';
                            return $actionBtn;
                        })
                        ->addColumn('created-on', function($row){
                            $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                            return $created_on;
                        })
                        ->addColumn('custom-voice-type', function($row){
                            $custom_voice = '<span class="cell-box voice-'.strtolower($row["voice_type"]).'">'.ucfirst($row["voice_type"]).'</span>';
                            return $custom_voice;
                        })
                        ->addColumn('download', function($row){
                            $url = ($row['storage'] == 'local') ? URL::asset($row['result_url']) : $row['result_url'];
                            $result = '<a class="" href="' . $url . '" download><i class="fa fa-cloud-download result-download fs-20"></i></a>';
                            return $result;
                        })
                        ->addColumn('single', function($row){
                            $url = ($row['storage'] == 'local') ? URL::asset($row['result_url']) : $row['result_url'];
                            $result = '<button type="button" class="result-play" onclick="resultPlay(this)" src="' . $url . '" type="'. $row['audio_type'].'" id="'. $row['id'] .'"><i class="fa fa-play"></i></button>';
                            return $result;
                        })
                        ->addColumn('result', function($row){ 
                            $result = ($row['storage'] == 'local') ? URL::asset($row['result_url']) : $row['result_url'];
                            return $result;
                        })
                        ->addColumn('custom-language', function($row) {
                            $language = '<span class="vendor-image-sm overflow-hidden"><img class="mr-2" src="' . URL::asset($row['language_flag']) . '">'. $row['language'] .'</span> ';            
                            return $language;
                        })
                        ->rawColumns(['actions', 'created-on', 'custom-voice-type', 'result', 'download', 'single', 'custom-language'])
                        ->make(true);          
            }
        }

        
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $request)
    {

        if ($request->project == 'all') {
            $data_results = DB::table('results')->where('user_id', auth()->user()->id)
                        ->where('mode', 'file')
                        ->select(DB::raw('count(id) as total'))
                        ->get();
            $data_results = get_object_vars($data_results[0]); 

            $data_chars = DB::table('results')->where('user_id', auth()->user()->id)
                            ->where('mode', 'file')
                            ->select(DB::raw('sum(characters) as total'))
                            ->get();
            $data_chars = get_object_vars($data_chars[0]); 

        } else {
            $data_results = DB::table('results')->where('project', $request->project)->where('user_id', auth()->user()->id)
                        ->where('mode', 'file')
                        ->select(DB::raw('count(id) as total'))
                        ->get();
            $data_results = get_object_vars($data_results[0]); 

            $data_chars = DB::table('results')->where('project', $request->project)->where('user_id', auth()->user()->id)
                            ->where('mode', 'file')
                            ->select(DB::raw('sum(characters) as total'))
                            ->get();
            $data_chars = get_object_vars($data_chars[0]); 
        }

        
        if ($request->ajax()) {
            $data['results'] = $data_results;
            $data['chars'] = $data_chars;
            return $data;
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->ajax()) {
            request()->validate([
                'new-project' => 'required'
            ]);
    
            if (strtolower(request('new-project') == 'all')) {
                return response()->json(['status' => 'error', 'message' => 'Project Name is reserved and is already created, please create another one']);
            }
    
            $check = Project::where('user_id', auth()->user()->id)->where('name', request('new-project'))->first();
    
            if (!isset($check)) {
                $project = new Project([
                    'user_id' => auth()->user()->id,
                    'name' =>  htmlspecialchars(request('new-project'))
                ]);
        
                $project->save();
                
                return response()->json(['status' => 'success', 'message' => 'Project has been successfully created']);
            
            } else {
                return response()->json(['status' => 'error', 'message' => 'Project name already exists']);
            }
        }  
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Result $id)
    {
        if ($id->user_id == Auth::user()->id){

            return view('user.projects.show', compact('id'));     

        } else{
            return redirect()->route('user.projects');
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        request()->validate([
            'project' => 'required'
        ]);

        $check = Project::where('user_id', auth()->user()->id)->where('name', request('project'))->first();

        if (isset($check)) {
            $user = User::where('id', auth()->user()->id)->first();
            $user->project = request('project');
            $user->save();    

            return redirect()->back()->with('success', 'Default Project has been successfully updated');
        
        } else {
            return redirect()->back()->with('error', 'Default Project has not been updated. Please try again.');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        request()->validate([
            'project' => 'required'
        ]);

        $project = Project::where('user_id', auth()->user()->id)->where('name', request('project'))->first();
        

        if (isset($project)) {

            $project->delete();

            $user = User::where('id', auth()->user()->id)->first();
            $user->project = ($user->project == request('project'))? '' : $user->project;
            $user->save();    

            return redirect()->back()->with('success', 'Selected Project was deleted successfully.');
        
        } else {
            return redirect()->back()->with('error', 'Selected Project was not deleted properly. Please try again.');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        if ($request->ajax()) {

            $result = Result::where('id', request('id'))->firstOrFail();  

            if ($result->user_id == Auth::user()->id){

                $result->delete();

                return response()->json('success');    
    
            } else{
                return response()->json('error');
            } 
        }              
    }

}
