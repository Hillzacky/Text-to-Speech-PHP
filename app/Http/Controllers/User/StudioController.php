<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\MergeService;
use App\Models\Result;
use App\Models\Music;
use App\Models\Studio;
use Yajra\DataTables\DataTables;

class StudioController extends Controller
{
    private $api;
    private $merge_files;

    public function __construct()
    {
        $this->api = new LicenseController();
        $this->merge_files = new MergeService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        # Today's TTS Results for Datatable
        if ($request->ajax()) {
            $data = Result::where('mode', 'file')->where('user_id', Auth::user()->id)->latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div>
                                            <a href="'. route("user.studio.show", $row["id"] ). '"><i class="fa-solid fa-list-music voice-action-buttons rename-voice" title="View Result"></i></a>
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
                    ->addColumn('custom-extension', function($row) {
                        $language = '<span class="font-weight-bold">' . $row['result_ext'] .'</span> ';            
                        return $language;
                    })
                    ->rawColumns(['actions', 'created-on', 'custom-voice-type', 'result', 'download', 'single', 'custom-language', 'custom-extension'])
                    ->make(true);
                    
        }

        $row_limit = config('tts.max_merge_files');

        $musics = Music::where('user_id', auth()->user()->id)->orWhere('public', true)->latest()->get();

        $js['row_limit'] = json_encode($row_limit);

        return view('user.studio.index', compact('musics', 'row_limit', 'js'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function results(Request $request)
    {
        # Today's TTS Results for Datatable
        if ($request->ajax()) {
            $data = Studio::where('user_id', Auth::user()->id)->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div>
                                            <a href="'. route("user.studio.show.studio", $row["id"] ). '"><i class="fa-solid fa-list-music voice-action-buttons rename-voice" title="View Result"></i></a>
                                            <a class="deleteStudioResultButton" id="'. $row["id"] .'" href="#"><i class="fa-solid fa-trash-xmark voice-action-buttons deactivate-voice" title="Delete Result"></i></a> 
                                        </div>';
                        return $actionBtn;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                        return $created_on;
                    })
                    ->addColumn('download', function($row){
                        $url = ($row['storage'] == 'local') ? URL::asset($row['url']) : $row['url'];
                        $result = '<a class="" href="' . $url . '" download><i class="fa fa-cloud-download result-download fs-20"></i></a>';
                        return $result;
                    })
                    ->addColumn('single', function($row){
                        $url = ($row['storage'] == 'local') ? URL::asset($row['url']) : $row['url'];
                        $result = '<button type="button" class="result-play special-padding" onclick="resultPlay(this)" src="' . $url . '" id="'. $row['id'] .'-studio"><i class="fa fa-play"></i></button>';
                        return $result;
                    })
                    ->addColumn('custom-extension', function($row) {
                        $language = '<span class="font-weight-bold">' . $row['format'] .'</span> ';            
                        return $language;
                    })
                    ->rawColumns(['actions', 'created-on', 'download', 'single', 'custom-extension'])
                    ->make(true);
                    
        }

    }


    /**
     * List all uploaded background audio files.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request) 
    {
        # Today's TTS Results for Datatable
        if ($request->ajax()) {
            $data = Music::where('user_id', Auth::user()->id)->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div>                                       
                                        <a class="deleteResultButton" id="'. $row["id"] .'" href="#"><i class="fa-solid fa-trash-xmark voice-action-buttons deactivate-voice" title="Delete Result"></i></a> 
                                    </div>';
                        return $actionBtn;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-size', function($row){
                        $size = $this->formatBytes((int)$row['size']);
                        return $size;
                    })
                    ->addColumn('download', function($row){
                        $url = URL::asset($row['url']);
                        $result = '<a class="" href="' . $url . '" download><i class="fa fa-cloud-download result-download fs-20"></i></a>';
                        return $result;
                    })
                    ->addColumn('play', function($row){
                        switch ($row['type']) {
                            case 'mp3':
                                $type = 'audio/mpeg';
                                break;
                            case 'wav':
                                $type = 'audio/wav';
                                break;
                            case 'ogg':
                                $type = 'audio/ogg';
                                break;
                            default:
                                # code...
                                break;
                        }
                        $url = URL::asset($row['url']);
                        $result = '<button type="button" class="result-play" onclick="resultPlay(this)" src="' . $url . '" type="'. $type.'" id="'. $row['id'] .'"><i class="fa fa-play"></i></button>';
                        return $result;
                    })
                    ->rawColumns(['actions', 'created-on', 'download', 'play', 'custom-size'])
                    ->make(true);
                    
        }


        return view('user.studio.list');
    }


    /**
     * Merge selected audio files.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function merge(Request $request)
    {
        $verify = $this->api->verify_license();

        if($verify['status']!=true){
            return false;
        }

        if ($request->ajax()) {

            $rows = explode(',', request('rows'));
            $files = count($rows);
            $inputAudioFiles = [];
            $inputAudioFilesDelete = [];
            $total_characters = 0;
            $total_text = '';
            $total_text_raw = '';
            $stored_in_cloud = false;
            $change_file_volume = false;
            $change_bg_volume = false;
            
            if (request('format') == 'mp3') {
                $file_name = 'studio-' . Str::random(10) . '.mp3';
            } elseif (request('format') == 'wav') {
                $file_name = 'studio-' . Str::random(10) . '.wav';
            } elseif (request('format') == 'ogg') {
                $file_name = 'studio-' . Str::random(10) . '.ogg';
            }
            
            $mergedResultURL = 'storage/' . $file_name;

            # Get all result information for processing
            foreach ($rows as $value) {

                $result = Result::where('id', $value)->get()->toArray();

                # Handle locally stored results
                if ($result[0]['storage'] == 'local') {
                    array_push($inputAudioFiles, 'storage/' . $result[0]['file_name']);
                } else {
                    # Handle results stored in S3 and Wasabi buckets
                    $name = 'temp-' . $result[0]['file_name'];
                    $file = file_get_contents($result[0]['result_url']);
                    Storage::disk('audio')->put($name, $file);
                    array_push($inputAudioFiles, 'storage/' . $name);
                    array_push($inputAudioFilesDelete, $name);
                    $stored_in_cloud = true;
                }

                $total_characters += $result[0]['characters'];
                $total_text .= $result[0]['text'] . ' ';
                $total_text_raw .= $result[0]['text_raw'] . ' ';

            }

            # Process merging and adding background audio
            if (request('background_audio') != 'none' && $files == 1) {

                $audio = Music::where('id', request('background_audio'))->first();
                $bg_audio = ltrim($audio['url'], $audio['url'][0]);

                $result = Result::where('id', $rows[0])->get()->toArray();

                if ($result[0]['storage'] == 'local') {
                    $input_file = $result[0]['file_name'];
                } else {
                    $input_file = 'temp-' . $result[0]['file_name'];
                    $file = file_get_contents($result[0]['result_url']);
                    Storage::disk('audio')->put($input_file, $file);
                    $stored_in_cloud = true;
                }
                
                # Process volume adjustment for audio file
                if (request('audio_volume') != '1.0') {
                    $this->merge_files->adjust_volume('storage/' . $input_file, 'storage/temp-' . $file_name, request('audio_volume'));
                    $input_file = 'temp-' . $file_name;    

                    $change_file_volume = true;
                }

                # Process volume adjustment for background audio file
                if (request('background_volume') != '1.0') {
                    $bg_temp = 'bg-temp-' . Str::random(5) . '.' . $audio['type'];
                    $this->merge_files->adjust_volume($bg_audio, 'storage/' . $bg_temp, request('background_volume'));
                    $bg_audio = 'storage/' . $bg_temp;
                    $change_bg_volume = true;
                }
            
                $this->merge_files->merge_background($bg_audio, 'storage/' . $input_file, $mergedResultURL);

                # Remove temporary files
                if ($change_file_volume) {
                    Storage::disk('audio')->delete($input_file);
                }

                if ($change_bg_volume) {
                    $bg_audio_temp = explode('/', $bg_audio);
                    $bg_audio = end($bg_audio_temp);
                    Storage::disk('audio')->delete($bg_audio);
                }   
                
                if ($stored_in_cloud) {
                    Storage::disk('audio')->delete($input_file);
                }

            } elseif (request('background_audio') == 'none') {

                $this->merge_files->merge(request('format'), $inputAudioFiles, $mergedResultURL, true);

                if (request('audio_volume') != '1.0') {
                    $this->merge_files->adjust_volume($mergedResultURL, 'storage/temp-' . $file_name, request('audio_volume'));

                    # Remove temporary files
                    Storage::disk('audio')->delete($file_name);
                    Storage::disk('audio')->copy('temp-' . $file_name, $file_name);
                    Storage::disk('audio')->delete('temp-' . $file_name);
                }

                # Remove temporary files from cloud vendors
                if ($stored_in_cloud) {
                    foreach ($inputAudioFilesDelete as $value) {
                        Storage::disk('audio')->delete($value);
                    }
                }
            
            } elseif (request('background_audio') != 'none' && $files > 1) {

                $merge = $this->merge_files->merge(request('format'), $inputAudioFiles, $mergedResultURL, true);

                if ($merge) {

                    $audio = Music::where('id', request('background_audio'))->first();
                    $bg_audio = ltrim($audio['url'], $audio['url'][0]);
                    $input_file = $file_name;

                    # Process volume adjustment for audio file
                    if (request('audio_volume') != '1.0') {
                        $this->merge_files->adjust_volume('storage/' . $file_name, 'storage/temp-' . $file_name, request('audio_volume'));
                        $input_file = 'temp-' . $file_name;    
                        $change_file_volume = true;
                    }
                    
                    # Process volume adjustment for background audio file
                    if (request('background_volume') != '1.0') {
                        $bg_temp = 'bg-temp-' . Str::random(5) . '.' . $audio['type'];
                        $this->merge_files->adjust_volume($bg_audio, 'storage/' . $bg_temp, request('background_volume'));
                        $bg_audio = 'storage/' . $bg_temp;
                        $change_bg_volume = true;
                    }

                    $this->merge_files->merge_background($bg_audio, 'storage/' . $input_file, 'storage/final-temp-' . $file_name);

                    if ($change_file_volume) {
                        Storage::disk('audio')->delete($file_name);
                    }
    
                    if ($change_bg_volume) {
                        $bg_audio_temp = explode('/', $bg_audio);
                        $bg_audio = end($bg_audio_temp);
                        Storage::disk('audio')->delete($bg_audio);
                    } 

                    # Remove temporary files
                    Storage::disk('audio')->delete($input_file);
                    Storage::disk('audio')->copy('final-temp-' . $file_name, $file_name);
                    Storage::disk('audio')->delete('final-temp-' . $file_name);

                    # Remove temporary files from cloud vendors
                    if ($stored_in_cloud) {
                        foreach ($inputAudioFilesDelete as $value) {
                            Storage::disk('audio')->delete($value);
                        }
                    }
                }
            } 
            

            $result_url = Storage::url($file_name);

            $result = new Studio([
                'user_id' => Auth::user()->id,
                'title' => request('title'),
                'format' => request('format'),
                'characters' => $total_characters,
                'files' => $files,
                'url' => $result_url,
                'storage' => config('tts.default_storage'),
                'text' => $total_text,
                'text_raw' => $total_text_raw,
            ]); 

            $result->save();

            return response()->json('success');   
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showStudio(Studio $id)
    {
        if ($id->user_id == Auth::user()->id){

            return view('user.studio.show-studio', compact('id'));     

        } else{
            return redirect()->route('user.studio');
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

            return view('user.studio.show', compact('id'));     

        } else{
            return redirect()->route('user.studio');
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


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteStudioResult(Request $request)
    {
        if ($request->ajax()) {

            $result = Studio::where('id', request('id'))->firstOrFail();  

            if ($result->user_id == Auth::user()->id){

                $result->delete();

                return response()->json('success');    
    
            } else{
                return response()->json('error');
            } 
        }              
    }


    public function settings(Request $request)
    {
        if ($request->ajax()) {
            $data['size'] = config('tts.max_background_audio_size');
            return $data;
        }
    }

    
    /**
     * Upload background audio file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $status = false;

        if (request()->hasFile('audiofile')) {
                
            $file = request()->file('audiofile');
            $extension = $file->getClientOriginalExtension();
            $name = $file->getClientOriginalName();
            $size = $file->getSize();

            $audio_length = gmdate("H:i:s", request('audiolength'));

            $folder = '/uploads/music/';
            $file_name = Str::random(10) . '.' . $extension;
            $url = $folder . $file_name;

            $file->storeAs($folder, $file_name, 'public');

            $result = new Music([
                'user_id' => Auth::user()->id,
                'url' => $url,
                'type' => $extension,
                'size' => $size,
                'duration' => $audio_length,
                'name' => $name,
            ]); 

            $result->save();

            $status = true;
        }

        if ($request->ajax()) {
            $data = ($status) ? true : false;
            return $data;
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteMusic(Request $request)
    {
        if ($request->ajax()) {

            $result = Music::where('id', request('id'))->firstOrFail(); 

            if ($result->user_id == Auth::user()->id){

                Storage::disk('public')->delete($result->url);

                $result->delete();

                return response()->json('success');    
    
            } else{
                return response()->json('error');
            } 
        }               
    }


    private function formatBytes($bytes, $precision = 2) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
        
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow]; 
    }
}
