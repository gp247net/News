<?php
namespace App\GP247\Plugins\News\Admin;

use GP247\Core\Controllers\RootAdminController;
use GP247\Core\Models\AdminLanguage;
use App\GP247\Plugins\News\Models\NewsCategory;
use App\GP247\Plugins\News\AppConfig;

use Validator;

class NewsCategoryController extends RootAdminController
{
    public $languages;
    public $plugin;

    public function __construct()
    {
        parent::__construct();
        $this->languages = AdminLanguage::getListActive();
        $this->plugin = new AppConfig;

    }

    public function index()
    {
        $categoriesTitle =  NewsCategory::getListTitleAdmin();

        $data = [
            'title' => gp247_language_render($this->plugin->appPath.'::Category.admin.list'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'menuRight' => [],
            'menuLeft' => [],
            'topMenuRight' => [],
            'topMenuLeft' => [],
            'urlDeleteItem' => gp247_route_admin('admin_news_category.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'css' => '', 
            'js' => '',
        ];

        $listTh = [
            'image' => gp247_language_render($this->plugin->appPath.'::Category.image'),
            'title' => gp247_language_render($this->plugin->appPath.'::Category.title'),
            'parent' => gp247_language_render($this->plugin->appPath.'::Category.parent'),
            'status' => gp247_language_render($this->plugin->appPath.'::Category.status'),
            'sort' => gp247_language_render($this->plugin->appPath.'::Category.sort'),
            'action' => gp247_language_render($this->plugin->appPath.'::Category.admin.action'),
        ];
        $sort = gp247_clean(request('sort') ?? 'id__desc');
        $keyword    = gp247_clean(request('keyword') ?? '');
        $arrSort = [
            'id__desc' => gp247_language_render('filter_sort.id_desc'),
            'id__asc' => gp247_language_render('filter_sort.id_asc'),
        ];

        $dataSearch = [
            'keyword'    => $keyword,
            'sort'       => $sort,
            'arrSort'    => $arrSort,
        ];
        $dataTmp = (new NewsCategory)->getCategoryListAdmin($dataSearch);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $arrAction = [];
            $arrAction[] = '<a href="' . gp247_route_admin('admin_news_category.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '" class="dropdown-item"><i class="fa fa-edit"></i> '.gp247_language_render('action.edit').'</a>';
            $arrAction[] = '<a href="#" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . gp247_language_render('action.delete') . '" class="dropdown-item"><i class="fas fa-trash-alt"></i> '.gp247_language_render('action.remove').'</a>';
            $arrAction[] = '<a target=_new href="' . gp247_route_front('news.category', ['alias' => $row['alias']]) . '" class="dropdown-item"><i class="fas fa-external-link-alt"></i> '.gp247_language_render('action.link').'</a>';
            $action = $this->procesListAction($arrAction);
            $dataTr[$row['id']] = [
                'image' => gp247_image_render($row->getThumb(), '50px', '50px',$row['title']),
                'title' => $row['title'],
                'parent' => $row['parent'] ? ($categoriesTitle[$row['parent']] ?? '') : 'ROOT',
                'status' => $row['status'] ? '<span class="badge badge-success">ON</span>' : '<span class="badge badge-danger">OFF</span>',
                'sort' => $row['sort'],
                'action' => $action
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links('gp247-core::component.pagination');
        $data['resultItems'] = gp247_language_render($this->plugin->appPath.'::Category.admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);


        //menuRight
        $data['menuRight'][] = '<a href="' . gp247_route_admin('admin_news_category.create') . '" class="btn  btn-success  btn-flat" title="New" id="button_create_new">
                           <i class="fa fa-plus"></i> <span class="hidden-xs">' . gp247_language_render('admin.add_new') . '</span>
                           </a>';
        //=menuRight

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $kSort => $vSort) {
            $optionSort .= '<option  ' . (($sort == $kSort) ? "selected" : "") . ' value="' . $kSort . '">' . $vSort . '</option>';
        }        
        //=menuSort

        //menuSearch
        $data['topMenuRight'][] = '
                <form action="' . gp247_route_admin('admin_news_category.index') . '" id="button_search">
                <div class="input-group input-group">
                <select class="form-control rounded-0 select2" name="sort" id="sort">
                '.$optionSort.'
                </select> &nbsp;
                    <input type="text" name="keyword" class="form-control float-right" placeholder="' . gp247_language_render('search.placeholder') . '" value="' . $keyword . '">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                </form>';
        //=menuSearch


        return view('gp247-core::screen.list')
            ->with($data);
    }

/**
 * Form create new order in admin
 * @return [type] [description]
 */
    public function create()
    {
        $data = [
            'title' => gp247_language_render($this->plugin->appPath.'::Category.admin.add_news_title'),
            'subTitle' => '',
            'title_description' => gp247_language_render($this->plugin->appPath.'::Category.admin.add_news_des'),
            'icon' => 'fa fa-plus',
            'languages' => $this->languages,
            'category' => [],
            'categories' => (new NewsCategory)->getTreeCategoriesAdmin(),
            'url_action' => gp247_route_admin('admin_news_category.create'),
            'appPath'           => $this->plugin->appPath,
        ];
        return view($this->plugin->appPath.'::Admin.news_category')
            ->with($data);
    }

/**
 * Post create new order in admin
 * @return [type] [description]
 */
    public function postCreate()
    {
        $data = request()->all();

        $langFirst = array_key_first(gp247_language_all()->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions'][$langFirst]['title'];
        $data['alias'] = gp247_word_format_url($data['alias']);
        $data['alias'] = gp247_word_limit($data['alias'], 100);

        $validator = Validator::make($data, [
            'sort' => 'numeric|min:0',
            'parent' => 'required',
            'descriptions.*.title' => 'required|string|max:200',
            'descriptions.*.keyword' => 'nullable|string|max:200',
            'descriptions.*.description' => 'nullable|string|max:500',
            'alias' => 'required|string|max:100|news_category_alias_unique',
        ], [
            'alias.regex' => gp247_language_render($this->plugin->appPath.'::Category.alias_validate'),
            'descriptions.*.title.required' => gp247_language_render('validation.required', ['attribute' => gp247_language_render($this->plugin->appPath.'::Category.title')]),
            'alias.news_category_alias_unique' => gp247_language_render($this->plugin->appPath.'::Category.alias_unique'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }
        $dataInsert = [
            'image'    => $data['image'],
            'alias'    => $data['alias'],
            'parent'   => $data['parent'],
            'status'   => !empty($data['status']) ? 1 : 0,
            'sort'     => (int) $data['sort'],
            'store_id' => session('adminStoreId'),
        ];
        $dataInsert = gp247_clean($dataInsert, [], true);

        $category = NewsCategory::createCategoryAdmin($dataInsert);
        $id = $category->id;
        $dataDes = [];
        $languages = $this->languages;
        foreach ($languages as $code => $value) {
            $dataDes[] = [
                'category_id' => $id,
                'lang'        => $code,
                'title'       => $data['descriptions'][$code]['title'],
                'keyword'     => $data['descriptions'][$code]['keyword'],
                'description' => $data['descriptions'][$code]['description'],
            ];
        }
        $dataDes = gp247_clean($dataDes, [], true);
        NewsCategory::insertDescriptionAdmin($dataDes);

        gp247_cache_clear('cache_news_category');

        return redirect()->route('admin_news_category.index')->with('success', gp247_language_render($this->plugin->appPath.'::Category.admin.create_success'));

    }

/**
 * Form edit
 */
    public function edit($id)
    {
        $category = NewsCategory::getCategoryAdmin($id);

        if (!$category) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $data = [
            'title'             => gp247_language_render($this->plugin->appPath.'::Category.admin.edit'),
            'subTitle'          => '',
            'title_description' => '',
            'icon'              => 'fa fa-pencil-square-o',
            'languages'         => $this->languages,
            'category'          => $category,
            'categories'        => (new NewsCategory)->getTreeCategoriesAdmin(),
            'url_action'        => gp247_route_admin('admin_news_category.edit', ['id' => $category['id']]),
            'appPath'           => $this->plugin->appPath,
        ];
        return view($this->plugin->appPath.'::Admin.news_category')
            ->with($data);
    }

/**
 * update status
 */
    public function postEdit($id)
    {
        $category = NewsCategory::getCategoryAdmin($id);
        if (!$category) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $data = request()->all();

        $langFirst = array_key_first(gp247_language_all()->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions'][$langFirst]['title'];
        $data['alias'] = gp247_word_format_url($data['alias']);
        $data['alias'] = gp247_word_limit($data['alias'], 100);

        $validator = Validator::make($data, [
            'sort'                       => 'numeric|min:0',
            'parent'                     => 'required',
            'descriptions.*.title'       => 'required|string|max:200',
            'descriptions.*.keyword'     => 'nullable|string|max:200',
            'descriptions.*.description' => 'nullable|string|max:500',
            'alias'                      => 'required|string|max:100|news_category_alias_unique:'.$id,
        ], [
            'alias.regex' => gp247_language_render($this->plugin->appPath.'::Category.alias_validate'),
            'descriptions.*.title.required' => gp247_language_render('validation.required', ['attribute' => gp247_language_render($this->plugin->appPath.'::Category.title')]),
            'alias.news_category_alias_unique' => gp247_language_render($this->plugin->appPath.'::Category.alias_unique'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }
        //Edit
        $dataUpdate = [
            'image'    => $data['image'],
            'alias'    => $data['alias'],
            'parent'   => $data['parent'],
            'sort'     => $data['sort'],
            'status'   => empty($data['status']) ? 0 : 1,
            'store_id' => session('adminStoreId'),
        ];
        $dataUpdate = gp247_clean($dataUpdate, [], true);
        $category->update($dataUpdate);
        $category->descriptions()->delete();
        $dataDes = [];
        foreach ($data['descriptions'] as $code => $row) {
            $dataDes[] = [
                'category_id' => $id,
                'lang'        => $code,
                'title'       => $row['title'],
                'keyword'     => $row['keyword'],
                'description' => $row['description'],
            ];
        }
        $dataDes = gp247_clean($dataDes, [], true);
        NewsCategory::insertDescriptionAdmin($dataDes);

        gp247_cache_clear('cache_news_category');

        return redirect()->route('admin_news_category.index')->with('success', gp247_language_render($this->plugin->appPath.'::Category.admin.edit_success'));

    }

    /*
    Delete list Item
    Need mothod destroy to boot deleting in model
    */
    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => gp247_language_render('action.method_not_allow')]);
        } else {
            $ids = request('ids');
            $arrID = explode(',', $ids);
            $arrDontPermission = [];
            foreach ($arrID as $key => $id) {
                if(!$this->checkPermisisonItem($id)) {
                    $arrDontPermission[] = $id;
                }
            }
            if (count($arrDontPermission)) {
                return response()->json(['error' => 1, 'msg' => gp247_language_render('admin.remove_dont_permisison') . ': ' . json_encode($arrDontPermission)]);
            }
            NewsCategory::destroy($arrID);
            gp247_cache_clear('cache_news_category');
            return response()->json(['error' => 0, 'msg' => gp247_language_render('action.remove_success')]);
        }
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id) {
        return NewsCategory::getCategoryAdmin($id);
    }

}
