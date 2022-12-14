@extends('layouts.app')

@section('css')
	<!-- Data Table CSS -->
	<link href="{{URL::asset('plugins/awselect/awselect.min.css')}}" rel="stylesheet" />
	<!-- Telephone Input CSS -->
	<link href="{{URL::asset('plugins/telephoneinput/telephoneinput.css')}}" rel="stylesheet" >
	<!-- Sweet Alert CSS -->
	<link href="{{URL::asset('plugins/sweetalert/sweetalert2.min.css')}}" rel="stylesheet" />
@endsection

@section('page-header')
	<!-- EDIT PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('Update Personal Information') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{route('user.tts')}}"><i class="mdi mdi-account-settings-variant mr-2 fs-12"></i>{{ __('User') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{url('#')}}"> {{ __('My Profile Settings') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('Edit Profile') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')
	<!-- EDIT USER PROFILE PAGE -->
	<div class="row">
		<div class="col-xl-3 col-lg-4 col-sm-12">
			<div class="card border-0">
				<div class="widget-user-image overflow-hidden mx-auto mt-5"><img alt="User Avatar" class="rounded-circle" src="@if(auth()->user()->profile_photo_path){{ asset(auth()->user()->profile_photo_path) }} @else {{ URL::asset('img/users/avatar.jpg') }} @endif"></div>
				<div class="card-body text-center">
					<div>
						<h4 class="mb-1 mt-1 font-weight-bold fs-16">{{ auth()->user()->name }}</h4>
						<h6 class="text-muted fs-12">{{ auth()->user()->job_role }}</h6>
						<a href="{{ route('user.profile') }}" class="btn btn-primary mt-3 mb-2">{{ __('View Profile') }}</a>
					</div>
				</div>
				<div class="card-footer p-0">
					<div class="row">
						<div class="col-sm-6 border-right text-center">
							<div class="p-4">
								<h5 class="mb-1 font-weight-bold text-dark number-font fs-14">${{ number_format(auth()->user()->balance) }}</h5>
								<span class="text-muted fs-12">{{ __('Current Balance') }}</span>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="text-center p-4">
								<h5 class="mb-1 font-weight-bold text-dark number-font fs-14">{{ number_format(auth()->user()->available_chars) }}</h5>
								<span class="text-muted fs-12">{{ __('Available Characters') }}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-9 col-lg-8 col-sm-12">
			<form method="POST" class="w-100" action="{{ route('user.profile.update', [auth()->user()->id]) }}" enctype="multipart/form-data">
				@method('PUT')
				@csrf

				<div class="card border-0">
					<div class="card-header">
						<h3 class="card-title">{{ __('Language & Voice') }}</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<!-- LANGUAGE -->
								<div class="input-box">	
									<h6>{{ __('Default Language') }}</h6>
									<select id="languages" name="language" data-placeholder="Select Default Language:" data-callback="language_select">			
										@foreach ($languages as $language)
											<option value="{{ $language->language_code }}" data-img="{{ URL::asset($language->language_flag) }}" @if (auth()->user()->language == $language->language_code) selected @endif> {{ $language->language }}</option>
										@endforeach
									</select>
								</div> <!-- END LANGUAGE -->
							</div>

							<div class="col-md-6 col-sm-12">
								<!-- VOICE -->
								<div class="input-box">	
									<h6>{{ __('Default Voice') }}</h6>
									<select id="voices" name="voice" data-placeholder="Select Default Voice:" data-callback="default_voice">			
										@foreach ($voices as $voice)
											<option value="{{ $voice->voice_id }}" 		
												@if (config('tts.vendor_logos') == 'show') data-img="{{ URL::asset($voice->vendor_img) }}" @endif										
												data-id="{{ $voice->voice_id }}" 
												data-lang="{{ $voice->language_code }}" 
												data-type="{{ $voice->voice_type }}"
												data-gender="{{ $voice->gender }}"
												@if (config('tts.user_neural') == 'disable')
													data-usage= "@if ((auth()->user()->group == 'user') && ($voice->voice_type == 'neural')) avoid-clicks @endif"	
												@endif																							
												@if (auth()->user()->voice == $voice->voice_id) selected @endif
												data-class="@if (auth()->user()->language !== $voice->language_code) remove-voice @endif"> 
												{{ $voice->voice }}  														
											</option>
										@endforeach
									</select>
								</div> <!-- END VOICE -->
							</div>
						</div>					
					</div>
				</div>

				<div class="card border-0">
					<div class="card-header">
						<h3 class="card-title">{{ __('Projects') }}</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<div class="input-box">	
									<h6>{{ __('Default Project Name') }}</h6>								
									<select id="project" name="project" data-placeholder="{{ __('Select Default Project Name') }}">	
										@foreach ($projects as $project)
											<option value="{{ $project->name }}" @if (auth()->user()->project == $project->name) selected @endif> {{ ucfirst($project->name) }}</option>
										@endforeach											
									</select>
									@error('project')
										<p class="text-danger">{{ $errors->first('project') }}</p>
									@enderror	
								</div>
							</div>

							<div class="col-md-6 col-sm-12 pt-align">
								<div class="dropdown">
									<button class="btn btn-special create-project mr-4" type="button" id="add-project" data-toggle="tooltip" title="Create New Project"><i class="mdi mdi-animation"></i></button>																																
								</div>
							</div>
						</div>					
					</div>
				</div>

				<div class="card border-0">
					<div class="card-header">
						<h3 class="card-title">{{ __('Edit Profile') }}</h3>
					</div>
					<div class="card-body pb-0">					
						<div class="row">
							<div class="col-sm-6 col-md-6">
								<div class="input-box">
									<div class="form-group">
										<label class="form-label fs-12">{{ __('Full Name') }}</label>
										<input type="text" class="form-control @error('name') is-danger @enderror" name="name" value="{{ auth()->user()->name }}">
										@error('name')
											<p class="text-danger">{{ $errors->first('name') }}</p>
										@enderror									
									</div>
								</div>
							</div>
							<div class="col-sm-6 col-md-6">
								<div class="input-box">
									<div class="form-group">
										<label class="form-label fs-12">{{ __('Job Role') }}</label>
										<input type="text" class="form-control @error('job_role') is-danger @enderror" name="job_role" value="{{ auth()->user()->job_role }}">
										@error('job_role')
											<p class="text-danger">{{ $errors->first('job_role') }}</p>
										@enderror
									</div>
								</div>
							</div>						
							<div class="col-sm-6 col-md-6">
								<div class="input-box">
									<div class="form-group">
										<label class="form-label fs-12">{{ __('Email Address') }}</label>
										<input type="email" class="form-control @error('email') is-danger @enderror" name="email" value="{{ auth()->user()->email }}">
										@error('email')
											<p class="text-danger">{{ $errors->first('email') }}</p>
										@enderror
									</div>
								</div>
							</div>
								
							<div class="col-sm-6 col-md-6">
								<div class="input-box">
									<div class="form-group">								
										<label class="form-label fs-12">{{ __('Phone Number') }}</label>
										<input type="tel" class="fs-12 @error('phone_number') is-danger @enderror" id="phone-number" name="phone_number" value="{{ auth()->user()->phone_number }}">
										@error('phone_number')
											<p class="text-danger">{{ $errors->first('phone_number') }}</p>
										@enderror
									</div>
								</div>
							</div>			
							<div class="col-sm-6 col-md-6">
								<div class="input-box">
									<label class="form-label fs-12">{{ __('Change Avatar') }}</label>
									<div class="input-group file-browser">									
										<input type="text" class="form-control border-right-0 browse-file" placeholder="choose" readonly>
										<label class="input-group-btn">
											<span class="btn btn-primary special-btn">
												Browse <input type="file" name="profile_photo" style="display: none;">
											</span>
										</label>
									</div>
									@error('profile_photo')
										<p class="text-danger">{{ $errors->first('profile_photo') }}</p>
									@enderror
								</div>
							</div>	
							<div class="col-sm-6 col-md-6">
								<div class="input-box">
									<div class="form-group">
										<label class="form-label fs-12">{{ __('Company Name') }}</label>
										<input type="text" class="form-control @error('company') is-danger @enderror" name="company" value="{{ auth()->user()->company }}">
										@error('company')
											<p class="text-danger">{{ $errors->first('company') }}</p>
										@enderror
									</div>
								</div>
							</div>
							<div class="col-sm-6 col-md-6">
								<div class="input-box">
									<div class="form-group">
										<label class="form-label fs-12">{{ __('Company Website') }}</label>
										<input type="text" class="form-control @error('website') is-danger @enderror" name="website" value="{{ auth()->user()->website }}">
										@error('website')
											<p class="text-danger">{{ $errors->first('website') }}</p>
										@enderror
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<div class="input-box">
									<div class="form-group">
										<label class="form-label fs-12">{{ __('Address Line') }}</label>
										<input type="text" class="form-control @error('address') is-danger @enderror" name="address" value="{{ auth()->user()->address }}">
										@error('address')
											<p class="text-danger">{{ $errors->first('address') }}</p>
										@enderror
									</div>
								</div>
							</div>
							<div class="col-sm-6 col-md-4">
								<div class="input-box">
									<div class="form-group">
										<label class="form-label fs-12">{{ __('City') }}</label>
										<input type="text" class="form-control @error('city') is-danger @enderror" name="city" value="{{ auth()->user()->city }}">
										@error('city')
											<p class="text-danger">{{ $errors->first('city') }}</p>
										@enderror
									</div>
								</div>
							</div>
							<div class="col-sm-6 col-md-3">
								<div class="input-box">
									<div class="form-group">
										<label class="form-label fs-12">{{ __('Postal Code') }}</label>
										<input type="text" class="form-control @error('postal_code') is-danger @enderror" name="postal_code" value="{{ auth()->user()->postal_code }}">
										@error('postal_code')
											<p class="text-danger">{{ $errors->first('postal_code') }}</p>
										@enderror
									</div>
								</div>
							</div>
							<div class="col-md-5">
								<div class="form-group">
									<label class="form-label fs-12">{{ __('Country') }}</label>
									<select id="user-country" name="country" data-placeholder="Select Your Country:">	
										@foreach(config('countries') as $value)
											<option value="{{ $value }}" @if(auth()->user()->country == $value) selected @endif>{{ $value }}</option>
										@endforeach										
									</select>
									@error('country')
										<p class="text-danger">{{ $errors->first('country') }}</p>
									@enderror
								</div>
							</div>
						</div>
						<div class="card-footer border-0 text-right mb-2 pr-0">
							<a href="{{ route('user.profile') }}" class="btn btn-cancel mr-2">{{ __('Cancel') }}</a>
							<button type="submit" class="btn btn-primary">{{ __('Update') }}</button>							
						</div>					
					</div>				
				</div>
			</form>
		</div>
	</div>
	<!-- EDIT USER PROFILE PAGE --> 
@endsection

@section('js')
	<!-- Awselect JS -->
	<script src="{{URL::asset('plugins/awselect/awselect-custom.js')}}"></script>
	<script src="{{URL::asset('js/dashboard.js')}}"></script>
	<script src="{{URL::asset('js/awselect.js')}}"></script>
	<!-- File Uploader -->
	<script src="{{URL::asset('js/file-upload.js')}}"></script>
	<!-- Telephone Input JS -->
	<script src="{{URL::asset('plugins/telephoneinput/telephoneinput.js')}}"></script>
	<script src="{{URL::asset('plugins/sweetalert/sweetalert2.all.min.js')}}"></script>
	<script>
		$(function() {
			"use strict";
			
			$("#phone-number").intlTelInput();

			// CREATE NEW PROJECT
			$(document).on('click', '#add-project', function(e) {

				e.preventDefault();

				Swal.fire({
					title: 'Create New Project',
					showCancelButton: true,
					confirmButtonText: 'Create',
					reverseButtons: true,
					closeOnCancel: true,
					input: 'text',
				}).then((result) => {
					if (result.value) {
						var formData = new FormData();
						formData.append("new-project", result.value);
						$.ajax({
							headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
							method: 'post',
							url: 'project/create',
							data: formData,
							processData: false,
							contentType: false,
							success: function (data) {
								if (data['status'] == 'success') {
									Swal.fire('Project Created', 'New project has been successfully created', 'success');	
									location.reload();								
								} else {
									Swal.fire('Project Creation Error', data['message'], 'error');
								}      
							},
							error: function(data) {
								Swal.fire({ type: 'error', title: 'Oops...', text: 'Something went wrong!' })
							}
						})
					} else if (result.dismiss !== Swal.DismissReason.cancel) {
						Swal.fire('No Project Name Entered', 'Make sure to provide a new project name before creating', 'error')
					}
				})
			});
		});
	</script>
@endsection