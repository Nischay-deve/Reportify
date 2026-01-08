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
use App\Traits\CommonMethodsTraits;


class SMReportSearchFormComposer
{
    use CommonMethodsTraits;
    protected $connection = 'setfacts';

    public function compose(View $view)
    {
        $websiteIds = $this->getWebsiteIdsFromSessionForDatabaseSearch();
        // dump($websiteIds);

        $team = DB::connection('setfacts')->select(DB::raw("SELECT teams.id, teams.name , teams.slug, COUNT(reports.id) AS total_reports FROM `teams` 
        INNER JOIN `reports` ON `reports`.`team_id` = `teams`.`id` where teams.active=1 and teams.website_id in (".$websiteIds.") and teams.is_public=1 GROUP BY `teams`.`id`"));

        $teamId = "";

        $view->with(['sm_team' => $team, 'sm_teamId' => $teamId]);
    }
}
