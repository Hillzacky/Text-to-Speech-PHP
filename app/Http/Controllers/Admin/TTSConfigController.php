<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\LicenseController;
use App\Models\Vendor;
use App\Models\Voice;
use DB;


class TTSConfigController extends Controller
{
    private $api;

    public function __construct()
    {
        $this->api = new LicenseController();
    }

    /**
     * Display TTS configuration settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        # Set Voice Types as Listed in TTS Config
        if (config('tts.voice_type') == 'standard') {
            $languages = DB::table('voices')
                ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
                ->join('languages', 'voices.language_code', '=', 'languages.language_code')
                ->where('voices.voice_type', 'standard')
                ->select('languages.id', 'languages.language', 'voices.language_code', 'languages.language_flag')                
                ->distinct()
                ->orderBy('languages.language', 'asc')
                ->get();

            $voices = DB::table('voices')
                ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
                ->where('voices.voice_type', 'standard')
                ->get();

        } elseif (config('tts.voice_type') == 'neural') {
            $languages = DB::table('voices')
                ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
                ->join('languages', 'voices.language_code', '=', 'languages.language_code')
                ->where('voices.voice_type', 'neural')
                ->select('languages.id', 'languages.language', 'voices.language_code', 'languages.language_flag')                
                ->distinct()
                ->orderBy('languages.language', 'asc')
                ->get();

            $voices = DB::table('voices')
                ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
                ->where('voices.voice_type', 'neural')
                ->get();

        } else {
            $languages = DB::table('voices')
                ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
                ->join('languages', 'voices.language_code', '=', 'languages.language_code')
                ->select('languages.id', 'languages.language', 'voices.language_code', 'languages.language_flag')                
                ->distinct()
                ->orderBy('languages.language', 'asc')
                ->get();

            $voices = DB::table('voices')
                ->join('vendors', 'voices.vendor_id', '=', 'vendors.vendor_id')
                ->get();
        }

        return view('admin.tts-management.configuration.index', compact('languages', 'voices'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        if ($this->api->api_url != 'https://license.berkine.space/') {
            return redirect()->back();
        }

        request()->validate([
            'set-voice-types' => 'required',
            'set-ssml-effects' => 'required',
            'set-max-chars' => 'required|integer|max:60000|min:0',
            'set-free-chars' => 'required|integer|max:60000|min:0',
            'set-max-voices' => 'required|integer|max:20|min:1',
            'set-max-voices-user' => 'required|integer|max:20|min:1',
            'set-storage-option' => 'required',
            'set-storage-clean' => 'required',
            'free-tier-limit' => 'required',
            'listen-download' => 'required',

            'enable-listen' => 'sometimes|required',
            'limit' => 'required_if:enable-listen,on',

            'enable-aws' => 'sometimes|required',
            'set-aws-access-key' => 'required_if:enable-aws,on',
            'set-aws-secret-access-key' => 'required_if:enable-aws,on',
            'set-aws-region' => 'required_if:enable-aws,on',
            'set-aws-bucket' => 'required_if:enable-aws,on',

            'enable-azure' => 'sometimes|required',
            'set-azure-key' => 'required_if:enable-azure,on',
            'set-azure-region' => 'required_if:enable-azure,on',

            'enable-gcp' => 'sometimes|required',
            'gcp-configuration-path' => 'required_if:enable-gcp,on',

            'enable-ibm' => 'sometimes|required',
            'ibm-api-key' => 'required_if:enable-ibm,on',
            'ibm-endpoint-url' => 'required_if:enable-ibm,on'
        ]);

        if (request('voice')) {
            $voice = Voice::where('voice_id', request('voice'))->firstOrFail();
            if ($voice->language_code != request('language')) {
                return redirect()->back()->with('error', 'Selected voice does not belong to this language, select correct one.');
            }
        }        

        $this->storeConfiguration('CONFIG_VOICE_TYPE', request('set-voice-types'));
        $this->storeConfiguration('CONFIG_SSML_EFFECT', request('set-ssml-effects'));
        $this->storeConfiguration('CONFIG_MAX_CHAR_LIMIT', request('set-max-chars'));
        $this->storeConfiguration('CONFIG_MAX_FREE_TIER_CHAR_LIMIT', request('free-tier-limit'));
        $this->storeConfiguration('CONFIG_MAX_FREE_CHARS', request('set-free-chars'));
        $this->storeConfiguration('CONFIG_MAX_VOICE_LIMIT', request('set-max-voices'));
        $this->storeConfiguration('CONFIG_MAX_VOICE_LIMIT_USER', request('set-max-voices-user'));
        $this->storeConfiguration('CONFIG_DEFAULT_STORAGE', request('set-storage-option'));
        $this->storeConfiguration('CONFIG_CLEAN_STORAGE', request('set-storage-clean'));
        $this->storeConfiguration('CONFIG_USER_NEURAL_VOICES', request('free-tier-neural'));
        $this->storeConfiguration('CONFIG_VENDOR_LOGOS', request('vendor-logo'));
        $this->storeConfiguration('CONFIG_DEFAULT_LANGUAGE', request('language'));
        $this->storeConfiguration('CONFIG_DEFAULT_VOICE', request('voice'));
        $this->storeConfiguration('CONFIG_LISTEN_DOWNLOAD', request('listen-download'));

        $this->storeConfiguration('AWS_ACCESS_KEY_ID', request('set-aws-access-key'));
        $this->storeConfiguration('AWS_SECRET_ACCESS_KEY', request('set-aws-secret-access-key'));
        $this->storeConfiguration('AWS_DEFAULT_REGION', request('set-aws-region'));
        $this->storeConfiguration('AWS_BUCKET', request('set-aws-bucket'));

        $this->storeConfiguration('WASABI_ACCESS_KEY_ID', request('set-wasabi-access-key'));
        $this->storeConfiguration('WASABI_SECRET_ACCESS_KEY', request('set-wasabi-secret-access-key'));
        $this->storeConfiguration('WASABI_DEFAULT_REGION', request('set-wasabi-region'));
        $this->storeConfiguration('WASABI_BUCKET', request('set-wasabi-bucket'));

        $this->storeConfiguration('CONFIG_ENABLE_AWS', request('enable-aws'));
        $this->storeConfiguration('CONFIG_ENABLE_AWS_STANDARD', request('enable-aws-standard'));
        $this->storeConfiguration('CONFIG_ENABLE_AWS_NEURAL', request('enable-aws-neural'));
        $this->storeConfiguration('CONFIG_ENABLE_AZURE', request('enable-azure'));
        $this->storeConfiguration('CONFIG_ENABLE_AZURE_STANDARD', request('enable-azure-standard'));
        $this->storeConfiguration('CONFIG_ENABLE_AZURE_NEURAL', request('enable-azure-neural'));
        $this->storeConfiguration('CONFIG_ENABLE_GCP', request('enable-gcp'));
        $this->storeConfiguration('CONFIG_ENABLE_GCP_STANDARD', request('enable-gcp-standard'));
        $this->storeConfiguration('CONFIG_ENABLE_GCP_NEURAL', request('enable-gcp-neural'));
        $this->storeConfiguration('CONFIG_ENABLE_IBM', request('enable-ibm'));

        $this->storeConfiguration('CONFIG_FRONTEND_LIVE_SYNTHESIZE', request('enable-listen'));
        $this->storeConfiguration('CONFIG_FRONTEND_MAX_CHAR_LIMIT', request('limit'));
        $this->storeConfiguration('CONFIG_FRONTEND_NEURAL_VOICES', request('neural'));

        $this->storeConfiguration('AZURE_SUBSCRIPTION_KEY', request('set-azure-key'));
        $this->storeConfiguration('AZURE_DEFAULT_REGION', request('set-azure-region'));
        $this->storeConfiguration('GOOGLE_APPLICATION_CREDENTIALS', request('gcp-configuration-path'));
        $this->storeConfiguration('IBM_API_KEY', request('ibm-api-key'));
        $this->storeConfiguration('IBM_ENDPOINT_URL', request('ibm-endpoint-url'));      

        # Enable/Disable AWS Voices
        if (request('enable-aws') == 'on') {
            $aws_std = Vendor::where('vendor_id', 'aws_std')->first();
            $aws_std->enabled = 1;
            $aws_std->save();

            $aws_nrl = Vendor::where('vendor_id', 'aws_nrl')->first();
            $aws_nrl->enabled = 1;
            $aws_nrl->save();

        } else {
            $aws_std = Vendor::where('vendor_id', 'aws_std')->first();
            $aws_std->enabled = 0;
            $aws_std->save();

            $aws_nrl = Vendor::where('vendor_id', 'aws_nrl')->first();
            $aws_nrl->enabled = 0;
            $aws_nrl->save();
        }

        if (request('enable-aws-standard') == 'on') {
            DB::table('voices')->where('vendor_id', 'aws_std')->update(array('status' => 'active'));
    
        } else {
            DB::table('voices')->where('vendor_id', 'aws_std')->update(array('status' => 'deactive'));
        }

        if (request('enable-aws-neural') == 'on') {
            DB::table('voices')->where('vendor_id', 'aws_nrl')->update(array('status' => 'active'));
    
        } else {
            DB::table('voices')->where('vendor_id', 'aws_nrl')->update(array('status' => 'deactive'));
        }


        # Enable/Disable GCP Voices
        if (request('enable-gcp') == 'on') {
            $gcp_std = Vendor::where('vendor_id', 'gcp_std')->first();
            $gcp_std->enabled = 1;
            $gcp_std->save();

            $gcp_nrl = Vendor::where('vendor_id', 'gcp_nrl')->first();
            $gcp_nrl->enabled = 1;
            $gcp_nrl->save();

        } else {
            $gcp_std = Vendor::where('vendor_id', 'gcp_std')->first();
            $gcp_std->enabled = 0;
            $gcp_std->save();

            $gcp_nrl = Vendor::where('vendor_id', 'gcp_nrl')->first();
            $gcp_nrl->enabled = 0;
            $gcp_nrl->save();
        }

        if (request('enable-gcp-standard') == 'on') {
            DB::table('voices')->where('vendor_id', 'gcp_std')->update(array('status' => 'active'));
    
        } else {
            DB::table('voices')->where('vendor_id', 'gcp_std')->update(array('status' => 'deactive'));
        }

        if (request('enable-gcp-neural') == 'on') {
            DB::table('voices')->where('vendor_id', 'gcp_nrl')->update(array('status' => 'active'));
    
        } else {
            DB::table('voices')->where('vendor_id', 'gcp_nrl')->update(array('status' => 'deactive'));
        }


        # Enable/Disable Azure Voices
        if (request('enable-azure') == 'on') {
            $azure_std = Vendor::where('vendor_id', 'azure_std')->first();
            $azure_std->enabled = 1;
            $azure_std->save();

            $azure_nrl = Vendor::where('vendor_id', 'azure_nrl')->first();
            $azure_nrl->enabled = 1;
            $azure_nrl->save();

        } else {
            $azure_std = Vendor::where('vendor_id', 'azure_std')->first();
            $azure_std->enabled = 0;
            $azure_std->save();

            $azure_nrl = Vendor::where('vendor_id', 'azure_nrl')->first();
            $azure_nrl->enabled = 0;
            $azure_nrl->save();
        }

        if (request('enable-azure-standard') == 'on') {
            DB::table('voices')->where('vendor_id', 'azure_std')->update(array('status' => 'active'));
    
        } else {
            DB::table('voices')->where('vendor_id', 'azure_std')->update(array('status' => 'deactive'));
        }

        if (request('enable-azure-neural') == 'on') {
            DB::table('voices')->where('vendor_id', 'azure_nrl')->update(array('status' => 'active'));
    
        } else {
            DB::table('voices')->where('vendor_id', 'azure_nrl')->update(array('status' => 'deactive'));
        }
        

        # Enable/Disable IBM Voices
        if (request('enable-ibm') == 'on') {
            $ibm_nrl = Vendor::where('vendor_id', 'ibm_nrl')->first();
            $ibm_nrl->enabled = 1;
            $ibm_nrl->save();

        } else {
            $ibm_nrl = Vendor::where('vendor_id', 'ibm_nrl')->first();
            $ibm_nrl->enabled = 0;
            $ibm_nrl->save();
        }

        return redirect()->back()->with('success', 'Settings were successfully updated');       
    }


    /**
     * Record in .env file
     */
    private function storeConfiguration($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {

            file_put_contents($path, str_replace(
                $key . '=' . env($key), $key . '=' . $value, file_get_contents($path)
            ));

        }
    }
}
