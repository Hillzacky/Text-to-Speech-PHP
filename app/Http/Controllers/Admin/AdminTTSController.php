<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Services\Statistics\TTSService;
use App\Services\Statistics\CostsService;
use App\Models\Result;
use App\Models\Voice;
use App\Models\User;
use DataTables;

class AdminTTSController extends Controller
{
    /**
     * Display TTS dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));

        $tts = new TTSService($year, $month);

        $tts_data_yearly = [
            'total_free_chars' => $tts->getTotalFreeCharsUsageYearly(),
            'total_paid_chars' => $tts->getTotalPaidCharsUsageYearly(),
            'total_standard_chars' => $tts->getTotalStandardCharsUsageYearly(),
            'total_neural_chars' => $tts->getTotalNeuralCharsUsageYearly(),
            'total_audio_files' => $tts->getTotalAudioFilesYearly(),
            'total_listen_results' => $tts->getTotalListenResultsYearly(),
        ];

        $tts_data_monthly = [
            'free_chars' => $tts->getTotalFreeCharsUsageMonthly(),
            'paid_chars' => $tts->getTotalPaidCharsUsageMonthly(),
            'standard_chars' => $tts->getTotalStandardCharsUsageMonthly(),
            'neural_chars' => $tts->getTotalNeuralCharsUsageMonthly()
        ];

        $vendor_data = [
            'aws_month' => $tts->getAWSUsageMonthly(),
            'aws_year' => $tts->getAWSUsageYearly(),
            'azure_month' => $tts->getAzureUsageMonthly(),
            'azure_year' => $tts->getAzureUsageYearly(),
            'gcp_month' => $tts->getGCPUsageMonthly(),
            'gcp_year' => $tts->getGCPUsageYearly(),
            'ibm_month' => $tts->getIBMUsageMonthly(),
            'ibm_year' => $tts->getIBMUsageYearly()
        ];
        
        $chart_data['free_chars'] = json_encode($tts->getFreeCharsUsageYearly());
        $chart_data['paid_chars'] = json_encode($tts->getPaidCharsUsageYearly());

        $percentage['aws_year'] = json_encode($tts->getAWSUsageYearly());
        $percentage['azure_year'] = json_encode($tts->getAzureUsageYearly());
        $percentage['gcp_year'] = json_encode($tts->getGCPUsageYearly());
        $percentage['ibm_year'] = json_encode($tts->getIBMUsageYearly());
        $percentage['free_current'] = json_encode($tts->getTotalFreeCharsUsageMonthly());
        $percentage['free_past'] = json_encode($tts->getTotalFreeCharsUsagePastMonth());
        $percentage['paid_current'] = json_encode($tts->getTotalPaidCharsUsageMonthly());
        $percentage['paid_past'] = json_encode($tts->getTotalPaidCharsUsagePastMonth());
        $percentage['standard_current'] = json_encode($tts->getTotalStandardCharsUsageMonthly());
        $percentage['standard_past'] = json_encode($tts->getTotalStandardCharsUsagePastMonth());
        $percentage['neural_current'] = json_encode($tts->getTotalNeuralCharsUsageMonthly());
        $percentage['neural_past'] = json_encode($tts->getTotalNeuralCharsUsagePastMonth());

        return view('admin.tts-management.dashboard.index', compact('chart_data', 'percentage', 'tts_data_yearly', 'tts_data_monthly', 'vendor_data'));
    }


    /**
     * List all tts synthesize results
     */
    public function listResults(Request $request)
    {
        if ($request->ajax()) {
            $data = Result::all()->where('mode', 'file')->sortByDesc("created_at");
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div>
                                        <a href="'. route("admin.tts.result.show", $row["id"] ). '"><i class="fa-solid fa-list-music voice-action-buttons rename-voice" title="View Result"></i></a>
                                        <a class="deleteResultButton" id="'. $row["id"] .'" href="#"><i class="fa-solid fa-trash-xmark voice-action-buttons deactivate-voice" title="Delete Result"></i></a>
                                    </div>';
                        return $actionBtn;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-plan-type', function($row){
                        $custom_plan = '<span class="cell-box plan-'.strtolower($row["plan_type"]).'">'.ucfirst($row["plan_type"]).'</span>';
                        return $custom_plan;
                    })
                    ->addColumn('custom-voice-type', function($row){
                        $custom_voice = '<span class="cell-box voice-'.strtolower($row["voice_type"]).'">'.ucfirst($row["voice_type"]).'</span>';
                        return $custom_voice;
                    })
                    ->addColumn('username', function($row){
                        if ($row["user_id"]) {
                            $username = '<span>'.User::find($row["user_id"])->name.'</span>';
                            return $username;
                        } else {
                            return $row["user_id"];
                        }
                       
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
                    ->addColumn('vendor', function($row){
                        $path = URL::asset($row['vendor_img']);
                        $vendor = '<div class="vendor-image-sm overflow-hidden"><img alt="vendor" class="rounded-circle" src="' . $path . '"></div>';
                        return $vendor;
                    })
                    ->addColumn('custom-language', function($row) {
                        $language = '<span class="vendor-image-sm overflow-hidden"><img class="mr-2" src="' . URL::asset($row['language_flag']) . '">'. $row['language'] .'</span> ';            
                        return $language;
                    })
                    ->rawColumns(['actions', 'custom-plan-type', 'created-on', 'username', 'custom-voice-type', 'result', 'vendor', 'download', 'single', 'custom-language'])
                    ->make(true);
                    
        }

        return view('admin.tts-management.results.index');
    }


    /**
     * Display selected result details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Result $id)
    {   
        $name = User::find($id->user_id)->name;
        $email = User::find($id->user_id)->email;

        $cost = new CostsService();

        $json_data['cost'] = json_encode($cost->getCostPerText($id->id));

        return view('admin.tts-management.results.show', compact('id', 'email', 'json_data'));
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

            $result->delete();

            return response()->json('success');    
        }     
    }


    /**
     * List all tts synthesize results
     */
    public function voices(Request $request)
    {
        if ($request->ajax()) {
            $data = Voice::select('voices.*', 'languages.language')->join('languages', 'voices.language_code', '=', 'languages.language_code')->orderBy('languages.language_code', 'DESC')->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div>        
                                        <a class="changeVoiceNameButton" id="' . $row["id"] . '" href="#"><i class="fa fa-edit voice-action-buttons rename-voice" title="Rename Voice"></i></a>      
                                        <a class="changeAvatarButton" id="' . $row["id"] . '" href="#"><i class="fa-solid fa-user-astronaut voice-action-buttons rename-voice" title="Change Avatar"></i></a>
                                        <a class="activateVoiceButton" id="' . $row["id"] . '" href="#"><i class="fa fa-check voice-action-buttons activate-voice" title="Activate Voice"></i></a>
                                        <a class="deactivateVoiceButton" id="' . $row["id"] . '" href="#"><i class="voice-action-buttons fa fa-close deactivate-voice" title="Deactivate Voice"></i></a>  
                                    </div>';
                        return $actionBtn;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["updated_at"], 'Y-m-d H:i:s').'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-voice-type', function($row){
                        $custom_voice = '<span class="cell-box voice-'.strtolower($row["voice_type"]).'">'.ucfirst($row["voice_type"]).'</span>';
                        return $custom_voice;
                    })
                    ->addColumn('custom-status', function($row){
                        $custom_voice = '<span class="cell-box status-'.strtolower($row["status"]).'">'.ucfirst($row["status"]).'</span>';
                        return $custom_voice;
                    })
                    ->addColumn('single', function($row){
                        $url = ($row['storage'] == 'local') ? URL::asset($row['sample_url']) : $row['sample_url'];
                        $result = '<button type="button" class="result-play" onclick="resultPlay(this)" src="' . $url . '" type="'. $row['audio_type'].'" id="'. $row['id'] .'"><i class="fa fa-play"></i></button>';
                        return $result;
                    })
                    ->addColumn('vendor', function($row){
                        $path = URL::asset($row['vendor_img']);
                        $vendor = '<div class="vendor-image-sm overflow-hidden"><img alt="vendor" class="rounded-circle" src="' . $path . '"></div>';
                        return $vendor;
                    })
                    ->addColumn('avatar', function($row){
                        if ($row['avatar_url']) {
                            $path = URL::asset($row['avatar_url']);
                        } else {
                            $path = URL::asset('img/users/avatar.jpg');
                        }

                        $avatar = '<div class="widget-user-image-sm overflow-hidden"><img alt="Voice Avatar" class="rounded-circle" src="' . $path . '"></div>';
                        return $avatar;
                    })
                    ->rawColumns(['actions', 'created-on', 'custom-voice-type', 'vendor', 'single', 'custom-status', 'avatar'])
                    ->make(true);
                    
        }

        return view('admin.tts-management.voices.index');
    }


    public function changeAvatar(Request $request) {

        if (request()->has('avatar')) {
        
            try {
                request()->validate([
                    'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:1048'
                ]);
                
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'PHP FileInfo: ' . $e->getMessage());
            }
            
            $image = request()->file('avatar');

            $name = Str::random(10);

            $voice = Voice::find(request('id'));
         
            switch ($voice->vendor) {
                case 'aws':
                    $folder = 'voices/aws/avatars/';
                    break;
                case 'azure':
                    $folder = 'voices/azure/avatars/';
                    break;
                case 'gcp':
                    $folder = 'voices/gcp/avatars/';
                    break;
                case 'ibm':
                    $folder = 'voices/ibm/avatars/';
                    break;
                default:
                    $folder = 'voices/vatars/';
                    break;
            }
          
            $filePath = $folder . $name . '.' . $image->getClientOriginalExtension();
            
            $this->uploadImage($image, $folder, 'public', $name);
            
            $voice->avatar_url = $filePath;
            $voice->save();

            return  response()->json('success');
        }
    }


    /**
     * Update the specified voice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function voiceUpdate(Request $request)
    {   
        if ($request->ajax()) {

            $voice = Voice::where('id', request('id'))->firstOrFail(); 
            
            if ($voice->vendor != 'aws') {
                $voice->update(['voice' => request('name')]);
                return  response()->json('success');
            } else {
                return response()->json(["error" => "AWS Voices cannot be renamed. It is a vendor limitation as AWS relies on initial voice names upon text synthesizing."], 422);
            }

            

            
        }  
    }


    /**
     * Enable the specified voice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function voiceActivate(Request $request)
    {
        if ($request->ajax()) {

            $voice = Voice::where('id', request('id'))->firstOrFail();  

            if ($voice->status == 'active') {
                return  response()->json('active');
            }

            $voice->update(['status' => 'active']);

            return  response()->json('success');
        }
    }


    /**
     * Enable all voices.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function voicesActivateAll(Request $request)
    {
        if ($request->ajax()) {

            Voice::query()->update(['status' => 'active']);

            return  response()->json('success');
        }          
    }


    /**
     * Disable the specified voice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function voiceDeactivate(Request $request)
    {
        if ($request->ajax()) {

            $voice = Voice::where('id', request('id'))->firstOrFail();  

            if ($voice->status == 'deactive') {
                return  response()->json('deactive');
            }

            $voice->update(['status' => 'deactive']);

            return  response()->json('success');
        }    
    }


    /**
     * Disable all voices.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function voicesDeactivateAll(Request $request)
    {
        if ($request->ajax()) {

            Voice::query()->update(['status' => 'deactive']);

            return  response()->json('success');
        }     
    }


    /**
     * Upload voice avatar image
     */
    public function uploadImage(UploadedFile $file, $folder = null, $disk = 'public', $filename = null)
    {
        $name = !is_null($filename) ? $filename : Str::random(25);

        $image = $file->storeAs($folder, $name .'.'. $file->getClientOriginalExtension(), $disk);

        return $image;
    }
}
