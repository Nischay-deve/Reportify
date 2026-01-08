<?php
/*--------------------
https://github.com/jazmy/laravelformbuilder
Licensed under the GNU General Public License v3.0
Author: Jasmine Robinson (jazmy.com)
Last Updated: 12/29/2018
----------------------*/

namespace App\Traits;

use App\Models\Form;
use App\Models\Submission;

trait HasFormBuilderTraits
{
    /**
     * A User can have one or many forms
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function forms()
    {
        return $this->hasMany(Form::class);
    }

    /**
     * A User can have one or many submission
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function storeUserToFormList($user, $details)
    {
        $name = $user->name;
        $email = $user->email;
        $gender = "";
        $mobile = "";
        $dob = "";
        $occupation = "";
        $more_info = "";
        $reference = "";
        $reference_mobile = "";

        // dump($user);
        // dump($details);

        if(isset($details->gender)){
            $gender = $details->gender;
        }
        if(isset($details->mobile)){
            $mobile = $details->mobile;
        }
        if(isset($details->dob)){
            $dob = $details->dob;
        }
        if(isset($details->occupation)){
            $occupation = $details->occupation;
        }
        if(isset($details->moreinfo)){
            $more_info = $details->moreinfo;
        }
        if(isset($details->reference)){
            $reference = $details->reference;
        }
        if(isset($details->reference_mobile)){
            $reference_mobile = $details->reference_mobile;
        }

        echo "<BR>Processing  User ".$name." : ".$email;        

        $user_register_form_id = config('app.user_register_list_form_id');
        $user_register_form_identifier = config('app.user_register_list_form_identifier');
        $user_id_for_register_user_form = config('app.user_register_list_user_id');

        $form = Form::where('identifier', $user_register_form_identifier)->firstOrFail();
        if ($form) {

            $userData = array();
            $userData['submit_mode'] = "close";
            $userData[config('app.user_register_list_attribute_name')] = $name;
            $userData[config('app.user_register_list_attribute_email')] = $email;
            $userData[config('app.user_register_list_attribute_gender')] = $gender;
            $userData[config('app.user_register_list_attribute_mobile')] = $mobile;
            $userData[config('app.user_register_list_attribute_dob')] = $dob;
            $userData[config('app.user_register_list_attribute_occupation')] = $occupation;
            $userData[config('app.user_register_list_attribute_more_info')] = $more_info;
            $userData[config('app.user_register_list_attribute_reference')] = $reference;
            $userData[config('app.user_register_list_attribute_reference_mobile')] = $reference_mobile;
            
            $form->submissions()->create([
                'user_id' => $user_id_for_register_user_form,
                'content' => $userData,
            ]);
          
            echo "<BR>Insert Done <hr>";        
        }
    
    }
}
