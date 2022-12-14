@extends('layouts.app')

@section('css')
	<!-- Data Table CSS -->
	<link href="{{URL::asset('plugins/awselect/awselect.min.css')}}" rel="stylesheet" />
@endsection

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('User Support Request') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="mdi mdi-account-alert mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.support')}}"> {{ __('Support Requests') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.support')}}"> {{ __('Support Requests Details') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')						
	<!-- SUPPORT REQUEST -->
	<div class="row">
		<div class="col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden border-0">
				<div class="card-header">
					<h3 class="card-title">{{ __('Support Request') }} ID: <span class="text-muted">{{ $ticket->ticket_id }}</h3>
				</div>
				<div class="card-body pt-5">									
							
						<div class="row">		
							<div class="col-lg-4 col-md-4 col-12">
								<h6 class="font-weight-bold mb-1">{{ __('Support Category') }}: </h6>
								<span class="fs-14 text-primary font-weight-bold">{{ $ticket->category }}</span>
							</div>					
							<div class="col-lg-4 col-md-4 col-12">
								<h6 class="font-weight-bold mb-1">{{ __('Support Priority') }}: </h6>
								<span class="fs-14">{{ $ticket->priority }}</span>
							</div>														
							<div class="col-lg-4 col-md-4 col-12">
								<h6 class="font-weight-bold mb-1">{{ __('Created On') }}: </h6>
								<span class="fs-14">{{ $ticket->created_at }}</span>
							</div>							
						</div>
						
						<div class="row mt-4">
							<div class="col-lg-4 col-md-4 col-12">
								<h6 class="font-weight-bold mb-1">{{ __('Created By') }}: </h6>
								<span class="fs-14">{{ ucfirst($created_by) }}</span>
							</div>
							<div class="col-lg-4 col-md-4 col-12">
								<h6 class="font-weight-bold mb-1">{{ __('Status') }}: </h6>
								<span class="fs-14">{{ $ticket->status }}</span>
							</div>
							<div class="col-lg-4 col-md-4 col-12">
								<h6 class="font-weight-bold mb-1">{{ __('Resolved On') }}: </h6>
								<span class="fs-14">{{ $ticket->updated_at }}</span>
							</div>							
						</div>

						<div class="row pt-7">
							<div class="col-12">
								<h6 class="font-weight-bold mb-1">{{ __('Ticket Subject') }}: </h6>
								<p class="fs-14 font-italic">{{ $ticket->subject }}</p>
							</div>
							<div class="col-12 mt-2">
								<h6 class="font-weight-bold mb-1">{{ __('Ticket Message') }}: </h6>
								<p class="fs-14 font-italic">{{ $ticket->message }}</p>
							</div>
							<div class="col-12 mt-2">
								<h6 class="font-weight-bold mb-1">{{ __('Ticket Response') }}: </h6>
								<p class="fs-14 font-italic">{{ $ticket->response }}</p>
							</div>
						</div>

						@if ($ticket->category == 'Credit Request')
							<div class="row pt-7">
								<div class="col-12">
									<h6 class="font-weight-bold mb-2">{{ __('Add Credits to ') }} {{ ucfirst($created_by) }}: </h6>
									<a href="{{ route("admin.user.characters", $ticket->user_id ) }}" class="btn btn-primary mr-2">{{ __('Approve Credits') }}</a>
								</div>
							</div>
						@endif						

					<form action="{{ route('admin.support.update', [$ticket->ticket_id]) }}" method="POST" enctype="multipart/form-data">
						@method('PUT')
						@csrf

						<div class="row pt-7">
							<div class="col-lg-12 col-md-12 col-sm-12">				
								<div class="input-box">	
									<h6>{{ __('Response Status') }} <span class="text-muted">({{ __('Required') }})</span></h6>
									<select id="response-status" name="response-status" data-placeholder="{{ __('Select Response Status') }}:">			
										<option value="Pending" selected>{{ __('Pending') }}</option>
										<option value="Resolved">{{ __('Resolved') }}</option>
										<option value="Escalated">{{ __('Escalated') }}</option>
										<option value="Declined">{{ __('Declined') }}</option>
									</select>
								</div> 							
							</div>
						</div>

						<div class="row mt-2">
							<div class="col-lg-12 col-md-12 col-sm-12">	
								<div class="input-box">	
									<h6>{{ __('Response') }} <span class="text-muted">({{ __('Required') }})</span></h6>							
									<textarea class="form-control" name="response" rows="10"></textarea>
									@error('response')
										<p class="text-danger">{{ $errors->first('response') }}</p>
									@enderror	
								</div>											
							</div>
						</div>

						<!-- ACTION BUTTON -->
						<div class="border-0 text-right mb-2 mt-1">
							<a href="{{ route('admin.support') }}" class="btn btn-cancel mr-2">{{ __('Return') }}</a>
							<button type="submit" class="btn btn-primary">{{ __('Respond') }}</button>							
						</div>				

					</form>					
				</div>
			</div>
		</div>
	</div>
	<!-- END SUPPORT REQUEST -->
@endsection

@section('js')
	<!-- Awselect JS -->
	<script src="{{URL::asset('plugins/awselect/awselect.min.js')}}"></script>
	<script src="{{URL::asset('js/awselect.js')}}"></script>
@endsection
