<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditlog extends Model
{
    
    // CONSTANT - SEVERITY
    const SEVERITY_EMERGENCY = 0;

    const SEVERITY_ALERT = 1;

    const SEVERITY_CRITICAL = 2;

    const SEVERITY_ERROR = 3;

    const SEVERITY_WARNING = 4;

    const SEVERITY_NOTICE = 5;

    const SEVERITY_INFO = 6;

    const SEVERITY_DEBUG = 7;


    public static $SEVERITY_LABELS = [
        self::SEVERITY_EMERGENCY => 'Emergency',
        self::SEVERITY_ALERT => 'Alert',
        self::SEVERITY_CRITICAL => 'Critical',
        self::SEVERITY_ERROR => 'Error',
        self::SEVERITY_WARNING => 'Warning',
        self::SEVERITY_NOTICE => 'Notice',
        self::SEVERITY_INFO => 'Info',
        self::SEVERITY_DEBUG => 'Debug'
    ];

    // CONSTANT - CATEGORY
    const CATEGORY_GENERAL = 'General';
    const CATEGORY_REPORT = 'Report';    
    const CATEGORY_ROLE = 'Role';    
    const CATEGORY_USER = 'User';
    const CATEGORY_ADMIN_USER = 'Admin User';

    const CATEGORY_ISSUE = 'Issue';
    const CATEGORY_CATEGORY = 'Category';
    const CATEGORY_SUB_CATEGORY = 'Sub-Category';
    const CATEGORY_LOCAITON = 'Location';
    const CATEGORY_LOCATION_STATE = 'Location State';
    const CATEGORY_LANGUAGE = 'Language';
    const CATEGORY_SOURCE = 'Source';
    const CATEGORY_TEAMTAG = 'Team Tag';
    const CATEGORY_SMCAL = 'SM Calendar';    
    const CATEGORY_FAQ = 'Faq';    
    const CATEGORY_INCIDENCECAL = 'Incidence Calendar';   
    const CATEGORY_DOCUMENT = 'Documents';   
    const CATEGORY_SMCAL_MASTER = 'SM Calendar Master';   
    const CATEGORY_REVIEW = 'Review';


    // Data Input related

    /**
     *
     * @var string
     */
    protected $table = 'audit_logs';

    /**
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'ip_address',
        'severity',
        'category',
        'activity',
        'target_id',
        'data'
    ];

    /**
     * Relation - user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function search($start = 0, $tpp = 999, $orderBy = null, $search = null)
    {

        $tableName = ((new self)->getTable());
        $orderMap = [
            0 => $tableName . '.updated_at',
            1 => $tableName . '.severity',
            2 => $tableName . '.category',
            4 => $tableName . '.ip_address',
            5 => $tableName . '.user'
        ];

        $query = self::select($tableName . '.*', 'users.email AS user')
            ->leftJoin('users', $tableName . '.user_id', '=', 'users.id');


        if (isset($search) && is_object($search)) {
            // Open search
            if (isset($search->q)) {
                $query->where(function ($internalQuery) use ($search, $tableName) {
                    $internalQuery->where('users.email', 'like', '%' . $search->q . '%')
                        ->orWhere($tableName . '.category', 'like', '%' . $search->q . '%')
                        ->orWhere($tableName . '.activity', 'like', '%' . $search->q . '%')
                        ->orWhere($tableName . '.ip_address', 'like', '%' . $search->q . '%');
                });
            }
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $order) {
                $query->orderBy($orderMap[$order['column']], $order['dir']);
            }
        }

        return (object)[
            'ttl' => $query->get()->count(),
            'data' => $query->skip($start)->take($tpp)->get()
        ];
    }

    /**
     * Log web request activities
     *
     * @param integer $severity
     * @param string $category
     * @param string $activity
     * @param integer $targetId
     * @param string $data
     */
    static public function log($severity, $category, $activity = NULL, $targetId = NULL, $data = NULL)
    {
        $user = auth()->user();
        $roleId = RoleUser::where('user_id',$user->id)->first();

        if($roleId->role_id == 1){              
        
            $ipaddress = '127.0.0.1';
            if (! empty($_SERVER) && ! empty($_SERVER['REMOTE_ADDR'])) {
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            }
            $log = [
                'user_id' => $user->id,
                'ip_address' => $ipaddress,
                'severity' => $severity,
                'category' => $category,
                'activity' => $activity,
                'target_id' => $targetId,
                'data' => $data
            ];
            self::create($log);
        }
    }

    static public function info($category, $activity = NULL, $targetId = NULL, $data = NULL)
    {
        self::log(self::SEVERITY_INFO, $category, $activity, $targetId, $data);
    }

    static public function warning($category, $activity = NULL, $targetId = NULL, $data = NULL)
    {
        self::log(self::SEVERITY_WARNING, $category, $activity, $targetId, $data);
    }

    static public function critical($category, $activity = NULL, $targetId = NULL, $data = NULL)
    {
        self::log(self::SEVERITY_CRITICAL, $category, $activity, $targetId, $data);
    }  
}
