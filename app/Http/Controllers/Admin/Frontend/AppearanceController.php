<?php

namespace App\Http\Controllers\Admin\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Setting;

class AppearanceController extends Controller
{
    /**
     * Show appearance settings page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $information_rows = ['title', 'author', 'keywords', 'description'];
        $information = [];
        $settings = Setting::all();

        foreach ($settings as $row) {
            if (in_array($row['name'], $information_rows)) {
                $information[$row['name']] = $row['value'];
            }
        }

        return view('admin.frontend-management.appearance.index', compact('information'));
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
            'title' => 'required',
        ]);

        $rows = ['title', 'author', 'keywords', 'description'];        
        foreach ($rows as $row) {
            Setting::where('name', $row)->update(['value' => $request->input($row)]);
        }

        if (request()->has('main_logo')) {

            try{
                request()->validate([
                    'main_logo' => 'nullable|image|mimes:png|max:5048'
                ]);

            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'PHP FileInfo: ' . $e->getMessage());
            }
            
            $image = request()->file('main_logo');
            $name = 'logo';         
            $folder = 'img/brand/';
            
            $this->uploadImage($image, $folder, 'public', $name);
        }

        if (request()->has('footer_logo')) {

            try {
                request()->validate([
                    'footer_logo' => 'nullable|image|mimes:png|max:5048'
                ]);

            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'PHP FileInfo: ' . $e->getMessage());
            }
            
            $image = request()->file('footer_logo');
            $name = 'logo-white';         
            $folder = 'img/brand/';
            
            $this->uploadImage($image, $folder, 'public', $name);
        }

        if (request()->has('minimized_logo')) {

            try {
                request()->validate([
                    'minimized_logo' => 'nullable|image|mimes:png|max:5048'
                ]);

            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'PHP FileInfo: ' . $e->getMessage());
            }
            
            $image = request()->file('minimized_logo');
            $name = 'favicon';         
            $folder = 'img/brand/';
            
            $this->uploadImage($image, $folder, 'public', $name);
        }

        if (request()->has('favicon_logo')) {
            
            try {
                request()->validate([
                    'favicon_logo' => 'nullable|mimes:ico|max:5048'
                ]);

            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'PHP FileInfo: ' . $e->getMessage());
            }
            
            $image = request()->file('favicon_logo');
            $name = 'favicon';         
            $folder = 'img/brand/';
            
            $this->uploadImage($image, $folder, 'public', $name);
        }

        return redirect()->back()->with('success', 'Appearance settings successfully updated');
    }


    /**
     * Upload logo images
     */
    public function uploadImage(UploadedFile $file, $folder = null, $disk = 'public', $filename = null)
    {
        $name = !is_null($filename) ? $filename : Str::random(5);

        $file->storeAs($folder, $name .'.'. $file->getClientOriginalExtension(), $disk);

    }

}
