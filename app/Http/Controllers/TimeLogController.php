<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\TimeLog;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use DB;
use Exception;

class TimeLogController extends Controller
{
    public function index()
    {

        return view('timelog.index');
    }

    public function store(Request $request)
    {
        try {
            $date = $request->log_date;
            $userId = auth()->id();

            $existingMinutes = TimeLog::whereDate('date', $date)
                ->where('user_id', $userId)
                ->sum(DB::raw('hours * 60 + minutes'));


            $currentMinutes = 0;

            foreach ($request->tasks as $task) {
                $currentMinutes += ($task['hours'] * 60) + $task['minutes'];
            }

            if ($currentMinutes + $existingMinutes > 600) {
                return redirect()->back()->withErrors([
                    'hours' => 'Total time for the day cannot exceed 10 hours.',
                ])->withInput();
            }

            foreach ($request->tasks as $task) {
                TimeLog::create([
                    'user_id' => $userId,
                    'date' => $date,
                    'description' => $task['description'],
                    'hours' => $task['hours'],
                    'minutes' => $task['minutes'],
                ]);
            }
            return redirect()->route('timelog.list')->with('success', 'Tasks log saved successfully');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to save tasks log: ' . $e->getMessage()]);
        }
    }

    public function logList()
    {

        return view('timelog.list');
    }

    public function logData(Request $request)
    {
        $query = TimeLog::select([
            'date',
            DB::raw('GROUP_CONCAT(description SEPARATOR "; ") as descriptions'),
            DB::raw('SUM(hours) as total_hours'),
            DB::raw('SUM(minutes) as total_minutes')
        ])
            ->where('user_id', auth()->id())
            ->groupBy('date')
            ->orderBy('date', 'desc');

        return datatables()->of($query)
            ->filterColumn('descriptions', function ($query, $keyword) {
                $query->havingRaw("LOWER(GROUP_CONCAT(description SEPARATOR '; ')) LIKE ?", ["%" . strtolower($keyword) . "%"]);
            })
            ->filterColumn('total_hours', function ($query, $keyword) {
                $query->havingRaw("SUM(hours) LIKE ?", ["%" . $keyword . "%"]);
            })
            ->filterColumn('total_minutes', function ($query, $keyword) {
                $query->havingRaw("SUM(minutes) LIKE ?", ["%" . $keyword . "%"]);
            })
            ->addColumn('action', function ($row) {
                $date = $row->date;
                return '
                <a href="' . route('timelog.edit', base64_encode($date)) . '" class="btn btn-sm btn-warning">Edit</a>
                <button class="btn btn-sm btn-danger" onclick="deleteLogsByDate(\'' . $date . '\')">Delete</button>
            ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function editLog($date)
    {
        $date = base64_decode($date);
        $logs = TimeLog::where('user_id', auth()->id())
            ->where('date', $date)
            ->get();
        return view('timelog.edit', compact('logs', 'date'));
    }

    public function updateLog(Request $request, $date)

    {
        try {
            $data = $request->all();

            $totalMinutes = 0;

            foreach ($data['logs'] as $log) {
                $totalMinutes += ($log['hours'] * 60) + $log['minutes'];
            }

            if ($totalMinutes > 600) {
                return redirect()->back()->withErrors([
                    'total' => 'Total time for the day cannot exceed 10 hours.',
                ])->withInput();
            }

            foreach ($data['logs'] as $id => $logData) {
                TimeLog::where('id', $id)
                    ->where('user_id', auth()->id())
                    ->update([
                        'description' => $logData['description'],
                        'hours' => $logData['hours'],
                        'minutes' => $logData['minutes'],
                    ]);
            }

            return redirect()->route('timelog.list')->with('success', 'Time logs updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to update time logs: ' . $e->getMessage()]);
        }
    }

    public function deleteLog($date)
    {
        try{
        $date = base64_decode($date);
        TimeLog::where('user_id', auth()->id())
            ->where('date', $date)
            ->delete();

        return response()->json(['success' => 'Time logs deleted successfully'],200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete time logs: ' . $e->getMessage()], 500);
        }
    }

    public function checkLeaveDate($date)
    {
        $date = base64_decode($date);
        $isInLeave = Leave::where('user_id', auth()->id())
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->exists();

        return response()->json([
            'in_leave' => $isInLeave
        ]);
    }
}
