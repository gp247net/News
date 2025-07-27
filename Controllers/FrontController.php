<?php
#App\GP247\Plugins\News\Controllers\FrontController.php
namespace App\GP247\Plugins\News\Controllers;

use App\GP247\Plugins\News\AppConfig;
use GP247\Front\Controllers\RootFrontController;
use App\GP247\Plugins\News\Models\NewsCategory;
use App\GP247\Plugins\News\Models\NewsContent;

class FrontController extends RootFrontController
{
    public $plugin;

    public function __construct()
    {
        parent::__construct();
        $this->plugin = new AppConfig;
    }

    
    /**
     * Process news front
     *
     * @param [type] ...$params
     * @return void
     */
    public function index(...$params) 
    {
        if (GP247_SEO_LANG) {
            $lang = $params[0] ?? '';
            gp247_lang_switch($lang);
        }
        return $this->_news();
    }

    /**
     * News 
     * @return [type] [description]
     */
    private function _news()
    {
        $sortBy = 'sort';
        $sortOrder = 'asc';
        $filter_sort = request('filter_sort') ?? '';
        $filterArr = [
            'sort_desc' => ['sort', 'desc'],
            'sort_asc' => ['sort', 'asc'],
            'id_desc' => ['id', 'desc'],
            'id_asc' => ['id', 'asc'],
        ];
        if (array_key_exists($filter_sort, $filterArr)) {
            $sortBy = $filterArr[$filter_sort][0];
            $sortOrder = $filterArr[$filter_sort][1];
        }

        $entries = (new NewsCategory)
            ->getCategoryRoot()
            ->setSort([$sortBy, $sortOrder])
            ->setPaginate()
            ->setLimit(gp247_config('item_list'))
            ->getData();

        $subPath = 'news_index';
        $view = gp247_plugin_process_view($this->plugin->appPath, $this->GP247TemplatePath,$subPath);
        gp247_check_view($view);

        return view(
            $view,
            array(
                'title'       => gp247_language_render($this->plugin->appPath.'::News.front.index'),
                'entries'   => $entries,
                'keyword'     => '',
                'description' => '',
                'layout_page' => 'news_index',
                'filter_sort' => $filter_sort,
                'breadcrumbs' => [
                    ['url'    => '', 'title' => gp247_language_render($this->plugin->appPath.'::News.front.index')],
                ],
            )
        );
    }


    /**
     * Process front category
     *
     * @param [type] ...$params
     * @return void
     */
    public function categoryProcessFront(...$params) 
    {
        if (GP247_SEO_LANG) {
            $lang = $params[0] ?? '';
            $alias = $params[1] ?? '';
            gp247_lang_switch($lang);
        } else {
            $alias = $params[0] ?? '';
        }
        return $this->_category($alias);
    }

    /**
     * Category news
     * @return [type] [description]
     */
    private function _category($alias)
    {
        $newsCategory = (new NewsCategory)->getDetail($alias, 'alias');
            if ($newsCategory) { 
                $entries = (new NewsContent)
                    ->getContentToCategory($newsCategory->id)
                    ->setLimit(gp247_config('item_list'))
                    ->setPaginate()
                    ->getData();

                $subPath = 'news_category';
                $view = gp247_plugin_process_view($this->plugin->appPath, $this->GP247TemplatePath,$subPath);
                gp247_check_view($view);

                return view(
                    $view,
                    array(
                        'title'       => $newsCategory['title'],
                        'description' => $newsCategory['description'],
                        'keyword'     => $newsCategory['keyword'],
                        'entries'     => $entries,
                        'newsCategory' => $newsCategory,
                        'layout_page' => 'news_category',
                        'breadcrumbs' => [
                            ['url'    => gp247_route_front('news.index'), 'title' => gp247_language_render($this->plugin->appPath.'::News.front.index')],
                            ['url'    => '', 'title' => $newsCategory['title']],
                        ],
                    )
                );
            } else {
                gp247_check_view('GP247TemplatePath::'.gp247_store_info('template') . '.screen.notfound');
                return view('GP247TemplatePath::'.gp247_store_info('template') . '.screen.notfound',
                    array(
                        'title'       => gp247_language_render('front.notfound'),
                        'description' => '',
                        'keyword'     => '',
                        'msg'         => gp247_language_render('front.notfound_detail'),
                    )
                );
            }
    }

    /**
     * Process front Content detail
     *
     * @param [type] ...$params
     * @return void
     */
    public function contentProcessFront(...$params) 
    {
        if (GP247_SEO_LANG) {
            $lang = $params[0] ?? '';
            $category = $params[1] ?? '';
            $alias = $params[2] ?? '';
            gp247_lang_switch($lang);
        } else {
            $category = $params[0] ?? '';
            $alias = $params[1] ?? '';
        }
        return $this->_content($category, $alias);
    }

    /**
     * Content detail
     *
     * @param   [string]  $alias  [$alias description]
     *
     * @return  [type]          [return description]
     */
    private function _content($category, $alias)
    {
        $newsContent = (new NewsContent)->getDetail($alias, 'alias');

        if ($newsContent) {
            $categoryNews = $newsContent->category;

            if (!$categoryNews) {


                gp247_check_view($this->GP247TemplatePath.'.screen.notfound');
                return view($this->GP247TemplatePath.'.screen.notfound',
                    array(
                        'title'       => gp247_language_render('front.notfound'),
                        'description' => '',
                        'keyword'     => '',
                        'msg'         => gp247_language_render('front.notfound_detail'),
                    )
                );
            }

            if ($categoryNews->alias != $category) {
                return redirect(gp247_route_front('news.content', ['category' => $categoryNews->alias, 'alias' => $alias]));
            }

            $subPath = 'news_detail';
            $view = gp247_plugin_process_view($this->plugin->appPath, $this->GP247TemplatePath,$subPath);
            gp247_check_view($view);

            $title = ($newsContent) ? $newsContent->title : gp247_language_render('front.notfound');
            return view($view,
                array(
                    'title'           => $title,
                    'newsContent'     => $newsContent,
                    'description'     => $newsContent['description'],
                    'keyword'         => $newsContent['keyword'],
                    'og_image'        => $newsContent->getImage(),
                    'layout_page'     => 'news_detail',
                    'breadcrumbs'     => [
                        ['url'        => $categoryNews->getUrl(), 'title' => $categoryNews->getFull()->title],
                        ['url'        => '', 'title' => $title],
                    ],
                )
            );
        } else {
            gp247_check_view($this->GP247TemplatePath.'.screen.notfound');
            return view($this->GP247TemplatePath.'.screen.notfound',
                array(
                    'title'       => gp247_language_render('front.notfound'),
                    'description' => '',
                    'keyword'     => '',
                    'msg'         => gp247_language_render('front.notfound_detail'),
                )
            );
        }

    }
}
