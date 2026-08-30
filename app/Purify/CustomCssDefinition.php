<?php

namespace App\Purify;

use HTMLPurifier_CSSDefinition;
use HTMLPurifier_AttrDef_Enum;
use Stevebauman\Purify\Definitions\CssDefinition;

class CustomCssDefinition implements CssDefinition
{
    /**
     * Apply rules to the CSS Purifier definition.
     *
     * @param HTMLPurifier_CSSDefinition $definition
     * @return void
     */
    public static function apply(HTMLPurifier_CSSDefinition $definition)
    {
        $definition->info['display'] = new HTMLPurifier_AttrDef_Enum([
            'inline', 'block', 'inline-block', 'flex', 'inline-flex', 'grid', 'table', 'none'
        ]);
    }
}
