<?php
namespace App\Http\View\Composers;
use Illuminate\View\View;
use App\Models\User;
use App\Models\RoleUser;
use App\Models\Role;
use App\Models\Team;
use App\Models\UserProfile;
use App\Models\UserAssociation;
use DB;

class CommonComposer
{
    function __construct()
    {
    }

    public function compose(View $view)
    {
        $user = auth()->user();

        $isMasterAdmin = false;
        
        if($user){
            // $roleId = RoleUser::where('user_id',$user->id)->first();
            // $role = Role::where('id',$roleId->role_id)->first();

            // $role_id = $roleId->role_id;            
            // $role_name = $role->title;

            $role_id = 1;
            $team_id = "";

            $role_name = "Guest";

            $isMasterAdmin = false;
            // $mastAdminUserIdsArr = explode(',',config('app.master_admin_users'));

            // if(in_array($user->id, $mastAdminUserIdsArr)){
            //     $isMasterAdmin = true;
            // }
         
        } else {
            $user = array();

            $role_id = 1;
            $team_id = "";

            $role_name = "Guest";
        }

        $websites = array();
        // dump(session()->get('website_id')." : ".session()->get('website_slug'));
        $websiteShareApproved = array(session()->get('website_id'));
        if(session()->get('website_id')){
            $websiteShareApprovedData = DB::connection('setfacts')->select("select website_id from website_shares where approved=1 and show_data_in_database_search=1 and requested_by_website_id in (".session()->get('website_id').");");
            // dump("websiteShareApprovedData = ", $websiteShareApprovedData);
            foreach($websiteShareApprovedData as $data){
                $websiteShareApproved[] = $data->website_id;
            }

            if(count($websiteShareApproved) > 0){
                $ids = implode(",",$websiteShareApproved);
                // dump($ids);
                $websites = DB::connection('setfacts')->select("select * from websites where active=1 and id in (".$ids.") ;");
            }
        }

        // dump($websites);
        $website_slug = session()->get('website_slug');
        // dd($website_slug);

        $view->with(['role_id'=>$role_id, 'user'=>$user, 'role_name'=>$role_name, 'isMasterAdmin' => $isMasterAdmin, 'websites'=>$websites,'website_slug'=>$website_slug ]);

    }
}

?>
