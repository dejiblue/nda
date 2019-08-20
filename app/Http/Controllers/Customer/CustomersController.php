<?php

namespace App\Http\Controllers\Customer;

use App\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Mail;

class CustomersController extends Controller
{
    //
    private $page_title;
    private $mailchimp;
    private $listId;

    public function __construct(\Mailchimp $mailchimp)
    {
        $this->mailchimp = $mailchimp;
        $this->listId = env('MAILCHIMP_LIST_ID');
        $this->page_title = __('Opt in');
    }

    /**
     * Show the page where customers can subscribe.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('customer.index');
    }

    /**
     * submits customer details to db and sends an acknowledgement email
     * Also subscribes users to mailing list as long as consent is given
     * @param Request
     * @return
     * @throws \Illuminate\Validation\ValidationException
     */
    public function optin(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'mobile' => 'min:10|max:12|numeric',
            'g-recaptcha-response' => 'required|captcha',
        ]);

        try{
            $rec = Contact::updateOrCreate(['email' => $request->email],
                [['first_name' => $request->first_name,'last_name' => $request->last_name,'province' => $request->province,
                    'mobile' => $request->mobile,'opt_in' => $request->opt_in,'ip_address' => $request->ip_address,
                    'user_agent' => $request->mobile
                ]]);
            if($rec){
                // Queue an email to be sent to the user
                $this->sendMail($rec);
                // if user choose to optin for marketing, then add them to our mailing list in mailchimp
                if($rec->opt_in == 1){
                    $this->subscribe($rec);
                }
            }
        } catch(Exception $e) {
            session()->flash('message', 'Something went wrong!');
            session()->flash('message_type', 'alert alert-danger');
            return redirect()->route('customers.index');
        }
        session()->flash('message', 'Successful');
        session()->flash('message_type', 'alert alert-success');
        return redirect()->back();
    }

    // This can alternatively be a helper function that can be utilized throughout the project
    public function subscribe($rec)
    {
        try {
            $this->mailchimp
                ->lists
                ->subscribe(
                    $this->listId,
                    ['email' => $rec->email],
                    null,
                    null,
                    false
                );
            $message = 'Email Subscribed successfully';
        } catch (\Mailchimp_List_AlreadySubscribed $e) {
            $message = 'Email is Already Subscribed';
        } catch (\Mailchimp_Error $e) {
            $message = 'Error from MailChimp';
        }
        return $message;
    }

    // This can alternatively be a helper function that can be utilized throughout the project
    public function sendMail($rec)
    {
        $title = "Welcome to National Debt Advisors";
        $content = "We are thrilled to have you on board";

        // this uses database queues for better performance
        Mail::queue('emails.acknowledgement', ['title' => $title, 'content' => $content], function ($message) use($rec)
        {
            $message->from(env('MAIL_FROM'));
            $message->to($rec->email);
            //Add a subject
            $message->subject("Thanks for your submission");
        });
    }
    
}
