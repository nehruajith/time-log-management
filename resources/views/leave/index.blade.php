@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Apply Leave</div>

                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form method="POST" action="{{ route('leave.store') }}" id="leaveForm">
                        @csrf

                        <div class="row mb-3">
                            <label for="start_date" class="col-md-2 col-form-label">Leave Start Date</label>
                            <div class="col-md-4">
                                <input id="start_date" type="date" class="form-control" name="start_date" value="{{old('start_date')}}" required>
                            </div>

                        </div>
                        <div class="row mb-3">

                            <label for="end_date" class="col-md-2 col-form-label">Leave End Date</label>
                            <div class="col-md-4">
                                <input id="end_date" type="date" class="form-control" name="end_date" value="{{old('end_date')}}" required>
                            </div>
                        </div>

                        <div class="row mb-3">

                            <label for="leave_type" class="col-md-2 col-form-label">Leave Type</label>
                            <div class="col-md-4">
                                <select id="leave_type" class="form-control" name="leave_type" value="{{old('leave_type')}}" required>
                                    <option value="">-- Select Leave --</option>
                                    <option value="Casual Leave" {{ old('leave_type') == 'Casual Leave' ? 'selected' : '' }}>Casual Leave</option>
                                    <option value="Sick Leave" {{old('leave_type') == 'Sick Leave' ? 'selected' : ''}}>Sick Leave</option>
                                    <option value="Privilege Leave" {{old('leave_type') == 'Privilege Leave' ? 'selected' : ''}}>Privilege Leave</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">

                            <label for="leave_reason" class="col-md-2 col-form-label">Leave Reason</label>
                            <div class="col-md-4">
                                <textarea id="leave_reason" class="form-control" name="reason" required>{{old('reason')}}</textarea>
                            </div>
                        </div>

                        <div class="row mb-3">

                            <label for="button" class="col-md-2 col-form-label"></label>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
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
@endsection