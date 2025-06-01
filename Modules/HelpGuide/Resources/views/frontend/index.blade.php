@extends('helpguide::frontend.base',['fixednavbar' => 1])

@section('sub-header')
  <div class="homepage">
    @parent()
  </div>
@endsection

@section('main_content')
<section class="featured-articles p-2 container mb-5">
    <header class="section-header mt-4">
        <h3>{{__('Featured articles')}}</h3>
    </header>
    <div class="row m-4">
        @foreach ($featured_articles as $article)
        <div class="col-sm-12 col-lg-4">
          <div class="card card-body">
            <h3>
                <a href="{{ $article->url }}" class="text-dark text-decoration-none" style="font-size: 20px !important;">
                    <i class="bi bi-newspaper"></i> {{ $article->transTitle() }}
                </a>
            </h3>
          </div>
        </div>
        @endforeach
    </div>
</section>

<section class="articles-by-category container mb-5">
    <div class="container">
        <header class="section-header mb-5">
            <h3>{{__('Browse our knowledge base categories')}}</h3>
        </header>
        <div class="row mx-lg-n4">
                        
            @foreach ($articles_by_category as $abc)
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card card-body">
                    <h2><a href="{{$abc->url}}" class="text-dark text-decoration-none">{{$abc->name}} ({{ $abc->articles_count }})</a></h2>
                    @foreach ($abc->articles as $articleItem)
                        <h4>
                            <a href="{{ $articleItem->url }}" class="pl-2 d-block p-2 text-decoration-none border-bottom article-category text-dark">
                                <i class="bi bi-newspaper"></i> {{$articleItem->title}}
                            </a>
                        </h4>
                    @endforeach
                  </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@if(defaultSetting('ticket_allowed', true))
<section class="submit-ticket container-fluid">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="row justify-content-center my-5 text-white">
                    <div class="col-lg-12 text-center"><h4>{{__('still_no_luck_we_can_help')}}</h4></div>
                    <div class="col-auto mb-3">
                        <a href="{{ route("my_account") }}" class="btn btn-submit-ticket btn-sm" ><i class="bi bi-chat-left-quote"></i> {{__('Submit a ticket')}}</a>
                    </div>
                    <div class="col-lg-12 text-center">{{__('submit_ticket_text')}}</div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
@endsection
