@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('Checkout') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('user.tts') }}"><i class="fa-solid fa-badge-dollar mr-2 fs-12"></i>{{ __('User') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('user.subscriptions') }}"> {{ __('Subscriptions') }} </a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('Subscribe Now / Checkout') }}</a></li>
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
						<h4 class="checkout-success">{{ __('Congratulations') }}!</h4>
						@if ($plan->plan_type == 'subscription')
							<div class="text-center mb-6">
								<p class="mt-5 fs-14">{{ __('You have successfully subscribed to') }} <span class="text-info font-weight-bold">{{ $plan->plan_name }}</span> {{ __(' subscription plan') }}.</p>
								<p class="fs-14">{{ __('Each month you will have') }} <span class="text-info font-weight-bold">{{ number_format($plan->characters) }}</span> {{ __('characters added to your account') }}.</p>
							</div>						
						@endif
						@if ($plan->plan_type == 'prepaid')
							<div class="text-center mb-5">
								<p class="mt-5 fs-14">{{ __('You have successfully added') }} <span class="text-info font-weight-bold">{{ number_format($plan->characters) }}</span> {{ __('characters to your account') }}.</p>
								@if ($plan->bonus > 0)
									<p class="fs-14">{{ __('Bonus') }} <span class="text-info font-weight-bold">+{{ number_format($plan->bonus) }}</span> {{ __('characters were added as well') }}.</p>
								@endif								
							</div>						
						@endif
						<div class="text-center pt-2 pb-4">
							<a href="{{ route('user.payments.invoice', $order_id) }}" id="invoice-button" class="btn btn-primary pl-6 pr-6 mr-2">{{ __('Get Invoice') }}</a>
							<a href="{{ route('user.tts') }}" id="payment-button" class="btn btn-primary pl-6 pr-6">{{ __('Start Usage') }}</a>							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection



