<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\LicenseController;
use Illuminate\Http\Request;
use App\Models\PrepaidPlan;
use DataTables;

class FinancePrepaidController extends Controller
{
    private $api;

    public function __construct()
    {
        $this->api = new LicenseController();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {        
        if ($request->ajax()) {
            $data = PrepaidPlan::all()->sortByDesc("created_at");          
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div class="dropdown">
                                            <button class="btn table-actions" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>                       
                                            </button>
                                            <div class="dropdown-menu table-actions-dropdown" role="menu" aria-labelledby="actions">
                                                <a class="dropdown-item" href="'. route("admin.finance.prepaid.show", $row["id"] ). '"><i class="fa fa-file"></i> View</a>
                                                <a class="dropdown-item" href="'. route("admin.finance.prepaid.edit", $row["id"] ). '"><i class="fa fa-pencil"></i> Edit</a>
                                                <a class="dropdown-item" data-toggle="modal" id="deleteSubscriptionButton" data-target="#deleteModal" href="" data-attr="'. route("admin.finance.prepaid.delete", $row["id"] ). '"><i class="fa fa-trash"></i> Delete</a>
                                            </div>
                                        </div>';
                        return $actionBtn;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-plan-type', function($row){
                        return '<span class="cell-box payment-'.strtolower($row["plan_type"]).'">'.ucfirst($row["plan_type"]).'</span>';
                    })
                    ->addColumn('custom-status', function($row){
                        $custom_priority = '<span class="cell-box plan-'.strtolower($row["status"]).'">'.ucfirst($row["status"]).'</span>';
                        return $custom_priority;
                    })
                    ->addColumn('custom-cost', function($row){
                        $custom_priority = $row["cost"] . ' ' . $row["currency"];
                        return $custom_priority;
                    })
                    ->rawColumns(['actions', 'custom-status', 'created-on', 'custom-plan-type', 'custom-cost'])
                    ->make(true);
                    
        }

        return view('admin.finance-management.prepaid.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.finance-management.prepaid.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        request()->validate([
            'plan-type' => 'required',
            'plan-status' => 'required',
            'plan-name' => 'required',
            'cost' => 'required|numeric',
            'currency' => 'required',
            'characters' => 'required|integer',
            'bonus' => 'nullable|integer'
        ]);

        $frequency = 'One Time Payment';

        $plan = new PrepaidPlan([
            'plan_type' => request('plan-type'),
            'status' => request('plan-status'),
            'plan_name' => request('plan-name'),
            'cost' => request('cost'),
            'currency' => request('currency'),
            'characters' => request('characters'),
            'bonus' => request('bonus'),
            'payment_frequency' => $frequency,
        ]); 
               
        $plan->save();            

        return redirect()->route('admin.finance.prepaid')->with("success", "New prepaid plan has been created successfully");
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PrepaidPlan $id)
    {
        return view('admin.finance-management.prepaid.show', compact('id'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PrepaidPlan $id)
    {
        return view('admin.finance-management.prepaid.edit', compact('id'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PrepaidPlan $id)
    {       
        request()->validate([
            'plan-type' => 'required',
            'plan-status' => 'required',
            'plan-name' => 'required',
            'cost' => 'required|numeric',
            'currency' => 'required',
            'characters' => 'required|integer',
            'bonus' => 'nullable|integer'
        ]);

        $id->update([
            'plan_type' => request('plan-type'),
            'status' => request('plan-status'),
            'plan_name' => request('plan-name'),
            'cost' => request('cost'),
            'currency' => request('currency'),
            'characters' => request('characters'),
            'bonus' => request('bonus'),
        ]); 
           

        return redirect()->route('admin.finance.prepaid')->with("success", "Selected prepaid plan has been updated successfully");
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $plan = PrepaidPlan::where('id', $id)->firstOrFail();

        if($plan) {
            $plan->delete();
        }
    
        return redirect()->route('admin.finance.prepaid')->with('success', 'Selected prepaid plan was deleted successfully');          
    }


    public function delete($id)
    {   
        $plan = PrepaidPlan::where('id', $id)->firstOrFail();

        return view('admin.finance-management.prepaid.delete', compact('plan'));  
    }
}
