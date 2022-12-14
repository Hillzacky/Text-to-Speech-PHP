@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('Checkout') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('user.tts') }}"><i class="fa-solid fa-badge-dollar mr-2 fs-12"></i>{{ __('User') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('user.subscriptions') }}"> {{ __('Subscriptions') }} </a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('Bank Transfer Checkout') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')	
	<div class="row">
		<div class="col-md-6">
			<div class="card border-0 pt-2">
				<div class="card-body">			
					<div class="text-center">
						<i class="mdi mdi-approval fs-45 text-info mb-4"></i>
						<h4 class="checkout-success">{{ __('Almost There') }}!</h4>
						@if ($id->plan_type == 'subscription')
							<div class="text-center mb-6">
								<p class="mt-5 fs-14">{{ __('You have successfully placed order for ') }} <span class="text-info font-weight-bold">{{ $id->plan_name }}</span> {{ __('monthly subscription plan') }}.</p>
								<p class="fs-14">{{ __('After successful payment, each month you will have') }} <span class="text-info font-weight-bold">{{ number_format($id->characters) }}</span> {{ __('characters added to your account. To keep your subsciption active in coming month, please provide payments by the end of the current month.') }}.</p>
								<p class="fs-14">{{ __('Please provide payment to our bank requisites below. Use Order ID number as payment reference') }}.</p>	
								<p class="text-muted fs-12 mb-4">Order ID: <span class="font-weight-bold text-primary">{{ $orderID }}</span></p>
								<p class="text-muted fs-12 mb-4">Total Payment Due: <span class="font-weight-bold text-primary">{{ number_format((float)$total_price, 2, '.', '') }} {{ config('payment.default_currency') }}</span></p>
								<pre class="text-muted fs-12 mb-4">{{ $bank['bank_requisites'] }}</pre>
							</div>						
						@endif
						@if ($id->plan_type == 'prepaid')
							<div class="text-center mb-5">
								<p class="mt-5 fs-14">{{ __('You have successfully placed order for new ') }} <span class="text-info font-weight-bold">{{ number_format($id->characters) }}</span> {{ __('characters for your account') }}.</p>
								@if ($id->bonus > 0)
									<p class="fs-14">{{ __('With') }} <span class="text-info font-weight-bold">+{{ number_format($id->bonus) }}</span> {{ __('bonus characters as well') }}.</p>
								@endif	
								<p class="fs-14">{{ __('Please provide payment to our bank requisites below. Use Order ID number as payment reference') }}.</p>	
								<p class="text-muted fs-12 mb-4">Order ID: <span class="font-weight-bold text-primary">{{ $orderID }}</span></p>
								<p class="text-muted fs-12 mb-4">Total Payment Due: <span class="font-weight-bold text-primary">{{ number_format((float)$final_price, 2, '.', '') }} {{ config('payment.default_currency') }}</span></p>
								<pre class="text-muted fs-12 mb-4">{{ $bank['bank_requisites'] }}</pre>							
							</div>						
						@endif
						<div class="text-center pt-2 pb-4">
							<a href="{{ route('user.payments.invoice.transfer', $orderID) }}" id="invoice-button" class="btn btn-primary pl-6 pr-6 mr-2">{{ __('Get Bank Requisites') }}</a>						
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection



