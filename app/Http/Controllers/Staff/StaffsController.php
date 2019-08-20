<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StaffsController extends Controller
{
    private $page_title;

    public function __construct()
    {
        // you can protect this controller from executing if attempted activity is performed by unauthorized personnel
        $this->middleware('auth:staff');

        // If personnel is authorized, then we can set any info that will be available throughout the controller like staff_id
        $this->middleware(function ($request, $next) {
            $this->skey = 'STAFF' . Auth::id();
            return $next($request);
        });

        $this->page_title = __('Import Contacts');
    }

    /**
     * Show the page where the staff can upload csv files of contacts.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        //setup an empty array
        $records = [];

        //path where the csv files are stored
        $path = base_path('resources/pendingbatch');

        //loop over each file
        foreach (glob($path.'/*.csv') as $file) {

            //open the file and add the total number of lines to the records array
            $file = new \SplFileObject($file, 'r');
            $file->seek(PHP_INT_MAX);
            $records[] = $file->key();
        }

        //now sum all the array keys together to get the total
        $import_remainder = array_sum($records);

        return view('staff.contacts.index', compact('import_remainder'));
    }

    /**
     * uploads the csv file and split rows into individual csv's in batch of 10000
     * @param Request
     * @return
     * @throws \Illuminate\Validation\ValidationException
     */
    public function parseImport(Request $request)
    {
        request()->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        //get file from upload
        $path = request()->file('file')->getRealPath();

        //turn into array
        $file = file($path);

        //remove first line
        $data = array_slice($file, 1);

        //loop through file and split every 1000 lines
        $parts = (array_chunk($data, 1000));
        $i = 1;
        foreach($parts as $line) {
            $filename = base_path('resources/pendingbatch/'.date('y-m-d-H-i-s').$i.'.csv');
            file_put_contents($filename, $line);
            $i++;
        }

        session()->flash('status', 'queued for importing');

        return redirect("/staff/import");
    }

    /**
     * Sends a request to mailchimp to initiate a bulk email to subscribers
     * @param Request
     * @return
     */
    public function sendCampaign(Request $request)
    {
        //List ID from .env
        $listId = env('MAILCHIMP_LIST_ID');

        try{
            //Mailchimp instantiation with Key
            $mailchimp = new \Mailchimp(env('MAILCHIMP_KEY'));

            $options = [
                'list_id'   => $listId,
                'subject' => $request->input('subject'),
                'from_name' => env('MAIL_NAME'),
                'from_email' => env('MAIL_FROM'),
                'to_name' => 'NDA Subscribers'
            ];

            $content = [
                'html' => $request->input('message'),
                'text' => strip_tags($request->input('message'))
            ];
            //Create a Campaign $mailchimp->campaigns->create($type, $options, $content
            $campaign = $mailchimp->campaigns->create('regular', $options, $content);
            //Send campaign
            $mailchimp->campaigns->send($campaign['id']);

            return redirect()->back()->with('success','send campaign successfully');

        }catch(\Exception $e) {
            return redirect()->back()->with('error','Error from MailChimp');
        }
    }
}
