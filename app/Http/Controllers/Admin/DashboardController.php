<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoleUser;
use DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        $user = auth()->user();

        $roleId = RoleUser::where('user_id', $user->id)->first();
        // dd($roleId);

        if ($roleId->role_id ==  config('app.basic_user_role_id')) {
            return redirect()->route('calendar.index', session()->get('website_slug'));
        }

        // User Registrations
        $users = DB::select("SELECT
        users.user_type AS userType,
        COUNT(users.id) AS totalUsers,
        MAX(users.created_at) AS lastCreatedAt
        FROM
        users
        GROUP BY users.user_type
        ");


        //reviews Open
        $reviews = DB::select("SELECT        
        COUNT(reviews.id) AS totalReviews,
        MAX(reviews.created_at) AS lastCreatedAt
        FROM
        reviews
        ");


        return view('admin.dashboard.index', compact('users', 'reviews'));
    }
}
