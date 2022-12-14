@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('Change Password') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('user.tts') }}"><i class="mdi mdi-account-settings-variant mr-2 fs-12"></i>{{ __('User') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ url('#') }}"> {{ __('My Profile Settings') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ route('user.password') }}"> {{ __('Change Password') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER-->
@endsection

@section('content')
	<!-- CHANGE PASSWORD -->
	<div class="row"> 
		<div class="col-xl-3 col-lg-4">
			<div class="card border-0">
				<div class="card-header">
					<h3 class="card-title">{{ __('Edit Password') }}</h3>
				</div>
				<div class="card-body">
					<div class="text-center mb-5">
						<div class="widget-user-image overflow-hidden">
							<img alt="User Avatar" class="rounded-circle mr-3" src="@if(auth()->user()->profile_photo_path){{ asset(auth()->user()->profile_photo_path) }} @else {{ URL::asset('img/users/avatar.jpg') }} @endif">
						</div>
						<h4 class="mb-1 mt-4 font-weight-bold fs-16">{{ auth()->user()->name }}</h4>
						<h6 class="text-muted fs-12">{{ __('Last Profile Update') }}: {{ auth()->user()->updated_at->diffForHumans() }}</h6>
					</div>
					<form method="POST" action="{{ route('user.password.update', [auth()->user()->id]) }}" enctype="multipart/form-data">

						@csrf

						@foreach ($errors->all() as $error)
                            <p class="text-danger">{{ $error }}</p>
                         @endforeach 

						<div class="input-box">	
							<div class="form-group">
								<label class="form-label fs-12">{{ __('Current Password') }}</label>
								<input type="password" name='current_password' class="form-control">
							</div>
						</div>
						<div class="input-box">
							<div class="form-group">
								<label class="form-label fs-12">{{ __('New Password') }}</label>
								<input type="password" name="new_password" class="form-control">
							</div>
						</div>
						<div class="input-box mb-0">
							<div class="form-group mb-0">
								<label class="form-label fs-12">{{ __('Confirm New Password') }}</label>
								<input type="password" name="new_confirm_password" class="form-control">
							</div>
						</div>
						<div class="card-footer border-0 text-right mt-2 pr-0 pb-0">
							<a href="{{ route('user.profile', [auth()->user()->id]) }}" class="btn btn-cancel mr-2">{{ __('Cancel') }}</a>
							<button type="submit" class="btn btn-primary">{{ __('Change') }}</button>							
						</div>
					</form>					
				</div>				
			</div>
		</div>
	</div>
	<!-- CHANGE PASSWORD -->
@endsection
