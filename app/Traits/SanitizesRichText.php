<?php

namespace App\Traits;

use App\Support\HtmlSanitizer;

trait SanitizesRichText
{
    public static function bootSanitizesRichText(): void
    {
        static::saving(function ($model) {
            foreach ($model->richTextFields ?? [] as $field) {
                if (array_key_exists($field, $model->getAttributes())) {
                    $model->$field = HtmlSanitizer::clean($model->$field);
                }
            }
        });
    }
}
