<?php

use Stevebauman\Purify\Definitions\Html5Definition;

return [

    'default' => 'default',

    'configs' => [

        'default' => [
            'Core.Encoding' => 'utf-8',
            'HTML.Doctype' => 'HTML 4.01 Transitional',
            'HTML.Allowed' => 'h1,h2,h3,h4,h5,h6,b,u,strong,i,em,s,del,a[href|title|target],ul,ol,li,p[style|class|align],div[style|class|align],br,span[style|class],img[width|height|alt|src|class|style|align],blockquote,pre,code,table,thead,tbody,tr,th,td[colspan|rowspan|style],hr,sub,sup,figure[class|style],figcaption[class|style]',
            'HTML.ForbiddenElements' => '',
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty' => false,
        ],

    ],

    'definitions' => Html5Definition::class,

    'css-definitions' => null,

    'serializer' => [
        'driver' => env('CACHE_STORE', env('CACHE_DRIVER', 'file')),
        'cache' => \Stevebauman\Purify\Cache\CacheDefinitionCache::class,
    ],

];
