@php ($total_steps = 6) @endphp
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ticky installation</title>
  <link href="{{asset('build/common/css/main.css')}}" rel="stylesheet">
  <style>
    .installer-card {
      margin: auto;
    }

    .list-group-item {
      padding: 5px 15px;
    }

    .installer-card li {
      font-size: 0.8rem;
      padding: 5px;
    }

    .installer-card {
      max-width: 500px;
      margin: auto;
      color: #000000;
    }
  </style>
</head>

<body class="bg-secondary">

  <div class="container">
    <div class="row">
      <div class="col">
        <div class="card mt-5 mb-5 installer-card">
          <div class="card-header">
            Ticky Installer
            <a href="https://support.pandisoft.com/search?q=install" target="_blank"
              class="float-end text-decoration-none">ⓘ</a>
          </div>
          <div class="card-body p-2">
            @if ($errors->any())
            @foreach ($errors->all() as $error)
            <div class="alert alert-danger">{{ $error }}</div>
            @endforeach
            @endif
            <form method="POST" action="@isset($action){{$action}}@endisset">
              @csrf
              @yield('content')
              <div class="row">
                <div class="col">
                  @isset($previous)
                  <a href="{{$previous}}" class="btn btn-sm btn-outline-primary">Previous</a>
                  @endisset
                </div>
                <div class="col">
                  @isset($next)
                  <a href="{{$next}}" class="btn float-end btn-sm btn-outline-primary">Next</a>
                  @endisset
                  @isset($finish)
                  <a href="{{$finish}}" class="btn float-end btn-sm btn-outline-primary">Finish</a>
                  @endisset
                  @isset($action)
                  <button type="submit" class="btn float-end btn-sm btn-outline-primary">Next</button>
                  @endisset
                </div>
              </div>
            </form>
          </div>
          <div class="card-footer">
            <div>
              @isset($step)Step {{$step}}/{{$total_steps}} : @endisset{{ $step_text }}
            </div>
            <div class="progress">
              <div
                class="progress-bar progress-bar-striped @if($step == $total_steps) bg-success @else bg-success  @endif"
                role="progressbar" style="width: {{number_format( ($step/$total_steps) * 100, 2 )}}%" aria-valuenow="25"
                aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>