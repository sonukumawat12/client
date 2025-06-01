@extends('helpguide::frontend.base', ['title' => __("search")])

@section('main_content')
<div class="container">
    <div class="container">
        @if(isset($articles))
            @if($query)
                <p>{!! __('The Search results for your query :q are', ['q' => '<b>'.$query.'</b>']) !!} :</p>
            @endif
            
                @foreach($articles as $article)
                <div class="border-bottom my-2 p-2">
                    <a href="{{ $article->url }}">
                        <h3>{{ $article->transTitle() }}</h3>
                    </a>
                    <small>
                        {{ Illuminate\Support\Carbon::parse($article->created_at)->format(setting('date_format')) . " - " . Illuminate\Support\Carbon::parse($article->created_at)->diffForHumans() }}
                        @if ( $article->rate_total )
                        . {{ __(' :rate out of :total found this helpfull', ['rate' => $article->rate_helpful, 'total' => $article->rate_total])}}
                        @endif
                    </small>
                    <p class="p-0 m-0">{{ \Illuminate\Support\Str::limit(strip_tags($article->content), 150, $end='...') }}</p>
                </div>
                @endforeach
            
            {{ $articles->appends(['q' => $query ])->links() }}
        @else
            <p>{!! __('No results found for your query :q ', ['q' => '<b>'.$query.'</b>']) !!}</p>
        @endif
    </div>
</div>
@endsection