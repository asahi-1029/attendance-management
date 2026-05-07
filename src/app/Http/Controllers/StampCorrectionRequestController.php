<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest;

class StampCorrectionRequestController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->is_admin) {

            $page = $request->query('page','pending');

            $requests = StampCorrectionRequest::with('attendance', 'user')
                ->where('status',$page)
                ->get();

            return view('admin.stamp_correction_request.index',compact('page','requests'));
        } else {
            $page = $request->query('page','pending');
        
            $requests = StampCorrectionRequest::with('attendance', 'user')
            ->where('user_id', auth()->id())
            ->where('status', $page)
            ->get();

            return view('stamp_correction_request.index',compact('requests','page'));
        }
    }

}
