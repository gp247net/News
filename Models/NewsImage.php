<?php
#app/GP247/Plugins/News/Models/NewsImage.php
namespace App\GP247\Plugins\News\Models;

use App\GP247\Plugins\News\Models\NewsContent;
use Illuminate\Database\Eloquent\Model;
class NewsImage extends Model
{

    public $table = GP247_DB_PREFIX.'news_image';
    public $incrementing = false;
    protected $fillable = ['id', 'image', 'content_id', 'status'];
    protected $connection = GP247_DB_CONNECTION;
    public function content()
    {
        return $this->belongsTo(NewsContent::class, 'content_id', 'id');
    }
    protected static function boot()
    {
        parent::boot();

        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = gp247_generate_id();
            }
        });
    }
}
