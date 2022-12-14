<?php

return [

    /*
    |--------------------------------------------------------------------------
    | General TTS Settings
    |--------------------------------------------------------------------------
    */

    'enable' => [
        'aws' => env('CONFIG_ENABLE_AWS'),
        'aws_standard' => env('CONFIG_ENABLE_AWS_STANDARD'),
        'aws_neural' => env('CONFIG_ENABLE_AWS_NEURAL'),
        'azure' => env('CONFIG_ENABLE_AZURE'),
        'azure_standard' => env('CONFIG_ENABLE_AZURE_STANDARD'),
        'azure_neural' => env('CONFIG_ENABLE_AZURE_NEURAL'),
        'gcp' => env('CONFIG_ENABLE_GCP'),
        'gcp_standard' => env('CONFIG_ENABLE_GCP_STANDARD'),
        'gcp_neural' => env('CONFIG_ENABLE_GCP_NEURAL'),
        'ibm' => env('CONFIG_ENABLE_IBM'),
        'sound_studio' => env('CONFIG_ENABLE_SOUND_STUDIO'),    
    ],

    'frontend' => [
        'status' => env('CONFIG_FRONTEND_LIVE_SYNTHESIZE'),
        'max_chars' => env('CONFIG_FRONTEND_MAX_CHAR_LIMIT'),
        'neural' => env('CONFIG_FRONTEND_NEURAL_VOICES', 'disable'),
    ],

    'voice_type' => env('CONFIG_VOICE_TYPE', 'both'),

    'ssml_effect' => env('CONFIG_SSML_EFFECT', 'enable'),

    'max_chars_limit' => env('CONFIG_MAX_CHAR_LIMIT', 3000),
    
    'max_voice_limit' => env('CONFIG_MAX_VOICE_LIMIT', 5),

    'max_voice_limit_user' => env('CONFIG_MAX_VOICE_LIMIT_USER', 5),

    'free_chars_limit' => env('CONFIG_MAX_FREE_TIER_CHAR_LIMIT', 1000),

    'free_chars' => env('CONFIG_MAX_FREE_CHARS', 0),

    'default_storage' => env('CONFIG_DEFAULT_STORAGE', 'local'),

    'clean_storage' => env('CONFIG_CLEAN_STORAGE', 'never'),

    'user_neural' => env('CONFIG_USER_NEURAL_VOICES', 'disable'),

    'vendor_logos' => env('CONFIG_VENDOR_LOGOS', 'show'),

    'default_language' => env('CONFIG_DEFAULT_LANGUAGE'),

    'default_voice' => env('CONFIG_DEFAULT_VOICE'),

    'listen_download' => env('CONFIG_LISTEN_DOWNLOAD'),

    'max_background_audio_size' => env('CONFIG_MAX_BACKGROUND_AUDIO_SIZE'),

    'max_merge_files' => env('CONFIG_MAX_MERGE_FILES'),

    'windows_ffmpeg_path' => env('CONFIG_WINDOWS_FFMPEG_PATH'),

];
