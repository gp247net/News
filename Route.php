<?php
use Illuminate\Support\Facades\Route;

$config = file_get_contents(__DIR__.'/gp247.json');
$config = json_decode($config, true);

if(gp247_extension_check_active($config['configGroup'], $config['configKey'])) {

    $langUrl = GP247_SEO_LANG ?'{lang?}/' : '';
    $suffix = GP247_SUFFIX_URL;
    $prefixNewsCategory = config($config['configGroup'].'/'.$config['configKey'].'.GP247_PREFIX_CATEGORY');
    Route::group(
        [
            'middleware' => GP247_FRONT_MIDDLEWARE,
            'prefix'    => $langUrl,
            'namespace' => 'App\GP247\Plugins\News\Controllers',
        ],
        function () use($prefixNewsCategory, $suffix) {
            Route::get($prefixNewsCategory, 'FrontController@index')
            ->name('news.index');
            Route::get($prefixNewsCategory.'/{alias}', 'FrontController@categoryProcessFront')
                ->name('news.category');
            Route::get('{category}/{alias}'.$suffix, 'FrontController@contentProcessFront')
                ->name('news.content');
        }
    );

    Route::group(
        [
            'prefix' => GP247_ADMIN_PREFIX,
            'middleware' => GP247_ADMIN_MIDDLEWARE,
            'namespace' => '\App\GP247\Plugins\News\Admin',
        ], 
        function () {
            Route::group(['prefix' => 'news_category'], function () {
                Route::get('/', 'NewsCategoryController@index')
                    ->name('admin_news_category.index');
                Route::get('create', 'NewsCategoryController@create')
                    ->name('admin_news_category.create');
                Route::post('/create', 'NewsCategoryController@postCreate')
                    ->name('admin_news_category.create');
                Route::get('/edit/{id}', 'NewsCategoryController@edit')
                    ->name('admin_news_category.edit');
                Route::post('/edit/{id}', 'NewsCategoryController@postEdit')
                    ->name('admin_news_category.edit');
                Route::post('/delete', 'NewsCategoryController@deleteList')
                    ->name('admin_news_category.delete');
            });
    
            Route::group(['prefix' => 'news_content'], function () {
                Route::get('/', 'NewsContentController@index')
                    ->name('admin_news_content.index');
                Route::get('create', 'NewsContentController@create')
                    ->name('admin_news_content.create');
                Route::post('/create', 'NewsContentController@postCreate')
                    ->name('admin_news_content.create');
                Route::get('/edit/{id}', 'NewsContentController@edit')
                    ->name('admin_news_content.edit');
                Route::post('/edit/{id}', 'NewsContentController@postEdit')
                    ->name('admin_news_content.edit');
                Route::post('/delete', 'NewsContentController@deleteList')
                    ->name('admin_news_content.delete');
            });
        }
    );
}