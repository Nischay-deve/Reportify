<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RoleUser;
use App\Models\Auditlog;
use App\Models\Role;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\StoreAdminUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateAdminUserRequest;
use Hash;
use DB;
use Carbon\Carbon;
use App\Services\UserEmailService;

class UsersController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();
        $roleId = RoleUser::where('user_id', $user->id)->first();

        $userType = request()->query('mode');

        if (empty($userType)) {
            $userType =   User::TYPE_INTERNAL;
        } else {
            $userType = User::TYPE_EXTERNAL;
        }

        // show all users so that they can be restored
        $users = User::select('users.*')
            ->withTrashed()
            ->join('user_profiles', 'user_profiles.user_id', 'users.id');

        $users = $users->where('user_type', $userType);

        $users = $users->get();



        return view('admin.users.index', compact('userType', 'users',));
    }


    public function create()
    {

        $roles = Role::Where('title', '!=', 'Web Admin')->get()->pluck('title', 'id');

        $allUsers = User::select('users.id as user_id', 'users.name', 'users.email')
            ->join('role_user', 'role_user.user_id', 'users.id')
            ->join('roles', 'roles.id', 'role_user.role_id')
            ->where('roles.id', '!=', config("app.admin_user_role_id"))
            ->orderby('users.name')
            ->get()->toArray();
        // dd($allUsers);

        $condition = "";
        $locations = DB::select(DB::raw("SELECT 
        locations.id, 
        locations.name
        FROM 
        `locations` 
        where locations.deleted_at is null " . $condition . "
        GROUP BY `locations`.`id` 
        order by locations.parent, locations.name "));

        return view('admin.users.create', compact('locations', 'roles', 'allUsers'));
    }

    public function store(StoreUserRequest $request)
    {
        // dd($request->all());

        $user = User::create($request->all());

        $user->email_verification = Carbon::now()->getTimestamp();
        $user->save();

        $user->roles()->sync($request->input('roles', []));
        $user->profile()->create($request->all());

        // Audit Log Entry
        $resultMessage = sprintf('Created user: %s | Email: %s', $user->id, $user->email);
        Auditlog::info(Auditlog::CATEGORY_USER, $resultMessage, $user->id);

        return redirect()->route('admin.users.index')->with('success', 'Successfully added!');
    }

    public function edit($id)
    {

        $user = auth()->user();
        $roleId = RoleUser::where('user_id', $user->id)->first();

        $roles = Role::where('title', '<>', 'superadmin')->get()->pluck('title', 'id');
        $profile = User::find($id);
        $profile->load('roles');
        $selectedRole = RoleUser::where('user_id', $profile->id)->first();

        $condition = "";
        $locations = DB::select(DB::raw("SELECT 
        locations.id, 
        locations.name
        FROM 
        `locations` 
        where locations.deleted_at is null " . $condition . "
        GROUP BY `locations`.`id` 
        order by locations.parent, locations.name "));

        return view('admin.users.edit', compact(
            'locations',
            'roles',
            'profile',
            'selectedRole'
        ));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        // dd($request->all());
        // dd($user);
        $user->update($request->all());
        $user->roles()->sync($request->input('roles', []));
        // $user->profile()->update($request->only('gender', 'mobile', 'dob'
        //     , 'team_id'
        //     , 'slug'
        // ));

        $user->profile()->update($request->only('gender', 'mobile', 'dob', 'slug', 'place_id', 'occupation', 'moreinfo', 'reference', 'reference_mobile'));

        // Audit Log Entry
        $resultMessage = sprintf('Updated user: %s | Email: %s', $user->id, $user->email);
        Auditlog::info(Auditlog::CATEGORY_USER, $resultMessage, $user->id);

        if ($user->user_type == User::TYPE_EXTERNAL) {
            return redirect()->route('admin.users.index', ['mode' => 'external'])->with('success', 'Successfully updated!');
        } else {
            return redirect()->route('admin.users.index')->with('success', 'Successfully updated!');
        }
    }


    ///////////////////////////////
    // ADMIN User Functions
    ///////////////////////////////
    public function adminIndex()
    {
        $user = auth()->user();
        $roleId = RoleUser::where('user_id', $user->id)->first();

        $isMasterAdmin = false;
        $mastAdminUserIdsArr = explode(',', config('app.master_admin_users'));
        if (in_array($user->id, $mastAdminUserIdsArr)) {
            $isMasterAdmin = true;
        }

        if ($roleId->role_id == config("app.admin_user_role_id") && $isMasterAdmin) {
            // show all admin users so that they can be restored
            $users = User::select('users.*')
                ->withTrashed()
                ->whereHas('roles', function ($query) {
                    $query->Where('roles.id', config('app.admin_user_role_id'));
                })
                ->join('user_profiles', 'user_profiles.user_id', 'users.id')
                ->get();
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorize Access!');
        }

        return view('admin.users.admin_index', compact('users'));
    }

    public function adminCreate()
    {

        $user = auth()->user();
        $roleId = RoleUser::where('user_id', $user->id)->first();

        $isMasterAdmin = false;
        $mastAdminUserIdsArr = explode(',', config('app.master_admin_users'));
        if (in_array($user->id, $mastAdminUserIdsArr)) {
            $isMasterAdmin = true;
        }

        if ($roleId->role_id == config("app.admin_user_role_id") && $isMasterAdmin) {

            return view('admin.users.admin_create');
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorize Access!');
        }
    }

    public function adminStore(StoreAdminUserRequest $request)
    {
        // dd($request->all());
        $user = auth()->user();
        $roleId = RoleUser::where('user_id', $user->id)->first();

        $isMasterAdmin = false;
        $mastAdminUserIdsArr = explode(',', config('app.master_admin_users'));
        if (in_array($user->id, $mastAdminUserIdsArr)) {
            $isMasterAdmin = true;
        }

        if ($roleId->role_id == config("app.admin_user_role_id") && $isMasterAdmin) {

            $user = User::create($request->all());
            // Admin role
            $user->roles()->sync( config('app.admin_user_role_id'));
            $user->profile()->create($request->all());

            // Audit Log Entry
            $resultMessage = sprintf('Created admin user: %s | Email: %s', $user->id, $user->email);
            Auditlog::info(Auditlog::CATEGORY_ADMIN_USER, $resultMessage, $user->id);

            // store the user into REgistered user list
            if (config('app.store_user_to_list')) {
                $userDetails = $user->profile()->first();
                $this->storeUserToFormList($user, $userDetails);
            }

            return redirect()->route('admin.admin_users.index')->with('success', 'Successfully added!');
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorize Access!');
        }
    }

    public function adminEdit($id)
    {
        $user = auth()->user();
        $roleId = RoleUser::where('user_id', $user->id)->first();

        $isMasterAdmin = false;
        $mastAdminUserIdsArr = explode(',', config('app.master_admin_users'));
        if (in_array($user->id, $mastAdminUserIdsArr)) {
            $isMasterAdmin = true;
        }

        if ($roleId->role_id == config("app.admin_user_role_id") && $isMasterAdmin) {
            $profile = User::find($id);
            return view('admin.users.admin_edit', compact('profile'));
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorize Access!');
        }
    }

    public function adminUpdate(UpdateAdminUserRequest $request, User $user)
    {
        $loged_user = auth()->user();
        $roleId = RoleUser::where('user_id', $loged_user->id)->first();

        $isMasterAdmin = false;
        $mastAdminUserIdsArr = explode(',', config('app.master_admin_users'));
        if (in_array($loged_user->id, $mastAdminUserIdsArr)) {
            $isMasterAdmin = true;
        }

        if ($roleId->role_id == config("app.admin_user_role_id") && $isMasterAdmin) {
            // dd($request->all());
            // dd($user);
            $user->update($request->all());
            $user->roles()->sync( config('app.admin_user_role_id'));
            $user->profile()->update($request->only('gender', 'mobile', 'dob', 'slug'));

            // Audit Log Entry
            $resultMessage = sprintf('Updated admin user: %s | Email: %s', $user->id, $user->email);
            Auditlog::info(Auditlog::CATEGORY_ADMIN_USER, $resultMessage, $user->id);

            return redirect()->route('admin.admin_users.index')->with('success', 'Successfully updated!');
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorize Access!');
        }
    }

    public function adminDestroy($id)
    {
        $user = auth()->user();
        $roleId = RoleUser::where('user_id', $user->id)->first();

        $isMasterAdmin = false;
        $mastAdminUserIdsArr = explode(',', config('app.master_admin_users'));
        if (in_array($user->id, $mastAdminUserIdsArr)) {
            $isMasterAdmin = true;
        }

        if ($roleId->role_id == config("app.admin_user_role_id") && $isMasterAdmin) {
            $profile = User::find($id);
            $profile->delete();

            // Audit Log Entry
            $resultMessage = sprintf('Deleted admin user: %s | Email: %s', $profile->id, $profile->email);
            Auditlog::critical(Auditlog::CATEGORY_ADMIN_USER, $resultMessage, $profile->id);

            return back()->with('success', 'Successfully Deleted!');
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorize Access!');
        }
    }

    public function adminRestore($id)
    {
        $user = auth()->user();
        $roleId = RoleUser::where('user_id', $user->id)->first();

        $isMasterAdmin = false;
        $mastAdminUserIdsArr = explode(',', config('app.master_admin_users'));
        if (in_array($user->id, $mastAdminUserIdsArr)) {
            $isMasterAdmin = true;
        }

        if ($roleId->role_id == config("app.admin_user_role_id") && $isMasterAdmin) {
            $profile = User::withTrashed()->find($id);
            if ($profile) {
                $profile->restore();

                // Audit Log Entry
                $resultMessage = sprintf('Restored admin user: %s | Email: %s', $profile->id, $profile->email);
                Auditlog::warning(Auditlog::CATEGORY_ADMIN_USER, $resultMessage, $profile->id);

                return redirect()->route('admin.admin_users.index')->with('success', 'User Successfully restored!');
            } else {
                return redirect()->route('admin.admin_users.index')->with('error', 'User not found!');
            }
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorize Access!');
        }
    }

    public function adminViewLogIndex($id)
    {
        $user = auth()->user();
        $roleId = RoleUser::where('user_id', $user->id)->first();

        $isMasterAdmin = false;
        $mastAdminUserIdsArr = explode(',', config('app.master_admin_users'));
        if (in_array($user->id, $mastAdminUserIdsArr)) {
            $isMasterAdmin = true;
        }

        if ($roleId->role_id == config("app.admin_user_role_id") && $isMasterAdmin) {
            $profile = User::find($id);
            $auditlog = AuditLog::select(
                'audit_logs.id',
                'audit_logs.user_id',
                'audit_logs.created_at',
                'audit_logs.ip_address',
                'audit_logs.severity',
                'audit_logs.category',
                'audit_logs.activity',
                'audit_logs.target_id',
                'audit_logs.data',
                'users.id as target_user_id',
                'users.name as target_user_name',
                'users.email as target_user_email',
            )
                ->leftjoin('users', 'audit_logs.target_id', 'users.id')
                ->where('audit_logs.user_id', $profile->id)
                ->orderby('audit_logs.created_at', 'DESC')
                ->get();

            return view('admin.users.admin_view_log_index', compact('profile', 'auditlog'));
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorize Access!');
        }
    }

    public function adminAllLogIndex()
    {
        $user = auth()->user();
        $roleId = RoleUser::where('user_id', $user->id)->first();

        $isMasterAdmin = false;
        $mastAdminUserIdsArr = explode(',', config('app.master_admin_users'));
        if (in_array($user->id, $mastAdminUserIdsArr)) {
            $isMasterAdmin = true;
        }

        if ($roleId->role_id == config("app.admin_user_role_id") && $isMasterAdmin) {
            $auditlog = AuditLog::select(
                'audit_logs.id',
                'audit_logs.user_id',
                'audit_logs.created_at',
                'audit_logs.ip_address',
                'audit_logs.severity',
                'audit_logs.category',
                'audit_logs.activity',
                'audit_logs.target_id',
                'audit_logs.data',
                'users.id as target_user_id',
                'users.name as target_user_name',
                'users.email as target_user_email',
                'admin_user.id as admin_user_id',
                'admin_user.name as admin_user_name',
                'admin_user.email as admin_user_email',
            )
                ->leftjoin('users', 'audit_logs.target_id', 'users.id')
                ->leftjoin('users as admin_user', 'audit_logs.user_id', 'admin_user.id')
                ->orderby('audit_logs.created_at', 'DESC')
                ->get();

            return view('admin.users.admin_all_log_index', compact('auditlog'));
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorize Access!');
        }
    }



    public function resetpassword()
    {
        return view('member.users.resetpassword');
    }

    public function updatepassword(Request $request, User $user, $id)
    {

        //echo $id;exit;
        $password = Hash::make($request->password);
        User::where('id', $id)->update(array('password' => $password));

        return redirect()->route('calendar.index', session()->get('website_slug'))->with('success', 'Successfully updated! Please Logout and Login Again');
    }

    public function storeReview(Request $request){
        $data = array();

        $user = auth()->user();
        $user_id = $user->id;

        $itemid = '';
        if ($request->itemid) {
            $itemid = $request->itemid;
        }     
        $itemtype = '';
        if ($request->itemtype) {
            $itemtype = $request->itemtype;
        }  
        $itemwebsite = '';
        if ($request->itemwebsite) {
            $itemwebsite = $request->itemwebsite;
        }  
        $notes = '';
        if ($request->notes) {
            $notes = $request->notes;
        }  

        $review = Review::create([
            'user_id' => $user_id,
            'type' => $itemtype,
            'website' => $itemwebsite,
            'item_id' => $itemid,
            'notes' => $notes,
        ]);

        // dd($post);
        $review->save();        

        if($review->id){
            $data['message'] = "Review stored successfully!";
        }else{
            $data['message'] = "Error in storing review!";
        }

        return response()->json($data);
    }

    public function raiseRequest()
    {
        return view('member.users.raiseRequest');
    }

    public function sendRequest(Request $request)
    {
        $user = auth()->user();

        $name = $user->name;
        $email= $user->email;

        $comments = "";
        if ($request->comments) {
            $comments = $request->comments;
        }  

        $userEmails = config('app.user_request_email_send_to');
        $userEmailsArr = explode(",",$userEmails);
        $subject = config('app.request_email_subject');

        $reportContent = "User Name: ".$name;
        $reportContent .= "<br>User Email: ".$email;
        $reportContent .= "<br>Request Content: ".$comments;

        foreach($userEmailsArr as $key => $email){
            $toName = $toEmail = $email;           
            $data = array();
            $data["app_name"] = config('app.name');
            $data["subject"] = $subject;
            $data["template_id"] = config('app.sendgrid_user_request_template');
            $data["report_content"] = $reportContent;
            UserEmailService::sendEmail($toEmail, $toName, $data);
        }

        return redirect()->route('calendar.index',session()->get('website_slug'))->with('success', 'Your request has been submitted successfully!');
    }    
}
