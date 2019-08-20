<?php

namespace App\Console\Commands;

use App\Contact;
use Illuminate\Console\Command;

class ImportContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:contacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import contacts from stored csv files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //set the path for the csv files
        $path = base_path("resources/pendingbatch/*.csv");

        //run 2 loops at a time
        foreach (array_slice(glob($path),0,2) as $file) {

            //read the data into an array
            $data = array_map('str_getcsv', file($file));

            //loop over the data
            foreach($data as $row) {

                //insert the record or update if the email already exists
                $rec = Contact::updateOrCreate([
                    'email' => $row[4],
                ], ['first_name' => $row[0],'last_name' => $row[1],'province' => $row[1],
                    'mobile' => $row[1],'email' => $row[4],'opt_in' => $row[5],
                    'ip_address' => $row[6],'user_agent' => $row[7]
                ]);

                // if user choose to optin for marketing, then add them to our mailing list in mailchimp
                if($rec->opt_in == 1){
                    $this->subscribe($rec);
                }
            }
            //delete the file
            unlink($file);
        }
    }

    // Subscribe users to mailchimp subscribers list
    public function subscribe($rec)
    {
        //List ID from .env
        $listId = env('MAILCHIMP_LIST_ID');
        try {
            //Mailchimp instantiation with Key
            $mailchimp = new \Mailchimp(env('MAILCHIMP_KEY'));
            
            $mailchimp->lists
                      ->subscribe(
                            $listId,
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
}
