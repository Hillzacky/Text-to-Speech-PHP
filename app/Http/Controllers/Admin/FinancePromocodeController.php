<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\LicenseController;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Gabievi\Promocodes\Facades\Promocodes;
use App\Models\Promocode;
use DataTables;

class FinancePromocodeController extends Controller
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
            $data = Promocode::all();          
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div class="dropdown">
                                            <button class="btn table-actions" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>                       
                                            </button>
                                            <div class="dropdown-menu table-actions-dropdown" role="menu" aria-labelledby="actions">
                                                <a class="dropdown-item" href="'. route("admin.finance.promocodes.show", $row["id"] ). '"><i class="fa fa-file"></i> View</a>
                                                <a class="dropdown-item" href="'. route("admin.finance.promocodes.edit", $row["id"] ). '"><i class="fa fa-pencil"></i> Edit</a>
                                                <a class="dropdown-item" data-toggle="modal" id="deleteSubscriptionButton" data-target="#deleteModal" href="" data-attr="'. route("admin.finance.promocodes.delete", $row["id"] ). '"><i class="fa fa-trash"></i> Delete</a>
                                            </div>
                                        </div>';
                        return $actionBtn;
                    })
                    ->addColumn('name', function($row){
                        $value = json_decode($row['data'], true);
                        $name = '<span>'.$value['name'].'</span>';
                        return $name;
                    })
                    ->addColumn('type', function($row){
                        $value = json_decode($row['data'], true);
                        $value_type = ($value['type'] == 'percentage') ? 'Percentage Discount' : 'Fixed Discount';
                        $type = '<span>'.$value_type.'</span>';
                        return $type;
                    })
                    ->addColumn('custom-status', function($row){
                        $value = json_decode($row['data'], true);
                        $custom_priority = '<span class="cell-box promocode-'.strtolower($value['status']).'">'.ucfirst($value['status']).'</span>';
                        return $custom_priority;
                    })
                    ->addColumn('usage', function($row){
                        $usage = ($row['is_disposable'] == 1) ? '<span>True</span>' : '<span>False</span>';
                        return $usage;
                    })
                    ->rawColumns(['actions', 'custom-status', 'name', 'type', 'usage'])
                    ->make(true);
                    
        }

        return view('admin.finance-management.promocodes.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        return view('admin.finance-management.promocodes.create');
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
            'promo-name' => 'required',
            'status' => 'required',
            'promo-type' => 'required',
            'discount' => 'required',
            'quantity' => 'required|integer',
            'valid-until' => 'required',
        ]);

        $date = date("Y-m-d H:i:s", strtotime(request('valid-until')));
        $valid_until = Carbon::createFromDate($date);
        $now = Carbon::now();
        $expires_in_days = $valid_until->diffInDays($now);

        $total_quantity = (request('usage') == 1) ? 1 : request('quantity');

        Promocodes::create(
            $amount = 1,
            $reward = request('discount'),
            $data = [
                "name" => request('promo-name'),
                "status" => request('status'),
                "type" => request('promo-type'),
            ],
            $expires_in = $expires_in_days,
            $quantity = $total_quantity,
            $is_disposable = request('usage'),            
        ); 
                         

        return redirect()->route('admin.finance.promocodes')->with("success", "New promocode has been created successfully");
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Promocode $id)
    {   
        $data = json_decode($id->data);

        return view('admin.finance-management.promocodes.show', compact('id', 'data'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Promocode $id)
    {
        $data = json_decode($id->data);

        return view('admin.finance-management.promocodes.edit', compact('id', 'data'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Promocode $id)
    {       
        request()->validate([
            'promo-name' => 'required',
            'status' => 'required',
            'promo-type' => 'required',
            'discount' => 'required',
            'quantity' => 'required|integer',
            'valid-until' => 'required',
        ]);

        $data = [
            'name' => request('promo-name'),
            'status' => request('status'),
            'type' => request('promo-type'),            
        ];

        $quantity = (request('usage') == 1) ? 1 : request('quantity');

        $id->update([
            'reward' => request('discount'),
            'quantity' => $quantity,
            'is_disposable' => request('usage'),
            'expires_at' => request('valid-until'),
            'data' => $data
        ]);

        return redirect()->route('admin.finance.promocodes')->with("success", "Selected promocode has been updated successfully");
    }


    /**
     * Clean all expired promocodes automatically
     *
     */
    public function clean()
    {   
        Promocodes::clearRedundant();

        return redirect()->back()->with('success', 'All expired promocodes are cleared successfully');  
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $promocode = Promocode::where('id', $id)->firstOrFail();

        if($promocode) {
            $promocode->delete();
        }
    
        return redirect()->route('admin.finance.promocodes')->with('success', 'Selected promocode was deleted successfully');          
    }


    public function delete($id)
    {   
        $promocode = Promocode::where('id', $id)->firstOrFail();

        return view('admin.finance-management.promocodes.delete', compact('promocode'));  
    }
}
