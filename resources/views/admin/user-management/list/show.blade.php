@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('User Information') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="mdi mdi-account-convert mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.user.dashboard') }}"> {{ __('User Management') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.user.list') }}">{{ __('User List') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="#"> {{ __('View User Information') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')
	<!-- USER PROFILE PAGE -->
	<div class="row">
		<div class="col-xl-3 col-lg-3 col-md-12">
			<div class="card border-0">
				<div class="card-header">
					<h3 class="card-title">{{ __('Personal Information') }}</h3>
				</div>
				<div class="overflow-hidden p-0">
					<div class="row">
						<div class="col-sm-6 border-right border-bottom text-center">
							<div class="p-2">
								<span class="text-muted fs-12">{{ __('Current Balance') }}</span>
								<h5 class="mt-1 mb-1 font-weight-bold text-dark number-font fs-14">{!! config('payment.default_system_currency_symbol') !!}{{ $user->balance }}</h5>								
							</div>
						</div>
						<div class="col-sm-6 border-bottom">
							<div class="text-center p-2">
								<span class="text-muted fs-12">{{ __('Available Characters') }}</span>
								<h5 class="mt-1 mb-1 font-weight-bold text-dark number-font fs-14">{{ number_format($user->available_chars) }}</h5>								
							</div>
						</div>
					</div>
				</div>
				<div class="widget-user-image overflow-hidden mx-auto mt-5"><img alt="User Avatar" class="rounded-circle" src="@if($user->profile_photo_path) {{ $user->profile_photo_path }} @else {{ URL::asset('img/users/avatar.jpg') }} @endif"></div>
				<div class="card-body text-center">				
					<div>
						<h4 class="mb-1 mt-1 font-weight-bold fs-16">{{ $user->name }}</h4>
						<a href="{{ route('admin.user.edit', [$user->id]) }}" class="btn btn-primary mt-3 mb-2 mr-2 pl-5 pr-5"><i class="fa fa-pencil mr-1"></i> {{ __('Edit Profile') }}</a>
						<a href="{{ route('admin.user.characters', [$user->id]) }}" class="btn btn-primary mt-3 mb-2"><i class="fa fa-magic mr-1"></i>{{ __('Add Characters') }}</a>
					</div>
				</div>
				
				<div class="card-body pt-0">
					<div class="table-responsive">
						<table class="table mb-0">
							<tbody>
								<tr>
									<td class="py-2 px-0 border-top-0">
										<span class="font-weight-semibold w-50">{{ __('Full Name') }} </span>
									</td>
									<td class="py-2 px-0 border-top-0">{{ $user->name }}</td>
								</tr>
								<tr>
									<td class="py-2 px-0">
										<span class="font-weight-semibold w-50">{{ __('Email') }} </span>
									</td>
									<td class="py-2 px-0">{{ $user->email }}</td>
								</tr>
								<tr>
									<td class="py-2 px-0">
										<span class="font-weight-semibold w-50">{{ __('User Status') }} </span>
									</td>
									<td class="py-2 px-0">{{ $user->status }}</td>
								</tr>
								<tr>
									<td class="py-2 px-0">
										<span class="font-weight-semibold w-50">{{ __('User Group') }} </span>
									</td>
									<td class="py-2 px-0">{{ $user->group }}</td>
								</tr>
								<tr>
									<td class="py-2 px-0">
										<span class="font-weight-semibold w-50">{{ __('Registered On') }} </span>
									</td>
									<td class="py-2 px-0">{{ $user->created_at }}</td>
								</tr>								
								<tr>
									<td class="py-2 px-0">
										<span class="font-weight-semibold w-50">{{ __('Last Updated On') }} </span>
									</td>
									<td class="py-2 px-0">{{ $user->updated_at }}</td>
								</tr>
								<tr>
									<td class="py-2 px-0">
										<span class="font-weight-semibold w-50">{{ __('Referral ID') }} </span>
									</td>
									<td class="py-2 px-0">{{ $user->referral_id }}</td>
								</tr>
								<tr>
									<td class="py-2 px-0">
										<span class="font-weight-semibold w-50">{{ __('Job Role') }} </span>
									</td>
									<td class="py-2 px-0">{{ $user->job_role }}</td>
								</tr>								
								<tr>
									<td class="py-2 px-0">
										<span class="font-weight-semibold w-50">{{ __('Company') }}</span>
									</td>
									<td class="py-2 px-0">{{ $user->company }}</td>
								</tr>
								<tr>
									<td class="py-2 px-0">
										<span class="font-weight-semibold w-50">{{ __('Website') }} </span>
									</td>
									<td class="py-2 px-0">{{ $user->website }}</td>
								</tr>
								<tr>
									<td class="py-2 px-0">
										<span class="font-weight-semibold w-50">{{ __('Address') }} </span>
									</td>
									<td class="py-2 px-0">{{ $user->address }}</td>
								</tr>
								<tr>
									<td class="py-2 px-0">
										<span class="font-weight-semibold w-50">{{ __('Postal Code') }} </span>
									</td>
									<td class="py-2 px-0">{{ $user->postal_code }}</td>
								</tr>
								<tr>
									<td class="py-2 px-0">
										<span class="font-weight-semibold w-50">{{ __('City') }} </span>
									</td>
									<td class="py-2 px-0">{{ $user->city }}</td>
								</tr>
								<tr>
									<td class="py-2 px-0">
										<span class="font-weight-semibold w-50">{{ __('Country') }} </span>
									</td>
									<td class="py-2 px-0">{{ $user->country }}</td>
								</tr>								
								<tr>
									<td class="py-2 px-0">
										<span class="font-weight-semibold w-50">{{ __('Phone') }} </span>
									</td>
									<td class="py-2 px-0">{{ $user->phone_number }}</td>
								</tr>
							</tbody>
						</table>
						<div class="border-0 text-right mb-2 mt-2">
							<a href="{{ route('admin.user.list') }}" class="btn btn-primary">{{ __('Return') }}</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-9 col-lg-9 col-md-12">
			<div class="row">
				<div class="col-xl-12 col-md-12 col-12">
					<div class="card border-0">							
						<div class="card-header">
							<h3 class="card-title">{{ __('User Payments') }} <span class="text-muted">({{ __('Current Year') }})</span></h3>
						</div>
						<div class="card-body">
							<div class="row mb-5 mt-2">	
								<div class="col-xl-4 col-12 ">
									<p class=" mb-1 fs-12">{{ __('Total Payments') }} ({{ config('payment.default_system_currency') }})</p>
									<h3 class="mb-0 fs-20 number-font">{!! config('payment.default_system_currency_symbol') !!}{{ number_format((float)$user_data_year['total_payments'][0]['data'], 2, '.', '') }}</h3>
								</div>
							</div>
							<div class="chartjs-wrapper-demo">
								<canvas id="chart-user-payments" class="h-330"></canvas>
							</div>
						</div>								
					</div>
				</div>
				<div class="col-xl-12 col-md-12 col-12">
					<div class="card border-0">							
						<div class="card-header">
							<h3 class="card-title">{{ __('Character Usage') }} <span class="text-muted">({{ __('Current Year') }})</span></h3>
						</div>
						<div class="card-body">
							<div class="row mb-5 mt-2">	
								<div class="col-xl-3 col-12 ">
									<p class=" mb-1 fs-12">{{ __('Total Standard Characters Used') }}</p>
									<h3 class="mb-0 fs-20 number-font">{{ number_format($user_data_year['total_standard_chars'][0]['data']) }}</h3>
								</div>
								<div class="col-xl-3 col-12 ">
									<p class=" mb-1 fs-12">{{ __('Total Neural Characters Used') }}</p>
									<h3 class="mb-0 fs-20 number-font">{{ number_format($user_data_year['total_neural_chars'][0]['data']) }}</h3>
								</div>
								<div class="col-xl-3 col-12 ">
									<p class=" mb-1 fs-12">{{ __('Total Audio Files Created') }}</p>
									<h3 class="mb-0 fs-20 number-font">{{ number_format($user_data_year['total_audio_files'][0]['data']) }}</h3>
								</div>
								<div class="col-xl-3 col-12 ">
									<p class=" mb-1 fs-12">{{ __('Total Listen Mode Results') }}</p>
									<h3 class="mb-0 fs-20 number-font">{{ number_format($user_data_year['total_listen_modes'][0]['data']) }}</h3>
								</div>
							</div>
							<div class="chartjs-wrapper-demo">
								<canvas id="chart-user-usage" class="h-330"></canvas>
							</div>
						</div>								
					</div>
				</div>				
			</div>			
		</div>
	</div>
	<!-- END USER PROFILE PAGE -->
@endsection

@section('js')
	<!-- Chart JS -->
	<script src="{{URL::asset('plugins/chart/chart.bundle.js')}}"></script>
	<script type="text/javascript">
		$(function() {
	
			'use strict';

			var standardData = JSON.parse(`<?php echo $chart_data['standard_chars']; ?>`);
			var standardDataset = Object.values(standardData);
			var neuralData = JSON.parse(`<?php echo $chart_data['neural_chars']; ?>`);
			var neuralDataset = Object.values(neuralData);

			var ctx = document.getElementById('chart-user-usage');
			new Chart(ctx, {
				type: 'bar',
				data: {
					labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
					datasets: [{
						label: 'Standard Characters Used: ',
						data: standardDataset,
						backgroundColor: '#007bff',
						borderWidth: 1,
						fill: true
					}, {
						label: 'Neural Characters Used: ',
						data: neuralDataset,
						backgroundColor:  '#1e1e2d',
						borderWidth: 1,
						fill: true
					}]
				},
				options: {
					maintainAspectRatio: false,
					legend: {
						display: false,
						labels: {
							display: false
						}
					},
					responsive: true,
					scales: {
						yAxes: [{
							stacked: true,
							ticks: {
								beginAtZero: true,
								fontSize: 11,
								stepSize: 100000,
							},
							gridLines: {
								borderDash: [3, 2]                            
							}
						}],
						xAxes: [{
							barPercentage: 0.5,
							stacked: true,
							ticks: {
								fontSize: 11
							},
							gridLines: {
								borderDash: [3, 2]                            
							}
						}]
					},
					tooltips: {
						cornerRadius: 0,
						xPadding: 10,
						yPadding: 10
					},
					legend: {
						position: 'bottom',
						labels: {
						boxWidth: 10,
						fontSize: 10
						}
					}
				}
			});


			var paymentData = JSON.parse(`<?php echo $chart_data['payments']; ?>`);
			var paymentDataset = Object.values(paymentData);

			var ctx = document.getElementById('chart-user-payments');
			new Chart(ctx, {
				type: 'bar',
				data: {
					labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
					datasets: [{
						label: 'Payments ({{ config('payment.default_system_currency') }}): ',
						data: paymentDataset,
						backgroundColor: '#00c851',
						borderWidth: 1,
						fill: true
					}]
				},
				options: {
					maintainAspectRatio: false,
					legend: {
						display: false,
						labels: {
							display: false
						}
					},
					responsive: true,
					scales: {
						yAxes: [{
							stacked: true,
							ticks: {
								beginAtZero: true,
								fontSize: 11,
								stepSize: 50,
							},
							gridLines: {
								borderDash: [3, 2]                            
							}
						}],
						xAxes: [{
							barPercentage: 0.5,
							stacked: true,
							ticks: {
								fontSize: 11
							},
							gridLines: {
								borderDash: [3, 2]                            
							}
						}]
					},
					tooltips: {
						cornerRadius: 0,
						xPadding: 10,
						yPadding: 10
					},
					legend: {
						position: 'bottom',
						labels: {
						boxWidth: 10,
						fontSize: 10
						}
					}
				}
			});
		});
	</script>
@endsection
