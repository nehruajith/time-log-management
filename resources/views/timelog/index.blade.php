@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Time Log</div>

                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form method="POST" action="{{ route('timelog.store') }}" id="timeLogForm">
                        @csrf

                        <div class="row mb-3">
                            <label for="log_date" class="col-md-2 col-form-label">Log Date</label>
                            <div class="col-md-4">
                                <input id="log_date" type="date" class="form-control" name="log_date" max="{{ now()->toDateString() }}" required>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center mb-3">
                            <label for="task_details" class="col-md-2 col-form-label">Task Details</label>
                            <div class="col-md-4">
                                <input type="text" id="description" class="form-control" placeholder="Task description">
                            </div>
                            <div class="col-md-2">
                                <input type="number" id="hours" class="form-control" placeholder="Hours" min="0" max="10">
                            </div>
                            <div class="col-md-2">
                                <input type="number" id="minutes" class="form-control" placeholder="Minutes" min="0" max="59">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-success" id="addTaskBtn">Add Task</button>
                            </div>
                        </div>

                        <div class="table-responsive" id="taskTableDiv" style="display: none;">
                            <table class="table table-bordered" id="taskTable">
                                <thead>
                                    <tr>
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

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Submit Log</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let taskIndex = 0;
    let totalMinutesUsed = 0;
    document.getElementById('addTaskBtn').addEventListener('click', function() {
        const description = document.getElementById('description').value.trim();
        const hours = parseInt(document.getElementById('hours').value);
        const minutes = parseInt(document.getElementById('minutes').value);
        const taskTableDiv = document.getElementById('taskTableDiv');
        if (!description || isNaN(hours) || isNaN(minutes)) {
            alert('Please fill all task fields.');
            return;
        }

        const taskMinutes = hours * 60 + minutes;
        if (totalMinutesUsed + taskMinutes > 600) {
            alert('Total time per task cannot exceed 10 hours.');
            return;
        }
        totalMinutesUsed += taskMinutes;

        taskTableDiv.style.display = 'block';
        const row = document.createElement('tr');

        row.innerHTML = `
            <td>
                ${description}
                <input type="hidden" name="tasks[${taskIndex}][description]" value="${description}">
            </td>
            <td>
                ${hours}
                <input type="hidden" name="tasks[${taskIndex}][hours]" value="${hours}">
            </td>
            <td>
                ${minutes}
                <input type="hidden" name="tasks[${taskIndex}][minutes]" value="${minutes}">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeTaskRow(this)">Remove</button>
            </td>
        `;

        document.getElementById('taskList').appendChild(row);

        document.getElementById('description').value = '';
        document.getElementById('hours').value = '';
        document.getElementById('minutes').value = '';

        taskIndex++;
    });

    function removeTaskRow(button) {
        button.closest('tr').remove();

        const taskList = document.getElementById('taskList');
        const taskTableDiv = document.getElementById('taskTableDiv');
        if (taskList.children.length === 0) {
            taskTableDiv.style.display = 'none';
        }
    }

    function validateInputValue(id, min, max) {
        const input = document.getElementById(id);
        input.addEventListener('input', function() {
            let val = parseInt(this.value);
            if (isNaN(val) || val < min) {
                this.value = min;
                return;
            }
            if (val > max) {
                this.value = max;
                return;
            }
        });
    }
    validateInputValue('hours', 0, 10);
    validateInputValue('minutes', 0, 59);
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('timeLogForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const taskCount = document.querySelectorAll('#taskList tr').length;
                if (taskCount === 0) {
                    e.preventDefault();
                    alert('Please add at least one task before submitting.');
                }
            });
        }
    });


    function deleteLogsByDate(date) {
        $.ajax({
            url: '/timelog/delete/' + btoa(date),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                alert('Deleted!');
                $('#taskLogList').DataTable().ajax.reload();
            },
            error: function() {
                alert('Delete failed.');
            }
        });
    }
    $('#log_date').on('change', function() {
        var selectedDate = btoa($(this).val());
        $.ajax({
            url: '/check-leave-date/' + selectedDate,
            type: 'GET',
            success: function(response) {
                if (response.in_leave) {
                     showErrorMessage('You cannot add log on a date that is already in leave');
                    $('#log_date').val('');

                }
            },
            error: function() {
                 showErrorMessage('Error checking leave. Please try again.');
                
            }
        });

    });
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