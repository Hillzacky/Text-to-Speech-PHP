<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;


class SMTPController extends Controller
{
    /**
     * Display SMTP settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.settings.smtp.index');
    }


    /**
     * Store SMTP settings in env file
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        request()->validate([
            'smtp-host' => 'required',
            'smtp-port' => 'required',
            'smtp-username' => 'required',
            'smtp-password' => 'required',
            'smtp-encryption' => 'required',
        ]);

        $this->storeSettings('MAIL_HOST', request('smtp-host'));
        $this->storeSettings('MAIL_PORT', request('smtp-port'));
        $this->storeSettings('MAIL_USERNAME', request('smtp-username'));
        $this->storeSettings('MAIL_PASSWORD', request('smtp-password'));
        $this->storeSettings('MAIL_FROM_ADDRESS', request('smtp-from'));
        $this->storeSettings('MAIL_ENCRYPTION', request('smtp-encryption'));  

        if (config('mail.from.name') == '') {
            $newName = "'". request('smtp-name') . "'";
            $this->storeSettings('MAIL_FROM_NAME', $newName);
        } else {
            $newName = "'". request('smtp-name') . "'";
            $this->storeWithQuotes('MAIL_FROM_NAME', $newName);
        }

        return redirect()->back()->with('success', 'SMTP settings successfully updated');
    }


    /**
     * Send a test email
     */
    public function test()
    {
		try {
			
			Mail::send(array(), array(), function ($message) {
				$message->from(config('mail.from.address'), 'Test Email');
				$message->to(request('email'));
				$message->subject(request('subject'));
				$message->setBody(request('message'));
			});

			if (Mail::failures()) {
				return redirect()->back()->with('error', 'Test email failed');
			}
			
		} catch(\Exception $e) {
            return redirect()->back()->with('error', 'Your SMTP settings are not configured correctly yet: ' . $e->getMessage());
        } 

        return redirect()->back()->with('success', 'Test email successfully sent');
    }


    /**
     * Record in .env file
     */
    private function storeSettings($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {

            file_put_contents($path, str_replace(
                $key . '=' . env($key), $key . '=' . $value, file_get_contents($path)
            ));

        }
    }

    private function storeWithQuotes($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {

            file_put_contents($path, str_replace(
                $key . '=' . '\'' . env($key) . '\'', $key . '=' . $value, file_get_contents($path)
            ));

        }
    }

}
