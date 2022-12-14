<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\Statistics\UserService;
use App\Models\Voice;
use Aws\Polly\PollyClient;  
use Aws\Exception\AwsException;
use Aws\Credentials\Credentials;

class AWSTTSService 
{

    private $client;
    private $api;

    /**
     * Create Amazon Polly Client
     *
     * 
     */
    public function __construct()
    {   
        $this->api = new UserService();

        $verify = $this->api->verify_license();

        if($verify['status']!=true){
            return false;
        }

        try {

            $credentials = new Credentials(config('services.aws.key'), config('services.aws.secret'));

            $this->client = new PollyClient([
                'region'      => config('services.aws.region'),
                'version'     => 'latest',
                'credentials' => $credentials
            ]);

        } catch (AwsException $e) {
            return response()->json(["exception" => "Credentials are incorrect. Please notify support team."], 422);
            Log::error($e->getMessage());
        }

    }
    

    /**
     * Synthesize text less than 3000 billable characters
     *
     * @return result link (local or S3)
     */
    public function synthesizeSpeech(Voice $voice, $text, $format, $file_name)
    {
        if ($this->api->api_url != 'https://license.berkine.space/') {
            return;
        }

        if ($format == 'ogg') {
            $format = 'ogg_vorbis';
        }
        
        try {           

            $language = ($voice->voice_id == 'ar-aws-std-zeina') ? 'arb' : $voice->language_code;

            $text = preg_replace("/\&/", "&amp;", $text);
            $text = preg_replace("/(^|(?<=\s))<((?=\s)|$)/i", "&lt;", $text);
            $text = preg_replace("/(^|(?<=\s))>((?=\s)|$)/i", "&gt;", $text);  
            
            $ssml_text = "<speak>" . $text . "</speak>";

            # Create synthesize job
            $polly_result = $this->client->synthesizeSpeech([
                'Engine' => $voice->voice_type,
                'LanguageCode' => $language,
                'Text' => $ssml_text,
                'TextType' => 'ssml',
                'OutputFormat' => $format,		
                'VoiceId' => $voice->voice,					
            ]);


            $audio_stream = $polly_result->get('AudioStream')->getContents();

            Storage::disk('audio')->put($file_name, $audio_stream); 

            $data['result_url'] = Storage::url($file_name); 
            $data['name'] = $file_name;
            
            return $data;

        } catch (AwsException $e) {            
            return response()->json(["error" => "AWS Start Synthesize Speech Task Error. Please try again or send a support request."], 422);
            Log::error("AWS Start Synthesize Speech Task Error. " . $e->getMessage());
        }        
    }

}