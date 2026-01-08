<?php

namespace App\Models;

use Carbon\Carbon;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\UserVerifyEmail;
use App\Models\RoleUser;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Traits\HasFormBuilderTraits;

class User extends Authenticatable  implements JWTSubject
{
    use SoftDeletes, Notifiable;

    use HasFormBuilderTraits;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'email_verified_at',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'email_verified_at',
        'parent_id',
        'api_token',
        'status',
        'user_type',
        'email_verification',
        'mobile'
    ];

     /**
     * Roles
     */
    const ROLE_USER = 'User';
    const ROLE_MODERATOR = 'Team Coordinator';
    const ROLE_ADMIN = 'Web Admin';

    /**
     * Status
     */
    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';
    const STATUS_NOT_APPROVED = 'Not Approved';

    public static $STATUS_LABELS = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_NOT_APPROVED => 'Not Approved'
    ];


    /**
     * user_type
     */
    const TYPE_EXTERNAL = 'External';
    const TYPE_INTERNAL = 'Internal';

    public static $TYPE_LABELS = [
        self::TYPE_EXTERNAL => 'External',
        self::TYPE_INTERNAL => 'Internal',
    ];

    public function verifyEmail()
    {
        return $this->hasMany(UserVerifyEmail::class);
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function generateToken()
    {
        $this->api_token = str_random(60);
        $this->save();
        return $this->api_token;
    }
    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function roleuser()
    {
        return $this->belongsToMany(RoleUser::class);
    }
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }
    public function association()
    {
        return $this->hasOne(UserAssociation::class);
    }    
    public function userToUserAssociation()
    {
        return $this->hasMany(UserToUserAssociation::class,'head_user_id');
    }     
    public function UserModelPermissionAssociation()
    {
        return $this->hasMany(UserModelPermissionAssociation::class,'user_id');
    }            
    public function getAssociations(){

        $userAssociation = array();
        $userAssociation = UserAssociation::
        select(
            DB::raw('GROUP_CONCAT(DISTINCT(user_associations.team_id)) AS all_team_ids'),
            DB::raw('GROUP_CONCAT(DISTINCT(user_associations.module_id)) AS all_module_ids'),
            DB::raw('GROUP_CONCAT(DISTINCT(user_associations.chapter_id)) AS all_chapter_ids')           
        )
        ->leftjoin('teams','teams.id','user_associations.team_id')
        ->leftjoin('modules','modules.id','user_associations.module_id')
        ->leftjoin('chapters','chapters.id','user_associations.chapter_id')
        ->where('user_associations.user_id',$this->id);

        // $userAssociation = $userAssociation->toSql();
        // dd($userAssociation);

        $userAssociation = $userAssociation->get()->toArray();
        return $userAssociation;
    }    

    public function isAdmin()
    {
        // return $this->hasRole(User::ROLE_ADMIN);
        $user = auth()->user();
        $roleId = RoleUser::where('user_id',$user->id)->first();
        if($roleId->role_id == 1){
            return true;
        }else{
            return false;
        }

    }  
    
    public function isViewer()
    {
        // return $this->hasRole(User::ROLE_ADMIN);
        $user = auth()->user();
        $roleId = RoleUser::where('user_id',$user->id)->first();
        if($roleId->role_id == config('app.viewer_user_role_id')){
            return true;
        }else{
            return false;
        }

    }      

    // module management check
    public function isModuleAssigned($module)
    {

        if(auth::user()->isAdmin()){
            return true;
        }else{
            $user = auth()->user();
            $moduleAssigned = UserModelPermissionAssociation::
            join('model_permissions','model_permissions.id','user_model_permission_associations.model_permission_id')
            ->where('user_id',$user->id)
            ->where('model_permissions.name',$module)
            ->first();
            if($moduleAssigned){
                return true;
            }else{
                return false;
            }
        }



    }      


    public function reviews()
    {
        return $this->hasMany(Review::class,'user_id');
    }       
}
