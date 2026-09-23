<?php

return [

    'ffmpeg' => env(
        'FFMPEG_BINARY',
        '/usr/bin/ffmpeg'
    ),

    'ffprobe' => env(
        'FFPROBE_BINARY',
        '/usr/bin/ffprobe'
    ),

    /*
     * Segment HLS mục tiêu.
     */
    'hls_time' => 6,

    /*
     * Các chất lượng cần encode.
     */
    'qualities' => [

        '720p' => [
            'height' => 720,
            'video_bitrate' => '2800k',
            'maxrate' => '3000k',
            'bufsize' => '4200k',
        ],

        '1080p' => [
            'height' => 1080,
            'video_bitrate' => '5000k',
            'maxrate' => '5350k',
            'bufsize' => '7500k',
        ],

    ],

];