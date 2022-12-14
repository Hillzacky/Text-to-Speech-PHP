@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('My Referrals') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{route('user.tts')}}"><i class="fa-solid fa-badge-dollar mr-2 fs-12"></i>{{ __('User') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('user.balance') }}"> {{ __('My Balance') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('My Referrals') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')	
	<div class="row">
		<div class="col-lg-6 col-md-12 col-xm-12">
			<div class="card overflow-hidden border-0">
				<div class="card-body pt-7 pb-6">
					<h3 class="card-title fs-20 text-center">{{ __('Referral') }}</h3>
					<p class="fs-12 text-center pl-6 pr-6 mb-6">{{ $referral['referral_headline'] }}</p>

					<div class="row text-center justify-content-md-center">
						<div class="col-md-3 col-sm-12 referral-box special-shadow">
							<div><i class="mdi mdi-account-multiple-plus"></i></div>
							<a class="fs-12 font-weight-bold" href="{{ route('user.referral.referrals') }}">{{ __('My Referrals') }}</a>
						</div>
						<div class="col-md-3 col-sm-12 referral-box special-shadow">
							<div><i class="zmdi zmdi-money"></i></div>
							<a class="fs-12 font-weight-bold" href="{{ route('user.referral.payout') }}">{{ __('My Payouts') }}</a>
						</div>
						<div class="col-md-3 col-sm-12 referral-box special-shadow">
							<div><i class="fa fa-university"></i></div>
							<a class="fs-12 font-weight-bold" href="{{ route('user.referral.gateway') }}">{{ __('My Gateways') }}</a>
						</div>
					</div>

					<div class="row mt-7 ml-4 mr-4">						
						<div class="col-md-12 col-sm-12">							
							<div class="input-box">		
								<h6 class="fs-12 font-weight-bold poppins">{{ __('My Referral URL') }}</h6>							
								<div class="form-group d-flex referral-social-icons">									 							    
									<input type="text" class="form-control" id="email" readonly value="{{ config('app.url') }}/?ref={{ auth()->user()->referral_id }}">
									<div class="actions-total ml-2">
										<a href="" class="btn actions-total-buttons" id="actions-copy" data-link="{{ config('app.url') }}/?ref={{ auth()->user()->referral_id }}" data-toggle="tooltip" data-placement="top" title="Copy Referral Link"><i class="fa fa-link"></i></a>										
									</div>
								</div> 
							</div>
						</div>
					</div>

					<div class="row mt-5 ml-4 mr-4">						
						<div class="col-md-6 col-sm-12">							
							<div class="input-box">		
								<h6 class="fs-12 font-weight-bold poppins">{{ __('My Earned Commissions') }}</h6>							
								<p class="fs-12">{!! config('payment.default_system_currency_symbol') !!}{{ number_format((float)$total_commission[0]['data'], 2, '.', '') }} {{ config('payment.default_currency') }}</p>
							</div>
						</div>
						<div class="col-md-6 col-sm-12">							
							<div class="input-box">		
								<h6 class="fs-12 font-weight-bold poppins">{{ __('My Earned TTS Credits') }}</h6>							
								<p class="fs-12">{{ number_format($total_credits[0]['data']) }}</p>
							</div>
						</div>
					</div>

					<div class="row mt-5 ml-4 mr-4">						
						<div class="col-md-6 col-sm-12">							
							<div class="input-box">		
								<h6 class="fs-12 font-weight-bold poppins">{{ __('Referral Policy') }}</h6>							
								<p class="fs-12">@if (config('payment.referral.payment.policy') == 'first') {{ __('Only the First Successful Purchase') }} @else {{ __('All Successful Purchases') }} @endif</p>
							</div>
						</div>
						<div class="col-md-6 col-sm-12">							
							<div class="input-box">		
								<h6 class="fs-12 font-weight-bold poppins">{{ __('Referral Commission Rate') }}</h6>							
								<p class="fs-12">{{ config('payment.referral.payment.commission') }}%</p>
							</div>
						</div>
					</div>

					<div class="row mt-5 ml-4 mr-4">						
						<div class="col-md-12 col-sm-12">							
							<div class="input-box">		
								<h6 class="fs-12 font-weight-bold poppins mb-3">{{ __('Referral Guidelines') }}</h6>							
								<pre class="fs-12 referral-guideline">{{ $referral['referral_guideline'] }}</pre>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>

		<div class="col-lg-6 col-md-12 col-xm-12">
			<div class="card overflow-hidden border-0">
				<div class="card-body pt-7 pb-6">
					<h3 class="card-title fs-20 text-center">{{ __('How it Works') }}</h3>
					
					<div class="row text-center justify-content-md-center mt-7">
						<div class="col-lg-3 col-md-3 col-sm-4">
							<div class="referral-icon-user">
								<i class="mdi mdi-upload-network"></i>
								<h6 class="mt-3 fs-12 font-weight-bold">{{ __('Send Invitation') }}</h6>
								<p class="fs-12">{{ __('Send your referral link to your friends and tell them how cool is') }} {{ config('app.name') }}!</p>
								<img id="left-direction" src="{{ URL::asset('img/files/ref-1.png') }}" alt="">
							</div>							
						</div>
						<div class="col-lg-3 col-md-3 col-sm-4">
							<div class="referral-icon-user">
								<i class="mdi mdi-account-check"></i>
								<h6 class="mt-2 fs-12 font-weight-bold">{{ __('Registration') }}</h6>
								<p class="fs-12">{{ __('Let them register to our Text to Speech services using your referral link') }}.</p>
								<img id="right-direction" src="{{ URL::asset('img/files/ref-2.png') }}" alt="">
							</div>														
						</div>
						<div class="col-lg-3 col-md-3 col-sm-4">
							<div class="referral-icon-user">
								<i class="mdi mdi-approval"></i>
								<h6 class="mt-2 fs-12 font-weight-bold">{{ __('Get Commissions') }}</h6>
								<p class="fs-12">Earn {{ config('payment.referral.payment.commission') }}% commission for 
									@if (config('payment.referral.payment.policy') == 'first')
										their first prepaid or subscription plan purchase!
									@else
										every prepaid or subscription plan purchases!
								@endif</p>
							</div>							
						</div>
					</div>

					<form id="" action="{{ route('user.referral.email') }}" method="post" enctype="multipart/form-data">
						@csrf
						<div class="row mt-6 ml-4 mr-4">
							<div class="col-md-12">
								<h6 class="fs-12 font-weight-bold">{{ __('Invite your friends') }}</h6>
								<div class="input-box">								
									<h6>{{ __('Insert your friend\'s email address and send him/her invitations to join ') }} {{ config('app.name') }}!</h6> 
									<div class="input-group file-browser">							    
										<input type="email" class="form-control @error('email') is-danger @enderror border-right-0 browse-file" id="email" name="email" placeholder="Email address">
										<label class="input-group-btn">
											<button class="btn btn-primary special-btn" id="invite-friends-button">
												Send
											</button>
										</label>
									</div> 
									@error('email')
										<p class="text-danger">{{ $errors->first('email') }}</p>
									@enderror
								</div>

								<input type="text" name="subject" value="Introduction to join {{ config('app.name') }}" hidden>
								<input type="text" name="message" value="I want to refer you to this awesome text to speech service that I'm using! You can register via this link: {{ config('app.url') }}/?ref={{ auth()->user()->referral_id }}" hidden>
							</div>
						</div>
					</form>

					<div class="row mt-6 ml-4 mr-4">
						<h6 class="fs-12 ml-3 font-weight-bold">{{ __('Share the referral link') }}</h6>
						<h6 class="fs-12 ml-3">{{ __('You can also share your referral link by copying and sending it or sharing it on your social media profiles') }}.</h6> 
						<div class="col-md-8 col-sm-12">							
							<div class="input-box">									
								<div class="form-group">							    
									<input type="text" class="form-control" id="email" readonly value="{{ config('app.url') }}/?ref={{ auth()->user()->referral_id }}">
								</div> 
							</div>
						</div>
						<div class="col-md-4 col-sm-12 referral-social-icons text-right">
							<div class="actions-total">
								<a href="https://www.facebook.com/sharer/sharer.php?u={{ config('app.url') }}/?ref={{ auth()->user()->referral_id }}&t=Registration Link" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;" target="_blank" class="btn actions-total-buttons" id="actions-facebook" data-toggle="tooltip" data-placement="top" title="Share in Facebook"><i class="fa fa-facebook-f"></i></a>
								<a href="https://www.linkedin.com/shareArticle?mini=true&url={{ config('app.url') }}/?ref={{ auth()->user()->referral_id }}&title=Registration Link" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;" target="_blank" class="btn actions-total-buttons" id="actions-linkedin" data-toggle="tooltip" data-placement="top" title="Share in Linkedin"><i class="fa fa-linkedin"></i></a>
								<a href="http://www.reddit.com/submit?url={{ config('app.url') }}/?ref={{ auth()->user()->referral_id }}" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;" target="_blank" class="btn actions-total-buttons" id="actions-reddit" data-toggle="tooltip" data-placement="top" title="Share in Reddit"><i class="fa fa-reddit"></i></a>
								<a href="https://twitter.com/share?url={{ config('app.url') }}/?ref={{ auth()->user()->referral_id }}&text=Registration Link" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;" target="_blank" class="btn actions-total-buttons" id="actions-twitter" data-toggle="tooltip" data-placement="top" title="Share in Twitter"><i class="fa fa-twitter"></i></a>
								
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
@endsection

@section('js')
	<!-- Link Share JS -->
	<script src="{{URL::asset('js/link-share.js')}}"></script>
@endsection