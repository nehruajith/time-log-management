@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Time Log List</div>

                <div class="card-body">


                    <div class="table-responsive" id="taskLogDiv">
                        <table class="table" id="taskLogList">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Hours</th>
                                    <th>Minutes</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="taskList">

                            </tbody>
                        </table>
                    </div>



                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        $('#taskLogList').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('timelog.data') }}",

            columns: [{
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'descriptions',
                    name: 'descriptions'
                },
                {
                    data: 'total_hours',
                    name: 'total_hours'
                },
                {
                    data: 'total_minutes',
                    name: 'total_minutes'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]

        });
    });
    function deleteLogsByDate(date) {
 
    if (confirm('Are you sure you want to delete all logs for ' + date + '?')) {
        $.ajax({
            url: '/timelog/delete/' + btoa(date),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                 showSuccessMessage(response.success);
                $('#taskLogList').DataTable().ajax.reload();
            },
            error: function(xhr) {
                showErrorMessage(xhr.responseJSON.error || 'An error occurred while deleting logs.');
            }
        });
    }
}
</script>

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

