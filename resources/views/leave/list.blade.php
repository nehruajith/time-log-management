@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Leave List</div>

                <div class="card-body">


                    <div class="table-responsive" id="leaveDiv">
                        <table class="table" id="leaveList">
                            <thead>
                                <tr>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Leave Type</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="taskListBody">

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
        $('#leaveList').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('leave.data') }}",

            columns: [{
                    data: 'start_date',
                    name: 'start_date'
                },
                {
                    data: 'end_date',
                    name: 'end_date'
                },
                {
                    data: 'leave_type',
                    name: 'leave_type'
                },
                {
                    data: 'reason',
                    name: 'reason'
                },
                {
                    data: 'status',
                    name: 'status'
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

    function deleteLeave(id) {
        if (confirm('Are you sure you want to delete this leave?')) {
            $.ajax({
                url: '/leave/delete/' + btoa(id),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                   
                    showSuccessMessage(response.success);
                    $('#leaveList').DataTable().ajax.reload();
                },
                error: function(xhr) {
                   
                    showErrorMessage(xhr.responseJSON.error || 'An error occurred while deleting leave.');
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