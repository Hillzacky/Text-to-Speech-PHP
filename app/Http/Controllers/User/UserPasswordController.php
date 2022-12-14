<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Rules\ValidateUserPasswordRule;
use App\Models\User;

class UserPasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user.profile.password.index');
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null)
    {
        $request->validate([
            'current_password' => ['required', new ValidateUserPasswordRule],
            'new_password' => ['required', Rules\Password::min(8)],
            'new_confirm_password' => ['required','same:new_password', Rules\Password::min(8)],
        ]);

        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
   
        return redirect()->back()->with('success','Password Successfully Updated');
    }
}


