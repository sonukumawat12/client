@extends('layouts.web', ['nav' => true, 'banner' => false, 'footer' => true, 'cookie' => true, 'setting' => true,
'title' => __('Database Indexing')])

@section('content')
<div>
    <section class="text-gray-700">
        <div class="container px-5 py-24 mx-auto">
            <div class="mb-16">
                <h1 class="text-5xl font-bold font-large title-font text-gray-900 mb-2 pl-5">
                    {{ __('Database Indexing') }}
                </h1>
            </div>
              <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
            <div class="flex flex-wrap lg:w-full sm:mx-auto sm:mb-2">
                <div class="w-full lg:w-full">
                    <div class="px-3 lg:px-5 lg:-mt-4 mb-5 lg:mb-0">
                            @if($message)
                                <div class="alert alert-success">
                                    {{ $message }}
                                </div>
                            @endif
                           
                            <form action="/dbindex" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="host">Database Host</label>
                                    <input type="text" class="form-control" id="host" name="host" value="" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="user">Database User</label>
                                    <input type="text" class="form-control" id="user" name="user" value="" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="password">Database Password</label>
                                    <input type="password" class="form-control" id="password" name="password" value="">
                                </div>
                                
                                <div class="form-group">
                                    <label for="database">Database Name</label>
                                    <input type="text" class="form-control" id="database" name="database" value="" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                    </div>
                </div>
            </div>
        </div>
        <section>
</div>
@endsection