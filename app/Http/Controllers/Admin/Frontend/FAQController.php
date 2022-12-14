<?php

namespace App\Http\Controllers\Admin\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;
use DataTables;

class FAQController extends Controller
{
    /**
     * Show appearance settings page
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Faq::all()->sortByDesc("created_at");
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div class="dropdown">
                                            <button class="btn table-actions" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>                       
                                            </button>
                                            <div class="dropdown-menu table-actions-dropdown" role="menu" aria-labelledby="actions">
                                                <a class="dropdown-item" href="'. route("admin.settings.faq.edit", $row["id"] ). '"><i class="fa fa-pencil-square-o"></i> Edit</a>
                                                <a class="dropdown-item" data-toggle="modal" id="deleteFAQButton" data-target="#deleteModal" href="" data-attr="'. route("admin.settings.faq.delete", $row["id"] ). '"><i class="fa fa-trash"></i> Delete</a>
                                            </div>
                                        </div>';
                        return $actionBtn;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-status', function($row){
                        $custom_status = '<span class="cell-box faq-'.strtolower($row["status"]).'">'.ucfirst($row["status"]).'</span>';
                        return $custom_status;
                    })
                    ->rawColumns(['actions', 'custom-status', 'created-on'])
                    ->make(true);
                    
        }

        return view('admin.frontend-management.faq.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.frontend-management.faq.create');
    }


    /**
     * Store new faq in database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'question' => 'required',
            'status' => 'required',
            'answer' => 'required',
        ]);      

        $faq = Faq::create([
            'question' => $request->question,
            'status' => $request->status,
            'answer' => $request->answer,
        ]);

        return redirect()->route('admin.settings.faq')->with('success', 'FAQ answer successfully created');
    }


    /**
     * Edit blog.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Faq $id)
    {
        return view('admin.frontend-management.faq.edit', compact('id'));
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
            'question' => 'required',
            'status' => 'required',
            'answer' => 'required',
        ]);

        $blog = Faq::where('id', $id)->firstOrFail();
        $blog->question = request('question');
        $blog->status = request('status');
        $blog->answer = request('answer');
        $blog->save();    

        return redirect()->route('admin.settings.faq')->with('success', 'FAQ answer successfully updated');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $blog = Faq::where('id', $id)->firstOrFail();  

        $blog->delete();

        return redirect()->route('admin.settings.faq')->with('success', 'Selected FAQ answer was deleted successfully');       
    }

    public function delete(Faq $id)
    {  
        return view('admin.frontend-management.faq.delete', compact('id'));     
    }

}
