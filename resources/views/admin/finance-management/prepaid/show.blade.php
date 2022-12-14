@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('Show Prepaid Plan') }}</h4>
			<ol class="breadcrumb mb-2">
				<ol class="breadcrumb mb-2">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-sack-dollar mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
					<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.finance.dashboard') }}"> {{ __('Finance Management') }}</a></li>
					<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.finance.subscriptions') }}"> {{ __('Prepaid Plan Types') }}</a></li>
					<li class="breadcrumb-item active" aria-current="page"><a href="#"> {{ __('Prepaid') }}</a></li>
				</ol>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')						
	<div class="row">
		<div class="col-lg-6 col-md-6 col-xm-12">
			<div class="card border-0">
				<div class="card-header">
					<h3 class="card-title">{{ __('Plan Name') }}: <span class="text-info">{{ $id->plan_name }}</span> </h3>
				</div>
				<div class="card-body pt-5">		

					<div class="row">
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Created Date') }}: </h6>
							<span class="fs-14">{{ $id->created_at }}</span>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Plan Type') }}: </h6>
							<span class="fs-14">{{ __('Prepaid') }}</span>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Plan Name') }}: </h6>
							<span class="fs-14">{{ ucfirst($id->plan_name) }}</span>
						</div>
					</div>

					<div class="row pt-5">
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Plan Status') }}: </h6>
							<span class="fs-14">{{ ucfirst($id->status) }}</span>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Price') }}: </h6>
							<span class="fs-14">{{ $id->cost }}</span>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Currency') }}: </h6>
							<span class="fs-14">{{ $id->currency }}</span>
						</div>
					</div>

					<div class="row pt-5">
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Payment Frequence') }}: </h6>
							<span class="fs-14">{{ ucfirst($id->payment_frequency) }}</span>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Included Characters') }}: </h6>
							<span class="fs-14">{{ ucfirst($id->characters) }}</span>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<h6 class="font-weight-bold mb-1">{{ __('Bonus Characters') }}: </h6>
							<span class="fs-14">{{ $id->bonus }}</span>
						</div>
					</div>			

					<!-- SAVE CHANGES ACTION BUTTON -->
					<div class="border-0 text-right mb-2 mt-7">
						<a href="{{ route('admin.finance.prepaid') }}" class="btn btn-cancel mr-2">{{ __('Cancel') }}</a>
						<a href="{{ route('admin.finance.prepaid.edit', $id) }}" class="btn btn-primary">{{ __('Edit Plan') }}</a>						
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

