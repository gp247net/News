@php
/*
$layout_page = news_index
**Variables:**
- $newsCategory
- $entries: paginate
Use paginate: $entries->appends(request()->except(['page','_token']))->links()
*/
@endphp

@extends($GP247TemplatePath.'.layout')

@section('block_main')
<section class="section section-xl bg-default">
  <div class="container">
    <div class="row row-30">
    @if ($itemsList->count())
    @foreach ($itemsList as $item)
        @php
            $item = [
              'title' => $item->title,
              'url' => $item->getUrl(),
              'thumb' => $item->getThumb(),
            ];
        @endphp
       @include($GP247TemplatePath.'.common.item_single', ['item' => $item])
    @endforeach
    @else
    {!! gp247_language_render('front.no_item') !!}
    @endif

    </div>

  </div>
</section>

@endsection


@push('scripts')
  {{-- Script here --}}
@endpush
