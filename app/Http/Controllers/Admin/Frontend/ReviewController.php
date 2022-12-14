<?php

namespace App\Http\Controllers\Admin\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Review;
use DataTables;

class ReviewController extends Controller
{
    /**
     * Show appearance settings page
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Review::all()->sortByDesc("created_at");
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div class="dropdown">
                                            <button class="btn table-actions" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>                       
                                            </button>
                                            <div class="dropdown-menu table-actions-dropdown" role="menu" aria-labelledby="actions">
                                                <a class="dropdown-item" href="'. route("admin.settings.review.edit", $row["id"] ). '"><i class="fa fa-pencil-square-o"></i> Edit</a>
                                                <a class="dropdown-item" data-toggle="modal" id="deleteReviewButton" data-target="#deleteModal" href="" data-attr="'. route("admin.settings.review.delete", $row["id"] ). '"><i class="fa fa-trash"></i> Delete</a>
                                            </div>
                                        </div>';
                        return $actionBtn;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                        return $created_on;
                    })
                    ->rawColumns(['actions', 'created-on'])
                    ->make(true);
                    
        }

        return view('admin.frontend-management.review.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.frontend-management.review.create');
    }


    /**
     * Store review post properly in database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required',
            'position' => 'nullable',
            'text' => 'required',
        ]);

        if (request()->has('image')) {

            request()->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png,bmp,tiff,gif,svg,webp|max:10048'
            ]);
            
            $image = request()->file('image');
            $name = Str::random(10);         
            $folder = 'img/reviews/';
            
            $this->uploadImage($image, $folder, 'public', $name);

            $path = $folder . $name . '.' . request()->file('image')->getClientOriginalExtension();
        }  

        Review::create([
            'name' => $request->name,
            'position' => $request->position,
            'text' => $request->text,
            'image_url' => $path,
        ]);

        return redirect()->route('admin.settings.review')->with('success', 'Review successfully created');
    }


    /**
     * Edit review.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Review $id)
    {
        return view('admin.frontend-management.review.edit', compact('id'));
    }


    /**
     * Update review post properly in database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        request()->validate([
            'name' => 'required',
            'position' => 'nullable',
            'text' => 'required',
        ]);

        if (request()->has('image')) {

            request()->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png,bmp,tiff,gif,svg,webp|max:10048'
            ]);
            
            $image = request()->file('image');
            $name = Str::random(10);         
            $folder = 'img/reviews/';
            
            $this->uploadImage($image, $folder, 'public', $name);

            $path = $folder . $name . '.' . request()->file('image')->getClientOriginalExtension();

        } else {
            $path = '';
        }


        $review = Review::where('id', $id)->firstOrFail();
        $review->name = request('name');
        $review->image_url = ($path != '') ? $path : $review->image;
        $review->position = request('position');
        $review->text = request('text');
        $review->save();    

        return redirect()->route('admin.settings.review')->with('success', 'Review successfully updated');
    }


    /**
     * Upload logo images
     */
    public function uploadImage(UploadedFile $file, $folder = null, $disk = 'public', $filename = null)
    {
        $name = !is_null($filename) ? $filename : Str::random(5);

        $file->storeAs($folder, $name .'.'. $file->getClientOriginalExtension(), $disk);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $review = Review::where('id', $id)->firstOrFail();  

        $review->delete();

        return redirect()->route('admin.settings.review')->with('success', 'Selected review was deleted successfully');       
    }

    public function delete(Review $id)
    {  
        return view('admin.frontend-management.review.delete', compact('id'));     
    }

}
