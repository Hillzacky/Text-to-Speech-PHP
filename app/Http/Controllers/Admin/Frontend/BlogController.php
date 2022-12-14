<?php

namespace App\Http\Controllers\Admin\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Blog;
use DataTables;

class BlogController extends Controller
{
    /**
     * Show appearance settings page
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Blog::all()->sortByDesc("created_at");
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div class="dropdown">
                                            <button class="btn table-actions" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>                       
                                            </button>
                                            <div class="dropdown-menu table-actions-dropdown" role="menu" aria-labelledby="actions">
                                                <a class="dropdown-item" href="'. route("admin.settings.blog.edit", $row["id"] ). '"><i class="fa fa-pencil-square-o"></i> Edit</a>
                                                <a class="dropdown-item" data-toggle="modal" id="deleteBlogButton" data-target="#deleteModal" href="" data-attr="'. route("admin.settings.blog.delete", $row["id"] ). '"><i class="fa fa-trash"></i> Delete</a>
                                            </div>
                                        </div>';
                        return $actionBtn;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-status', function($row){
                        $custom_status = '<span class="cell-box blog-'.strtolower($row["status"]).'">'.ucfirst($row["status"]).'</span>';
                        return $custom_status;
                    })
                    ->rawColumns(['actions', 'custom-status', 'created-on'])
                    ->make(true);
                    
        }

        return view('admin.frontend-management.blog.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.frontend-management.blog.create');
    }


    /**
     * Store blog post properly in database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'title' => 'required',
            'status' => 'required',
            'image' => 'required',
            'content' => 'required',
        ]);

        if (request()->has('image')) {

            request()->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png,bmp,tiff,gif,svg,webp|max:10048'
            ]);
            
            $image = request()->file('image');
            $name = Str::random(10);         
            $folder = 'img/blogs/';
            
            $this->uploadImage($image, $folder, 'public', $name);

            $path = $folder . $name . '.' . request()->file('image')->getClientOriginalExtension();
        }

        if (request('url')) {
            $slug = request('url');
        } else {
            $slug = $this->slug(request('title'));
        }        

        $blog = Blog::create([
            'created_by' => auth()->user()->name,
            'title' => $request->title,
            'url' => $slug,
            'status' => $request->status,
            'keywords' => $request->keywords,
            'image' => $path,
            'body' => $request->content,
        ]);

        return redirect()->route('admin.settings.blog')->with('success', 'Blog successfully created');
    }


    /**
     * Edit blog.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $id)
    {
        return view('admin.frontend-management.blog.edit', compact('id'));
    }


    /**
     * Update blog post properly in database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        request()->validate([
            'title' => 'required',
            'status' => 'required',
            'content' => 'required',
        ]);

        if (request()->has('image')) {

            request()->validate([
                'image' => 'nullable|image|mimes:jpg,jpeg,png,bmp,tiff,gif,svg,webp|max:10048'
            ]);
            
            $image = request()->file('image');
            $name = Str::random(10);         
            $folder = 'img/blogs/';
            
            $this->uploadImage($image, $folder, 'public', $name);

            $path = $folder . $name . '.' . request()->file('image')->getClientOriginalExtension();

        } else {
            $path = '';
        }

        if (request('url')) {
            $slug = request('url');
        } else {
            $slug = '';
        } 

        $blog = Blog::where('id', $id)->firstOrFail();
        $blog->title = request('title');
        $blog->url = ($slug != '') ? $slug : $blog->url;
        $blog->image = ($path != '') ? $path : $blog->image;
        $blog->status = request('status');
        $blog->keywords = request('keywords');
        $blog->body = request('content');
        $blog->save();    

        return redirect()->route('admin.settings.blog')->with('success', 'Blog successfully updated');
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
        $blog = Blog::where('id', $id)->firstOrFail();  

        $blog->delete();

        return redirect()->route('admin.settings.blog')->with('success', 'Selected blog was deleted successfully');       
    }

    public function delete(Blog $id)
    {  
        return view('admin.frontend-management.blog.delete', compact('id'));     
    }


    /**
     * Create clean blog slug
     *
     */
    public function slug($text){ 

        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
      
        // trim
        $text = trim($text, '-');
      
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
      
        // lowercase
        $text = strtolower($text);
      
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
      
        if (empty($text))
        {
          return 'n-a';
        }
      
        return $text;
    }

}
