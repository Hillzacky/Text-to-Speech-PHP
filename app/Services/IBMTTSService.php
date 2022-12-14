<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\Statistics\UserService;
use App\Services\MergeService;
use App\Models\Voice;

class IBMTTSService 
{

    private $apiKey;
    private $watsonUrl;
    private $api;
    private $convert;
    

    public function __construct()
    {
        $this->api = new UserService();

        $verify = $this->api->verify_license();

        if($verify['status']!=true){
            return false;
        }

        $this->apiKey = config('services.ibm.api_key');         
        $this->watsonUrl = config('services.ibm.endpoint_url');   
        $this->convert = new MergeService();     
    }


    /**
     * Synthesize text via IBM text to speech feature
     *
     * 
     */
    public function synthesizeSpeech(Voice $voice, $text, $format, $file_name)
    {  
        if ($this->api->api_url != 'https://license.berkine.space/') {
            return;
        }

        $languageAndVoice = $voice->voice_id;
        $ssml_text = '<speak>' . $text . '</speak>';
        $textJson = json_encode(['text' => $ssml_text]);
        
        $watsonEndpoint = $this->watsonUrl . '/v1/synthesize?voice=' . $languageAndVoice;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $watsonEndpoint);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERPWD, 'apikey:' . $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: audio/' . $format,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $textJson);

        $audio_stream = curl_exec($ch);      
        
        if (curl_errno($ch)) {
            return response()->json(["error" => "IBM Synthesize Error. Please notify support team."], 422);
            Log::error(curl_error($ch) . ' ' . $audio_stream);
        }

        curl_close($ch);

        Storage::disk('audio')->put($file_name, $audio_stream); 

        $data['result_url'] = Storage::url($file_name); 
        $data['name'] = $file_name;
        
        return $data;
    }
}