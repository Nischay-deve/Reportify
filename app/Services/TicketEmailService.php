<?php
namespace App\Services;

use Carbon\Carbon;
// use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketComment;
use SendGrid\Mail\Mail;

class TicketEmailService
{

    public static function sendEmail($toUserEmail, $toUserName, $data){
        $email = new Mail();
        $email->setFrom(config('app.sendgrid_from_email_for_ticket'), config('app.sendgrid_from_name_for_ticket'));
        $email->addTo(
            $toUserEmail,
            $toUserName,
            $data,
            0
        );
        $email->setTemplateId($data['template_id']);
        $sendgrid = new \SendGrid(config('app.sendgrid_api_key'));
        try {
            $response = $sendgrid->send($email);
            // print $response->statusCode() . "\n";
            // print_r($response->headers());
            // print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: '.  $e->getMessage(). "\n";
        }   
    }

    public static function sendEmailWithAttachment($toUserEmail, $toUserName, $data){
        $email = new Mail();
        $email->setFrom(config('app.sendgrid_from_email_for_ticket'), config('app.sendgrid_from_name_for_ticket'));
        $email->addTo(
            $toUserEmail,
            $toUserName,
            $data,
            0
        );
        
        $file_encoded = base64_encode(file_get_contents($data['file_path']));
        $email->addAttachment(
           $file_encoded,
           "text/plain",
           "report.txt",
           "attachment"
        );

        $email->setTemplateId($data['template_id']);
        $sendgrid = new \SendGrid(config('app.sendgrid_api_key'));
        try {
            $response = $sendgrid->send($email);
            // print $response->statusCode() . "\n";
            // print_r($response->headers());
            // print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: '.  $e->getMessage(). "\n";
        }   
    }    

    /**
     * Send ticket created email
     *
     * @param Ticket $ticket
     */
    public static function sendTicketInformation(Ticket $ticket, $onlyAdmin=false)
    {
        $ticket_id = $ticket->ticket_id;
        $viewTicketUrl = url('admin/ticket/show_ticket/' . $ticket->ticket_id);
        $subjectPostFix = "[Query ID: ".$ticket->ticket_id."] ".$ticket->title;

        $user = User::find($ticket->user_id);
        // $email = $user->email;
        // $userName = $user->name;

        if(!$onlyAdmin){    
            $data = array();
            $data["app_name"] = config('app.name');
            $data["subject"] = config('app.name').' - '.$subjectPostFix;
            $data["template_id"] = config('app.sendgrid_new_ticket_template');
            $data["name"] = $user->name;
            $data["email"] = $ticket->email_to_responsd_to;
            $data["ticket_id"] = $ticket->ticket_id;
            $data["ticket_title"] = $ticket->title;
            $data["ticket_status"] = $ticket->titlestatus;
            $data["link"] = $viewTicketUrl;
            self::sendEmail($ticket->email_to_responsd_to, $user->name, $data);                    
        }

        //send email to admin
        $adminEmail = env('TICKET_ADMIN_EMAIL');
        $adminName = env('TICKET_ADMIN_NAME');       
      
        $data = array();
        $data["app_name"] = config('app.name');
        $data["subject"] = config('app.name').' - '.$subjectPostFix;
        $data["template_id"] = config('app.sendgrid_new_ticket_admin_template');
        $data["name"] = $adminName;
        $data["email"] = $adminEmail;                
        $data["ticketByUserName"] = $user->name;
        $data["ticketByUserEmail"] = $user->email;
        $data["ticket_id"] = $ticket->ticket_id;
        $data["ticket_title"] = $ticket->title;
        $data["ticket_status"] = $ticket->titlestatus;
        $data["link"] = $viewTicketUrl;
        self::sendEmail($adminEmail, $adminName, $data);        
    }     

    /**
     * Send ticket comment created email
     *
     * @param Ticket $ticket
     */
    public static function sendTicketComment(Ticket $ticket, TicketComment $comment)
    {
        $ticket_id = $ticket->ticket_id;
        $viewTicketUrl = url('admin/ticket/show_ticket/' . $ticket->ticket_id);
        $subjectPostFix = "[Query ID: ".$ticket->ticket_id."] ".$ticket->title;

        $comment = $comment->comment;
        $ticket_status = $ticket->status;

        $user = User::find($ticket->user_id);
        // $email = $user->email;
        // $userName = $user->name;

        $data = array();
        $data["app_name"] = config('app.name');
        $data["subject"] = 'RE: '.config('app.name').' - '.$subjectPostFix;
        $data["template_id"] = config('app.sendgrid_ticket_comment_template');
        $data["name"] = $user->name;
        $data["email"] = $ticket->email_to_responsd_to;
        $data["ticket_id"] = $ticket_id;
        $data["ticket_title"] = $ticket->title;
        $data["ticket_status"] = $ticket->status;
        $data["link"] = $viewTicketUrl;
        $data["comments"] = nl2br($comment);
        self::sendEmail($ticket->email_to_responsd_to, $user->name, $data);                
    }      

    /**
     * Send ticket status change email
     *
     * @param Ticket $ticket
     */
    public static function sendTicketStatusNotificationEmail(Ticket $ticket)
    {
        $ticket_id = $ticket->ticket_id;
        $viewTicketUrl = url('admin/ticket/show_ticket/' . $ticket->ticket_id);
        $subjectPostFix = "[Query ID: ".$ticket->ticket_id."] ".$ticket->title;

        $ticket_status = $ticket->status;

        $user = User::find($ticket->user_id);
        // $email = $user->email;
        // $userName = $user->name;

        $data = array();
        $data["app_name"] = config('app.name');
        $data["subject"] = 'RE: '.config('app.name').' - '.$subjectPostFix;
        $data["template_id"] = config('app.sendgrid_ticket_notification_template');
        $data["name"] = $user->name;
        $data["email"] = $ticket->email_to_responsd_to;
        $data["ticket_id"] = $ticket_id;
        $data["ticket_title"] = $ticket->title;
        $data["ticket_status"] = $ticket->status;
        $data["link"] = $viewTicketUrl;
        self::sendEmail($ticket->email_to_responsd_to, $user->name, $data);         
    }     
}