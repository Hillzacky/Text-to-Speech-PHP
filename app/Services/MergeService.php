<?php

namespace App\Services;

use App\Services\Statistics\UserService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;
use Psy\Shell;

class MergeService {

    private $ffmpeg;

    /**
     * Verify license
     *
     */
    public function __construct()
    {
        $this->api = new UserService();

        $verify = $this->api->verify_license();

        if($verify['status']!=true){
            return false;
        }

        $this->ffmpeg = base_path('vendor/ffmpeg') . '/ffmpeg';
    }


    /**
     * Merge multiple audio files together
     *
     */
    public function merge($format, $inputAudioFiles, $mergedResultURL)
    {
        try {

            if ($format == 'mp3') {
                
                shell_exec($this->check_os('ffmpeg') . ' -i "concat:' . implode('|', $inputAudioFiles) . '" -codec:a copy ' . $mergedResultURL);

            } elseif ($format == 'ogg') {
                $inputLength = count($inputAudioFiles);
                $inputFiles = '';
                $position = '';
                foreach ($inputAudioFiles as $key => $value) {
                    $inputFiles .= ' -i ' . $value;
                    $position .= '[' . $key . ':0]';
                }

                Shell_exec($this->check_os('ffmpeg') . $inputFiles . ' -filter_complex "' . $position . 'concat=n=' . $inputLength . ':v=0:a=1[out]" -map "[out]" -codec:a libopus ' . $mergedResultURL);
            
            } elseif ($format == 'wav' || $format == 'webm') {
                $inputLength = count($inputAudioFiles);
                $inputFiles = '';
                $position = '';
                foreach ($inputAudioFiles as $key => $value) {
                    $inputFiles .= ' -i ' . $value;
                    $position .= '[' . $key . ':0]';
                }

                Shell_exec($this->check_os('ffmpeg') . $inputFiles . ' -filter_complex "' . $position . 'concat=n=' . $inputLength . ':v=0:a=1[out]" -map "[out]" ' . $mergedResultURL);
            }

        } catch (Exception $e) {
			Log::error('Error occured during audio file merge task, by user ' . auth()->user()->id . 'Error details: ' . $e->getMessage());
		}

		return true;
    }


    public function convertMP3($file_name, $result_url)
    {
        shell_exec($this->check_os('ffmpeg') . ' -i '. $file_name . ' -f mp3 -ab 128k -ar 44100 -ac 2 ' . $result_url);
    }


    /**
     * Merge with background music
     *
     */
    public function merge_background($bg_audio, $inputAudioFile, $mergeResult) {

		try {

			shell_exec($this->check_os('ffmpeg') . ' -stream_loop -1 -i ' . $bg_audio . ' -i ' . $inputAudioFile . ' -filter_complex "[0:a][1:a]amerge=inputs=2[a]" -map "[a]" ' . $mergeResult);
		
        } catch (Exception $e) {
			Log::error('Error occured during background audio merge task, by user ' . auth()->user()->id . 'Error details: ' . $e->getMessage());
		}

        return true;
	}


    /**
     * Adjust audio file volume
     *
     */
    public function adjust_volume($inputAudioFile, $mergeResult, $volume) {

        try {

            shell_exec($this->check_os('ffmpeg') . ' -i ' . $inputAudioFile . ' -filter:a "volume=' . doubleval($volume) . '" ' . $mergeResult);
        
        } catch (Exception $e) {
            Log::error('Error occured during audio volume adjusting task, by user ' . auth()->user()->id . 'Error details: ' . $e->getMessage());
        }
	
	}


    /**
     * Check if Windows or Linux environment is used
     *
     */
    private function check_os($ffmpeg = '') 
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            if ($ffmpeg == 'ffmpeg') {
                return config('tts.windows_ffmpeg_path') . '\ffmpeg.exe';
            } elseif($ffmpeg == 'ffprobe') {
                return config('tts.windows_ffmpeg_path') . '\ffprobe.exe';
            }
            
        } else {
            return $this->ffmpeg;
        }
    }
}