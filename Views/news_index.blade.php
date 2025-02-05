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

    </div>

  </div>
</section>

   {{-- Render include view --}}
   @include($GP247TemplatePath.'.common.include_view')
   {{--// Render include view --}}

@endsection


@push('scripts')
  {{-- Script here --}}
@endpush
