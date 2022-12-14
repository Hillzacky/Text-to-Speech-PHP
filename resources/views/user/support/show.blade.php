@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('Support Request') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{route('user.tts')}}"><i class="fa-solid fa-messages-question mr-2 fs-12"></i>{{ __('User') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('user.support') }}"> {{ __('Support Request') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('Support Request Details') }}</a></li>
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
					<h3 class="card-title">{{ __('Support Request') }} ID: <span class="text-muted">{{ $ticket->ticket_id }}</span></h3>
				</div>
				<div class="card-body pt-5">		

					<div class="row">
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Support Category') }}: </h6>
							<span class="fs-14">{{ $ticket->category }}</span>
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

					<div class="row pt-5">
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Status') }}: </h6>
							<span class="fs-14">{{ ucfirst($ticket->status) }}</span>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Resolved By') }}: </h6>
							<span class="fs-14">{{ ucfirst($ticket->resolved_by) }}</span>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Resolved On') }}: </h6>
							<span class="fs-14">{{ $ticket->resolved_on }}</span>
						</div>
					</div>	

					<div class="row pt-7">
						<div class="col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Subject') }}: </h6>
							<p class="fs-14 font-italic">{{ $ticket->subject }}</p>
						</div>
						<div class="col-12 mt-2">
							<h6 class="font-weight-bold mb-1">{{ __('Message') }}: </h6>
							<p class="fs-14 font-italic">{{ $ticket->message }}</p>
						</div>
						<div class="col-12 pt-7">
							<h6 class="font-weight-bold mb-1">{{ __('Response') }}: </h6>
							<p class="fs-14 font-italic">{{ $ticket->response }}</p>
						</div>
					</div>	

					<!-- SAVE CHANGES ACTION BUTTON -->
					<div class="border-0 text-right mb-2 mt-8">
						<a href="{{ route('user.support') }}" class="btn btn-primary">{{ __('Return') }}</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- END SUPPORT REQUEST -->
@endsection

