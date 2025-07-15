<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\TimeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Exception;
class LeaveController extends Controller
{
    public function index()
    {

        return view('leave.index');
    }

    public function store(Request $request)
    {
        try{
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type' => 'required|string|max:255',
            'reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $hasTimeLog = TimeLog::where('user_id', auth()->id())
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->exists();


        if ($hasTimeLog) {
            return redirect()->back()->withErrors([
                'start_date' => 'You have already submitted a work report for one or more selected dates.',
            ])->withInput();
        }

        $isExistingLeave = Leave::where('user_id', auth()->id())
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($query) use ($request) {
                        $query->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->exists();

        if ($isExistingLeave) {
            return redirect()->back()->withErrors([
                'start_date' => 'You already have a leave request with the selected dates.',
            ])->withInput();
        }

        $data = $request->all();
        $data['user_id'] = auth()->id();
        Leave::create($data);
        return redirect()->route('leave.list')->with('success', 'Leave request submitted successfully.');
    }catch(Exception $e) {
        return redirect()->back()->withErrors(['error' => 'Failed to submit leave request: ' . $e->getMessage()]);
    }
    }

    public function leaveList()
    {
        return view('leave.list');
    }

    public function leaveData()
    {
        $leaves = Leave::where('user_id', auth()->id());

        return DataTables::of($leaves)
            ->addColumn('action', function ($leave) {
                $id = $leave->id;
                return '
                <button class="btn btn-sm btn-danger" onclick="deleteLeave(' . $id . ')">Delete</button>
            ';
            })
            ->make(true);
    }

    public function deleteLeave($id)
    {
        try{
        $leave = Leave::findOrFail(base64_decode($id));
        $leave->delete();
        return response()->json(['success' => 'Leave deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete leave: ' . $e->getMessage()], 500);
        }
    }
}
