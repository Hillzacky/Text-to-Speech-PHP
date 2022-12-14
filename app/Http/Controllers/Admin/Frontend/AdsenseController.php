<?php

namespace App\Http\Controllers\Admin\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Setting;

class AdsenseController extends Controller
{
    /**
     * Show appearance settings page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.frontend-management.adsense.index');
    }


    /**
     * Store appearance inputs properly in database and local storage
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'enable-adsense' => 'sometimes|required',
            'client-id' => 'required_if:enable-adsense,on',
        ]);

        $this->storeSettings('GOOGLE_ADSENSE_STATUS', request('enable-adsense'));
        $this->storeSettings('GOOGLE_ADSENSE_CLIENT_ID', request('client-id'));

        return redirect()->back()->with('success', 'Google AdSense settings successfully saved');
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
}
