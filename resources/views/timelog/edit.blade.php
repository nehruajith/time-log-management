@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Logs for {{ $date }}</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('timelog.update', $date) }}">
        @csrf

        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Hours</th>
                    <th>Minutes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                <tr>
                    <td>
                        <input type="text" name="logs[{{ $log->id }}][description]" value="{{ $log->description }}" class="form-control" required>
                    </td>
                    <td>
                        <input type="number" name="logs[{{ $log->id }}][hours]" value="{{ $log->hours }}" class="form-control" min="0" max="10" required>
                    </td>
                    <td>
                        <input type="number" name="logs[{{ $log->id }}][minutes]" value="{{ $log->minutes }}" class="form-control" min="0" max="59" required>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Update Logs</button>
        <a href="{{ route('timelog.list') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
@if (session('success') || session('error'))
<script>
$(document).ready(function() {
    @if(session('success'))
       
        showSuccessMessage('{{ session('success') }}');
    @elseif(session('error'))
        showErrorMessage('{{ session('error') }}');
    @endif
});
</script>
@endif