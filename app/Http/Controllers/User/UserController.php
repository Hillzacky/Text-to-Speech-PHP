<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use App\Services\Statistics\UserUsageYearlyService;
use App\Services\Statistics\UserUsageMonthlyService;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\Project;
use App\Models\Voice;
use App\Models\Language;
use App\Models\User;
use DB;


class UserController extends Controller
{
    use Notifiable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {                         
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));

        $usage_yearly = new UserUsageYearlyService($year);
        $usage_monthly = new UserUsageMonthlyService($month, $year);

        $user_data_year = [
            'total_standard_chars' => $usage_yearly->getTotalStandardCharsUsage(),
            'total_neural_chars' => $usage_yearly->getTotalNeuralCharsUsage(),
            'total_audio_files' => $usage_yearly->getTotalAudioFiles(),
            'total_listen_mode' => $usage_yearly->getTotalListenModes(),
        ];

        $user_data_month = [
            'total_standard_chars' => $usage_monthly->getTotalStandardCharsUsage(),
            'total_neural_chars' => $usage_monthly->getTotalNeuralCharsUsage(),
            'total_audio_files' => $usage_monthly->getTotalAudioFiles()
        ];
        
        $chart_data['standard_chars'] = json_encode($usage_yearly->getStandardCharsUsage());
        $chart_data['neural_chars'] = json_encode($usage_yearly->getNeuralCharsUsage());

        if (auth()->user()->hasActiveSubscription()) {
            $subscription = Subscription::where('user_id', auth()->user()->id)->where('status', 'Active')->first();
        } else {
            $subscription = false;
        }

        $voice = (auth()->user()->voice) ? Voice::where('voice_id', auth()->user()->voice)->first() : '';
        $language = (auth()->user()->language) ? Language::where('language_code', auth()->user()->language)->first() : '';
        $user_subscription = ($subscription) ? Plan::where('id', auth()->user()->plan_id)->first() : '';        

        return view('user.profile.index', compact('chart_data', 'user_data_year', 'user_data_month', 'user_subscription', 'voice', 'language'));           
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id = null)
    {   
        # Set Voice Types as Listed in TTS Config
        if (config('tts.voice_type') == 'standard') {
            $languages = DB::table('voices')
                ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
                ->join('languages', 'voices.language_code', '=', 'languages.language_code')
                ->where('vendors.enabled', '1')
                ->where('voices.status', 'active')
                ->where('voices.voice_type', 'standard')
                ->select('languages.id', 'languages.language', 'voices.language_code', 'languages.language_flag')                
                ->distinct()
                ->orderBy('languages.language', 'asc')
                ->get();

            $voices = DB::table('voices')
                ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
                ->where('vendors.enabled', '1')
                ->where('voices.voice_type', 'standard')
                ->where('voices.status', 'active')
                ->orderBy('voices.vendor', 'asc')
                ->get();

        } elseif (config('tts.voice_type') == 'neural') {
            $languages = DB::table('voices')
                ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
                ->join('languages', 'voices.language_code', '=', 'languages.language_code')
                ->where('vendors.enabled', '1')
                ->where('voices.status', 'active')
                ->where('voices.voice_type', 'neural')
                ->select('languages.id', 'languages.language', 'voices.language_code', 'languages.language_flag')                
                ->distinct()
                ->orderBy('languages.language', 'asc')
                ->get();

            $voices = DB::table('voices')
                ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
                ->where('vendors.enabled', '1')
                ->where('voices.voice_type', 'neural')
                ->where('voices.status', 'active')
                ->orderBy('voices.vendor', 'asc')
                ->get();

        } else {
            $languages = DB::table('voices')
                ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
                ->join('languages', 'voices.language_code', '=', 'languages.language_code')
                ->where('vendors.enabled', '1')
                ->where('voices.status', 'active')
                ->select('languages.id', 'languages.language', 'voices.language_code', 'languages.language_flag')                
                ->distinct()
                ->orderBy('languages.language', 'asc')
                ->get();

            $voices = DB::table('voices')
                ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
                ->where('vendors.enabled', '1')
                ->where('voices.status', 'active')
                ->orderBy('voices.vendor', 'asc')
                ->get();
        }

        $projects = Project::where('user_id', auth()->user()->id)->orderBy('name', 'asc')->get();

        return view('user.profile.edit', compact('languages', 'voices', 'projects'));
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(User $user)
    {           
        $user->update(request()->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','string','email','max:255',Rule::unique('users')->ignore($user)],
            'job_role' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'phone_number' => 'nullable|max:20',
            'address' => 'nullable|string|max:255',            
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'voice' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:255',
            'project' => 'nullable|string|max:255',
        ]));
        
        if (request()->has('profile_photo')) {
        
            try {
                request()->validate([
                    'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048'
                ]);
                
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'PHP FileInfo: ' . $e->getMessage());
            }
            
            $image = request()->file('profile_photo');

            $name = Str::random(20);
         
            $folder = '/uploads/img/users/';
          
            $filePath = $folder . $name . '.' . $image->getClientOriginalExtension();
            
            $this->uploadImage($image, $folder, 'public', $name);

            $user->profile_photo_path = $filePath;

            $user->save();
        }

        return redirect()->route('user.profile.edit', compact('user'))->with('success','Profile Successfully Updated');

    }


    /**
     * Upload user profile image
     */
    public function uploadImage(UploadedFile $file, $folder = null, $disk = 'public', $filename = null)
    {
        $name = !is_null($filename) ? $filename : Str::random(25);

        $image = $file->storeAs($folder, $name .'.'. $file->getClientOriginalExtension(), $disk);

        return $image;
    }


}
