@extends('helpguide::install.layouts.master', [
    'step_text' => 'Click next to begin the installation',
    'next' => route('install.requirements'),
    'step' => 0
])

@section('content')
    <p>The wizard will guide you through the installation steps. In addition, you can always click the Help button of the wizard to obtain assistance during the installation process.</p>
@endsection
