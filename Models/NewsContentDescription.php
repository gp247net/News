<?php
#app/GP247/Plugins/News/Models/NewsContentDescription.php
namespace App\GP247\Plugins\News\Models;

use Illuminate\Database\Eloquent\Model;

class NewsContentDescription extends Model
{
    protected $primaryKey = ['lang', 'content_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = GP247_DB_PREFIX.'news_content_description';
    protected $connection = GP247_DB_CONNECTION;
}
