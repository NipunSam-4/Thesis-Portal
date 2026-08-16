<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auto-Recommendation Inactivity Thresholds (from .env ONLY)
    |--------------------------------------------------------------------------
    |
    | Reads directly from environment variables without default fallback values:
    | - PTS1_AUTO_RECOMMEND_MINUTES / PTS1_AUTO_RECOMMEND_HOURS
    | - PTS2_EXTENSION_AUTO_RECOMMEND_MINUTES / PTS2_EXTENSION_AUTO_RECOMMEND_HOURS
    |
    */

    'pts1_minutes' => env('PTS1_AUTO_RECOMMEND_MINUTES') !== null 
        ? (int) env('PTS1_AUTO_RECOMMEND_MINUTES') 
        : (int) env('PTS1_AUTO_RECOMMEND_HOURS') * 60,

    'pts2_extension_minutes' => env('PTS2_EXTENSION_AUTO_RECOMMEND_MINUTES') !== null 
        ? (int) env('PTS2_EXTENSION_AUTO_RECOMMEND_MINUTES') 
        : (int) env('PTS2_EXTENSION_AUTO_RECOMMEND_HOURS') * 60,

];
