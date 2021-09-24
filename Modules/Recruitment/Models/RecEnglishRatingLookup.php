<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecEnglishRatingLookup extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_english_rating_lookups';
    public $timestamps = true;
    protected $fillable = ['english_ratings','order_sequence','score'];
}
