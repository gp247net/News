<?php
#app/Plugins/News/Models/Content/NewsCategoryDescription.php
namespace App\GP247\Plugins\News\Models;

use Illuminate\Database\Eloquent\Model;

class NewsCategoryDescription extends Model
{
    protected $primaryKey = ['lang', 'category_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = GP247_DB_PREFIX.'news_category_description';
    protected $connection = GP247_DB_CONNECTION;
}
