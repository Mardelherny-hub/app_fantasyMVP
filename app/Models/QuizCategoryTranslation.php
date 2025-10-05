<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizCategoryTranslation extends Model
{
    protected $fillable = ['quiz_category_id', 'locale', 'name'];

    public function category()
    {
        return $this->belongsTo(\App\Models\QuizCategory::class, 'quiz_category_id');
    }
}
