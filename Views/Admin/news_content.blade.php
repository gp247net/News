@extends('gp247-core::layout')

@section('main')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header with-border">
                <h2 class="card-title">{{ $title_description??'' }}</h2>

                <div class="card-tools">
                    <div class="btn-group float-right mr-5">
                        <a href="{{ gp247_route_admin('admin_news_content.index') }}" class="btn  btn-flat btn-default" title="List"><i
                                class="fa fa-list"></i><span class="hidden-xs"> {{gp247_language_render('admin.back_list')}}</span></a>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal" id="form-main"
                enctype="multipart/form-data">


                <div class="card-body">
                        @php
                        $descriptions = $content?$content->descriptions->keyBy('lang')->toArray():[];
                        @endphp

                        @foreach ($languages as $code => $language)

                        <div class="card">
                            <div class="card-header with-border">
                                <h3 class="card-title">{{ $language->name }} {!! gp247_image_render($language->icon,'20px','20px', $language->name) !!}</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                      <i class="fas fa-minus"></i>
                                    </button>
                                  </div>
                            </div>
                    
                            <div class="card-body">
                        <div
                            class="form-group  row {{ $errors->has('descriptions.'.$code.'.title') ? ' text-red' : '' }}">
                            <label for="{{ $code }}__title"
                                class="col-sm-2 col-form-label">{{ gp247_language_render($appPath.'::Content.title') }} <span class="seo" title="SEO"><i class="fa fa-coffee" aria-hidden="true"></i></span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="{{ $code }}__title" name="descriptions[{{ $code }}][title]"
                                        value="{{ old()? old('descriptions.'.$code.'.title'):($descriptions[$code]['title']??'') }}"
                                        class="form-control {{ $code.'__title' }}" placeholder="" />
                                </div>
                                @if ($errors->has('descriptions.'.$code.'.title'))
                                <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('descriptions.'.$code.'.title') }}
                                </span>
                                @else
                                <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ gp247_language_render('admin.max_c',['max'=>200]) }}
                                </span>
                                @endif
                            </div>
                        </div>

                        <div
                            class="form-group  row {{ $errors->has('descriptions.'.$code.'.keyword') ? ' text-red' : '' }}">
                            <label for="{{ $code }}__keyword"
                                class="col-sm-2 col-form-label">{{ gp247_language_render($appPath.'::Content.keyword') }} <span class="seo" title="SEO"><i class="fa fa-coffee" aria-hidden="true"></i></span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="{{ $code }}__keyword"
                                        name="descriptions[{{ $code }}][keyword]"
                                        value="{{ old()?old('descriptions.'.$code.'.keyword'):($descriptions[$code]['keyword']??'') }}"
                                        class="form-control {{ $code.'__keyword' }}" placeholder="" />
                                </div>
                                @if ($errors->has('descriptions.'.$code.'.keyword'))
                                <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('descriptions.'.$code.'.keyword') }}
                                </span>
                                @else
                                <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ gp247_language_render('admin.max_c',['max'=>200]) }}
                                </span>
                                @endif
                            </div>
                        </div>

                        <div
                            class="form-group  row {{ $errors->has('descriptions.'.$code.'.description') ? ' text-red' : '' }}">
                            <label for="{{ $code }}__description"
                                class="col-sm-2 col-form-label">{{ gp247_language_render($appPath.'::Content.description') }} <span class="seo" title="SEO"><i class="fa fa-coffee" aria-hidden="true"></i></span></label>
                            <div class="col-sm-8">
                                    <textarea  id="{{ $code }}__description"
                                        name="descriptions[{{ $code }}][description]"
                                        class="form-control {{ $code.'__description' }}" placeholder="" />{{ old()?old('descriptions.'.$code.'.description'):($descriptions[$code]['description']??'') }}</textarea>
                                @if ($errors->has('descriptions.'.$code.'.description'))
                                <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('descriptions.'.$code.'.description') }}
                                </span>
                                @else
                                <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ gp247_language_render('admin.max_c',['max'=>300]) }}
                                </span>
                                @endif
                            </div>
                        </div>

                        <div
                            class="form-group row {{ $errors->has('descriptions.'.$code.'.content') ? ' text-red' : '' }}">
                            <label for="{{ $code }}__content"
                                class="col-sm-2 col-form-label">{{ gp247_language_render($appPath.'::Content.content') }}</label>
                            <div class="col-sm-8">
                                <textarea id="{{ $code }}__content" class="editor"
                                    name="descriptions[{{ $code }}][content]">
                                        {{ old('descriptions.'.$code.'.content',($descriptions[$code]['content']??'')) }}
                                    </textarea>
                                @if ($errors->has('descriptions.'.$code.'.content'))
                                <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('descriptions.'.$code.'.content') }}
                                </span>
                                @endif
                            </div>
                        </div>

                            </div>
                        </div>
                        @endforeach

                        {{-- select category --}}
                        <div class="form-group row kind kind0 kind1 {{ $errors->has('category_id') ? ' text-red' : '' }}">
                            <label for="category_id" class="col-sm-2 col-form-label">
                                {{ gp247_language_render($appPath.'::Content.admin.select_category') }}
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control input-sm category_id" 
                                    data-placeholder="{{ gp247_language_render($appPath.'::Content.admin.select_category') }}" style="width: 100%;"
                                    name="category_id">
                                    <option value=""></option>
                                    @foreach ($categories as $k => $v)
                                    <option value="{{ $k }}"
                                        {{ (old('category_id',$content['category_id']??'') ==$k) ? 'selected':'' }}>{{ $v }}
                                    </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('category_id'))
                                <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('category_id') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        {{-- //select category --}}

                        <div class="form-group  row {{ $errors->has('alias') ? ' text-red' : '' }}">
                            <label for="alias"
                                class="col-sm-2 col-form-label">{!! gp247_language_render($appPath.'::Content.alias') !!}</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="alias" name="alias" value="{!! old('alias',($content['alias']??'')) !!}"
                                        class="form-control alias" placeholder="" />
                                </div>
                                @if ($errors->has('alias'))
                                <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('alias') }}
                                </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group  row {{ $errors->has('image') ? ' text-red' : '' }}">
                            <label for="image" class="col-sm-2 col-form-label">{{ gp247_language_render($appPath.'::Content.image') }}</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="text" id="image" name="image"
                                        value="{{ old('image',$content['image']??'') }}"
                                        class="form-control input-sm image" placeholder="" />
                                    <div class="input-group-append">
                                        <a data-input="image" data-preview="preview_image" data-type="docs"
                                            class="btn btn-primary lfm">
                                            <i class="fa fa-image"></i> {{gp247_language_render('action.choose_image')}}
                                        </a>
                                    </div>
                                </div>
                                @if ($errors->has('image'))
                                <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('image') }}
                                </span>
                                @endif
                                <div id="preview_image" class="img_holder">
                                    @if (old('image',$content['image']??''))
                                    <img src="{{ gp247_file(old('image',$content['image']??'')) }}">
                                    @endif

                                </div>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">{{ gp247_language_render($appPath.'::Content.additional_images') }}</label>
                            <div class="col-sm-8">
                                <button type="button" id="add_more_image" class="btn btn-flat btn-success">
                                    <i class="fa fa-plus" aria-hidden="true"></i> {{ gp247_language_render($appPath.'::Content.add_more_image') }}
                                </button>
                                @if (!empty($content->images))
                                    @foreach ($content->images as $key => $image)
                                    <div class="group-image">
                                        <div class="input-group">
                                            <input type="text" id="image_additional_{{ $key }}" name="images[]" value="{{ $image->image }}" class="form-control input-sm image" placeholder="">
                                            <div class="input-group-append">
                                                <a data-input="image_additional_{{ $key }}" data-preview="preview_image_{{ $key }}" data-type="docs" class="btn btn-primary lfm">
                                                    <i class="fa fa-image"></i> {{gp247_language_render('action.choose_image')}}
                                                </a>
                                            </div>
                                        </div>
                                        <div id="preview_image_{{ $key }}" class="img_holder">
                                            @if ($image->image)
                                                <img src="{{ gp247_image_get_path($image->image) }}">
                                            @endif
                                        </div>
                                        <span class="btn btn-flat btn-danger remove-image"><i class="fa fa-times"></i></span>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>



                        <div class="form-group  row {{ $errors->has('sort') ? ' text-red' : '' }}">
                            <label for="sort" class="col-sm-2 col-form-label">{{ gp247_language_render($appPath.'::Content.sort') }}</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="number" style="width: 100px;" id="sort" name="sort"
                                        value="{{ old()?old('sort'):$content['sort']??0 }}" class="form-control sort"
                                        placeholder="" />
                                </div>
                                @if ($errors->has('sort'))
                                <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('sort') }}
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group  row">
                            <label for="status" class="col-sm-2 col-form-label">{{ gp247_language_render($appPath.'::Content.status') }}</label>
                            <div class="col-sm-8">
                                <input class="checkbox" type="checkbox" name="status"
                                    {{ old('status',(empty($content['status'])?0:1))?'checked':''}}>

                            </div>
                        </div>


                </div>



                <!-- /.card-body -->

                <div class="card-footer row">
                    @csrf
                    <div class="col-md-2">
                    </div>

                    <div class="col-md-8">
                        <div class="btn-group float-right">
                            <button type="submit" class="btn btn-primary">{{ gp247_language_render('action.submit') }}</button>
                        </div>

                        <div class="btn-group float-left">
                            <button type="reset" class="btn btn-warning">{{ gp247_language_render('action.reset') }}</button>
                        </div>
                    </div>
                </div>

                <!-- /.card-footer -->
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .group-image {
        position: relative;
        margin-top: 15px;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .group-image .remove-image {
        position: absolute;
        top: -10px;
        right: -10px;
        width: 25px;
        height: 25px;
        padding: 0;
        line-height: 25px;
        text-align: center;
        border-radius: 50%;
        background: #dc3545;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .group-image .remove-image:hover {
        background: #c82333;
    }

    .group-image .remove-image i {
        font-size: 12px;
        color: #fff;
    }

    .group-image .img_holder {
        margin-top: 10px;
    }

    .group-image .img_holder img {
        max-height: 100px;
        border-radius: 4px;
    }
</style>
@endpush

@push('scripts')
@include('gp247-core::component.ckeditor_js')
<script type="text/javascript">
    $('textarea.editor').ckeditor(
    {
        filebrowserImageBrowseUrl: '{{ gp247_route_admin('admin.home').'/'.config('lfm.url_prefix') }}?type=docs',
        filebrowserImageUploadUrl: '{{ gp247_route_admin('admin.home').'/'.config('lfm.url_prefix') }}/upload?type=docs&_token={{csrf_token()}}',
        filebrowserBrowseUrl: '{{ gp247_route_admin('admin.home').'/'.config('lfm.url_prefix') }}?type=files',
        filebrowserUploadUrl: '{{ gp247_route_admin('admin.home').'/'.config('lfm.url_prefix') }}/upload?type=file&_token={{csrf_token()}}',
        filebrowserWindowWidth: '900',
        filebrowserWindowHeight: '500'
    }
);

$(document).ready(function() {
    $('#add_more_image').click(function() {
        var id = Date.now();
        var html = `<div class="group-image">
            <div class="input-group">
                <input type="text" id="image_additional_${id}" name="images[]" value="" class="form-control input-sm image" placeholder="">
                <div class="input-group-append">
                    <a data-input="image_additional_${id}" data-preview="preview_image_${id}" data-type="docs" class="btn btn-primary lfm">
                        <i class="fa fa-image"></i> {{gp247_language_render('action.choose_image')}}
                    </a>
                </div>
            </div>
            <div id="preview_image_${id}" class="img_holder"></div>
            <span class="btn btn-flat btn-danger remove-image"><i class="fa fa-times"></i></span>
        </div>`;
        $('#add_more_image').after(html);
        $('.lfm').filemanager();
    });
    
    $(document).on('click', '.remove-image', function() {
        $(this).closest('.group-image').remove();
    });
});
</script>
@endpush