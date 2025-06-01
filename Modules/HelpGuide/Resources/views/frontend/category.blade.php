@extends('helpguide::frontend.base', ['title' => __('Category articles')])

@section('main_content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <nav aria-label="breadcrumb">
            <ol class="breadcrumb text-capitalize">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{__('Help center')}}</a></li>
                <li class="breadcrumb-item">{{__('Categories')}}</li>
                <li class="breadcrumb-item">{{ $category->name }}</li>
            </ol>
            </nav>
        </div>
    </div>
</div>
<div class="container mb-4">
    
    @if(count($category->children))
        <h3>{{ $category->name  . ' ' . __('sub_categories')}}</h3>
        <div class="row mb-3">
            @foreach($category->children as $cat)
                <div class="col-xs-6 col-sm-4 col-md-3 mb-2">
                    <div class="card">
                        <div class="card-body p-0">
                            <h3 class="p-0 m-0"><a href="{{$cat->url}}" class="w-100 d-block text-center p-3">{{$cat->name}}</a></h3>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <h2>{{ __('":category" category articles', ['category' => $category->name  ]) }} . <small>( {{$articles->total()}} )</small></h2>
    @foreach($articles as $article)
        <div class="border-bottom my-2 p-2">
            <a href="{{ $article->url }}">
                <h3>{{ $article->title }}</h3>
            </a>
            <small>
                {{ Illuminate\Support\Carbon::parse($article->created_at)->format(setting('date_format')) . " - " . Illuminate\Support\Carbon::parse($article->created_at)->diffForHumans() }}
                @if ( $article->rate_total )
                . {{ __(' :rate out of :total found this helpfull', ['rate' => $article->rate_helpful, 'total' => $article->rate_total])}}
                @endif
            </small>
            <p class="p-0 m-0">{{ \Illuminate\Support\Str::limit( preg_replace("/&#?[a-z0-9]{2,8};/i","", strip_tags($article->content)), 150, $end='...') }}</p>
        </div>
    @endforeach

    @if( $articles )
        {{ $articles->links() }}
    @endif
</div>
@endsection