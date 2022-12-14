<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\LicenseController;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Services\Statistics\UserUsageYearlyService;
use App\Services\Statistics\UserPaymentsService;
use App\Services\Statistics\UserRegistrationYearlyService;
use App\Services\Statistics\UserRegistrationMonthlyService;
use App\Models\Plan;
use App\Models\User;
use DataTables;
use Active;


class AdminUserController extends Controller
{
    private $api;

    public function __construct()
    {
        $this->api = new LicenseController();
    }

    /**
     * Display user management dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));

        $registration_yearly = new UserRegistrationYearlyService($year);
        $registration_monthly = new UserRegistrationMonthlyService($year, $month);

        $user_data_year = [
            'total_free_tier' => $registration_yearly->getTotalFreeRegistrations(),
            'total_paid_tier' => $registration_yearly->getTotalPaidRegistrations(),
            'total_users' => $registration_yearly->getTotalUsers(),
            'total_paid_users' => $registration_yearly->getTotalPaidUsers(),
            'top_countries' => $this->getTopCountries(),
        ];
        
        $chart_data['free_registration_yearly'] = json_encode($registration_yearly->getFreeRegistrations());
        $chart_data['paid_registration_yearly'] = json_encode($registration_yearly->getPaidRegistrations());
        $chart_data['current_registered_users'] = json_encode($registration_monthly->getRegisteredUsers());
        $chart_data['user_countries'] = json_encode($this->getAllCountries());

        $users = Active::users()->count();
        $users_today = Active::usersWithinHours(24)->count();

        return view('admin.user-management.dashboard.index', compact('chart_data', 'user_data_year', 'users', 'users_today'));
    }


    /**
     * Display all users
     *
     * @return \Illuminate\Http\Response
     */
    public function listUsers(Request $request)
    {  
        if ($request->ajax()) {
            $data = User::latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div class="dropdown">
                                            <button class="btn table-actions" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>                       
                                            </button>
                                            <div class="dropdown-menu table-actions-dropdown" role="menu" aria-labelledby="actions">
                                                <a class="dropdown-item" href="'. route("admin.user.characters", $row["id"] ). '"><i class="fa-solid fa-wand-sparkles"></i> Add Credits</a>
                                                <a class="dropdown-item" href="'. route("admin.user.show", $row["id"] ). '"><i class="mdi mdi-account-check"></i> View</a>
                                                <a class="dropdown-item" href="'. route("admin.user.edit", $row["id"] ). '"><i class="mdi mdi-account-edit"></i> Edit</a>
                                                <a class="dropdown-item" data-toggle="modal" id="deleteUserButton" data-target="#deleteModal" href="" data-attr="'. route("admin.user.delete", $row["id"] ). '"><i class="mdi mdi-account-off"></i> Delete</a>
                                            </div>
                                        </div>';
                        return $actionBtn;
                    })
                    ->addColumn('avatar', function($row){
                        if ($row['profile_photo_path']) {
                            $path = asset($row['profile_photo_path']);
                        } else {
                            $path = URL::asset('img/users/avatar.jpg');
                        }

                        $avatar = '<div class="widget-user-image-sm overflow-hidden"><img alt="User Avatar" class="rounded-circle" src="' . $path . '"></div>';
                        return $avatar;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-status', function($row){
                        $custom_status = '<span class="cell-box user-'.$row["status"].'">'.ucfirst($row["status"]).'</span>';
                        return $custom_status;
                    })
                    ->addColumn('custom-group', function($row){
                        $custom_group = '<span class="cell-box user-group-'.$row["group"].'">'.ucfirst($row["group"]).'</span>';
                        return $custom_group;
                    })
                    ->addColumn('custom-email', function($row){
                        $custom_email = '<span class="text-info">'.$row["email"].'</span>';
                        return $custom_email;
                    })
                    ->addColumn('custom-balance', function($row){
                        $custom_group = config('payment.default_system_currency_symbol') . $row["balance"];
                        return $custom_group;
                    })
                    ->addColumn('custom-chars', function($row){
                        $custom_chars = number_format($row["available_chars"]);
                        return $custom_chars;
                    })
                    ->rawColumns(['actions', 'custom-status', 'custom-group', 'created-on', 'avatar', 'custom-chars', 'custom-balance', 'custom-email'])
                    ->make(true);                    
        }

        return view('admin.user-management.list.index');
    }


    /**
     * Display user activity
     *
     * @return \Illuminate\Http\Response
     */
    public function activity(Request $request)
    {
        $result = DB::table('sessions')
                ->join('users', 'sessions.user_id', '=', 'users.id')
                ->whereNotNull('sessions.user_id')
                ->select('sessions.ip_address', 'sessions.user_agent', 'sessions.last_activity', 'users.email', 'users.group')
                ->orderBy('sessions.last_activity', 'desc')
                ->get()->toArray();

        return view('admin.user-management.activity.index', compact('result'));
    }

    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user-management.list.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($this->api->api_url != 'https://license.berkine.space/') {
            return redirect()->back();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
            'role' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'country' => $request->country,
            'job_role' => $request->job_role,
            'phone_number' => $request->phone_number,
            'company' => $request->company,
            'website' => $request->website,
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
        ]);       
        
        $user->syncRoles($request->role);
        $user->status = 'active';
        $user->group = $request->role;
        $user->available_chars = config('tts.free_chars');
        $user->save();        

        return redirect()->back()->with('success', 'Congratulation! New user has been created');
    }


    /**
     * Display the details of selected user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, User $user)
    {   
        if ($this->api->api_url != 'https://license.berkine.space/') {
            return redirect()->back();
        }

        $year = $request->input('year', date('Y'));

        $usage_yearly = new UserUsageYearlyService($year);
        $payments_yearly = new UserPaymentsService($year);

        $user_data_year = [
            'total_standard_chars' => $usage_yearly->getTotalStandardCharsUsage($user->id),
            'total_neural_chars' => $usage_yearly->getTotalNeuralCharsUsage($user->id),
            'total_audio_files' => $usage_yearly->getTotalAudioFiles($user->id),
            'total_listen_modes' => $usage_yearly->getTotalListenModes($user->id),
            'total_payments' => $payments_yearly->getTotalPayments($user->id)
        ];
        
        $chart_data['standard_chars'] = json_encode($usage_yearly->getStandardCharsUsage($user->id));
        $chart_data['neural_chars'] = json_encode($usage_yearly->getNeuralCharsUsage($user->id));
        $chart_data['payments'] = json_encode($payments_yearly->getPayments($user->id));        

        return view('admin.user-management.list.show', compact('user', 'chart_data', 'user_data_year'));
    }


    /**
     * Show the form for editing the specified user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.user-management.list.edit', compact('user'));
    }


    /**
     * Show users available characters
     */
    public function characters(User $user)
    {
        return view('admin.user-management.list.characters', compact('user'));
    }


    /**
     * Add new characters to a user
     */
    public function increase(Request $request, User $user)
    {
        if ($this->api->api_url != 'https://license.berkine.space/') {
            return redirect()->back();
        }

        $request->validate([
            'characters' => 'required|integer',
        ]);

        $total = $user->available_chars + $request->characters;

        $user->available_chars = $total;
        $user->save();

        return redirect()->back()->with('success', "New {$request->characters} characters were added to {$user->name}");
    }


    /**
     * Update selected user data
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(User $user)
    {
        if ($this->api->api_url != 'https://license.berkine.space/') {
            return redirect()->back();
        }

        $user->update(request()->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','string','email','max:255',Rule::unique('users')->ignore($user)],
            'job_role' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'phone_number' => 'nullable|max:20',
            'address' => 'nullable|string|max:255',            
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'string|max:255',
        ]));

        return redirect()->back()->with('success','User profile was successfully updated');
    }

    /**
     * Change user group/status/password
     */
    public function change(Request $request, User $user)
    {
        if ($this->api->api_url != 'https://license.berkine.space/') {
            return redirect()->back();
        }
        
        $request->validate([
            'password' => ['nullable', 'confirmed', Rules\Password::min(8)],
            'status' => 'required',
            'group' => 'required'
        ]);
        
		$user->removeRole($user->group);
        $user->assignRole($request->group);
        $user->status = $request->status;
        $user->group = $request->group;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();   

        return redirect()->back()->with('success', 'User data was successfully updated');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.user.list')->with('success', 'Selected user was deleted successfully');       
    }

    public function delete(User $user)
    {   
        return view('admin.user-management.list.delete', compact('user'));     
    }


    /**
     * Show list of all countries
     */
    public function getAllCountries()
    {        
        $countries = User::select(DB::raw("count(id) as data, country"))
                ->groupBy('country')
                ->orderBy('data')
                ->pluck('data', 'country');    
        
        return $countries;        
    }


    /**
     * Show top 30 countries
     */
    public function getTopCountries()
    {        
        $countries = User::select(DB::raw("count(id) as data, country"))
                ->groupBy('country')
                ->orderByDesc('data')
                ->pluck('data', 'country')
                ->take(30)
                ->toArray();    

        return $countries;        
    }

}   
