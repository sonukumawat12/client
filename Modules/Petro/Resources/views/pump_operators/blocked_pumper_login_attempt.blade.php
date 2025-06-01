@extends('layouts.app')
@section('title', "Blocked " . __('petro::lang.pump_operators'))
@section('content')
    <section class="content-header main-content-inner">
        <div class="row">
            <div class="col-md-12">
                <h4 class="box-title text-center">Blocked Pump Operators IPs</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>Company Number</th>
                                <th>Last Entered Passcode</th>
                                <th>Login Attempts</th>
                                <th>Status</th>
                                <th>Last Attempted Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($pumperLoginAttempts as $pumperLoginAttempt)
                                <tr>
                                    <td>{{ $pumperLoginAttempt->ip_address }}</td>
                                    <td>{{ $pumperLoginAttempt->company_number }}</td>
                                    <td>{{ $pumperLoginAttempt->last_entered_passcode }}</td>
                                    <td>{{ $pumperLoginAttempt->attempt_count }}</td>
                                    <td>{{ $pumperLoginAttempt->status }}</td>
                                    <td>{{ (new \DateTime($pumperLoginAttempt->updated_at))->format('Y-m-d H:i:s') }}</td>
                                    <td><a href="{{ route('petro.unblockPumperLoginAttempt', $pumperLoginAttempt->id) }}" class="btn btn-primary">Unblock</a></td>
                                </tr>
                            @endforeach
                            @if ($pumperLoginAttempts->count() == 0)
                                <tr>
                                    <td colspan="7" style="text-align: center;">No Blocked Pump Operator Logins</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection