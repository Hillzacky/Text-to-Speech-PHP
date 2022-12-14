@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('User Dahsboard') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="mdi mdi-account-convert mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.user.dashboard') }}"> {{ __('User Management') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="#"> {{ __('User Dashboard') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')						
	<!-- USER BOX INFO -->
	<div class="row">
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden border-0">
				<div class="card-body">
					<i class="mdi mdi-account-multiple-plus text-primary fs-45 float-right"></i>	
					<p class=" mb-3 fs-12 font-weight-bold mt-1">{{ __('Total Registered Users') }}</p>
					<h2 class="mb-0"><span class="number-font-chars">{{ number_format($user_data_year['total_users'][0]['data']) }}</span></h2>									
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card border-0">
				<div class="card-body">
					<i class="mdi mdi-account-edit text-success fs-45 float-right"></i>	
					<p class=" mb-3 fs-12 font-weight-bold mt-1">{{ __('Total Subscribed Users') }}</p>
					<h2 class="mb-0"><span class="number-font-chars">{{ number_format($user_data_year['total_paid_users'][0]['data']) }}</span></h2>					
									
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card border-0">
				<div class="card-body">
					<i class="mdi mdi-account-network fs-45 float-right"></i>	
					<p class=" mb-3 fs-12 font-weight-bold mt-1">{{ __('Online Users') }}</p>
					<h2 class="mb-0"><span class="number-font-chars">{{ $users }}</span></h2>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card border-0">
				<div class="card-body">
					<i class="mdi mdi-account-convert fs-45 float-right"></i>	
					<p class=" mb-3 fs-12 font-weight-bold mt-1">{{ __('Visiters Today') }} ({{ __('Registered') }})</p>
					<h2 class="mb-0"><span class="number-font-chars">{{ $users_today }}</span></h2>
				</div>
			</div>
		</div>
	</div>
	<!-- END USER BOX INFO -->

	<!-- MONTHLY USAGE ANALYTICS -->
	<div class="row mt-4">
		<div class="col-lg-12 col-md-12">
			<div class="card overflow-hidden border-0">
				<div class="card-header">
					<h3 class="card-title">{{ __('Registered User Countries') }}</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-7 col-md-12 col-sm-12">
							<div class="mt-3">
								@if (config('services.google.maps.enable') == 'on')
									<div id="countries-analytics-chart" class="h-330"></div>
								@else 
									<div class="text-center">
										<p class="fs-12 mt-6">{{ __('Google Maps is Disabled') }}</p>
									</div>
								@endif
								
							</div>
						</div>
						<div class="col-lg-5 col-md-12 col-sm-12">
							<div class="mt-3 country-users">
								<h6>{{ __('Top 30 Countries') }}</h6>
								<ul>
									@foreach ( $user_data_year['top_countries'] as $key => $top_country )
										<li class="country">{{ $key }} - <span>{{ $top_country }}</span></li>
									@endforeach									
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-12 col-md-12">
			<div class="card overflow-hidden border-0">
				<div class="card-header">
					<h3 class="card-title">{{ __('New Registered Users') }} <span class="text-muted">({{ __('Current Month') }})</span></h3>
				</div>
				<div class="card-body mb-3 mt-3">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="">
								<canvas id="chart-new-users-month" class="h-330"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-12 col-md-12">
			<div class="card overflow-hidden border-0">
				<div class="card-header">
					<h3 class="card-title">{{ __('New Registered Users ') }}<span class="text-muted">({{ __('Current Year') }})</span></h3>
				</div>
				<div class="card-body">
					<div class="row mb-5 mt-2">
						<div class="col-xl-3 col-6">
							<p class="mb-1 fs-12">{{ __('Total Free Tier Users') }}</p>
							<h3 class="mb-0 fs-20 number-font">{{ number_format($user_data_year['total_free_tier'][0]['data']) }}</h3>
						</div>
						<div class="col-xl-3 col-6 ">
							<p class=" mb-1 fs-12">{{ __('Total Paid Tier Users') }}</p>
							<h3 class="mb-0 fs-20 number-font">{{ number_format($user_data_year['total_paid_tier'][0]['data']) }}</h3>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="mt-1">
								<canvas id="chart-new-users-year" class="h-330"></canvas>
							</div>
						</div>						
					</div>
				</div>
			</div>
		</div> 
	</div>
	<!-- END MONTHLY USAGE ANALYTICS -->

@endsection

@section('js')
	<!-- Chart JS -->
	<script src="{{URL::asset('plugins/chart/chart.bundle.js')}}"></script>	
	<script src="{{URL::asset('plugins/googlemaps/loader.js')}}"></script>	
	<script type="text/javascript">
		$(function() {
	
			'use strict';
			
			var freeData = JSON.parse(`<?php echo $chart_data['free_registration_yearly']; ?>`);
			var freeDataset = Object.values(freeData);
			var paidData = JSON.parse(`<?php echo $chart_data['paid_registration_yearly']; ?>`);
			var paidDataset = Object.values(paidData);

			var ctx = document.getElementById('chart-new-users-year');
			new Chart(ctx, {
				type: 'bar',
				data: {
					labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
					datasets: [{
						label: 'Free Tier Users: ',
						data: freeDataset,
						backgroundColor: '#007bff',
						borderWidth: 1,
						fill: true
					}, {
						label: 'Paid Tier Users: ',
						data: paidDataset,
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
								stepSize: 40,
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


			var paymentData = JSON.parse(`<?php echo $chart_data['current_registered_users']; ?>`);
			var paymentDataset = Object.values(paymentData);

			var ctx = document.getElementById('chart-new-users-month');
			new Chart(ctx, {
				type: 'bar',
				data: {
					labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31'],
					datasets: [{
						label: 'New Registered User: ',
						data: paymentDataset,
						backgroundColor: '#007bff',
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
								stepSize: 20,
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


			var paymentData = JSON.parse(`<?php echo $chart_data['user_countries']; ?>`);
			var sessionData = [];
			for (const [key, value] of Object.entries(paymentData)) {
				sessionData.push([`${key}`, `${value}`]);
			}

			google.charts.load('current', {
				'packages':['geochart'],
				// Note: you will need to get a mapsApiKey for your project.
				// See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
				'mapsApiKey': '{{ config('services.google.maps.key') }}'
			});

			google.charts.setOnLoadCallback(drawRegionsMap);

			function drawRegionsMap() {     

				var options = {colors: ['#007bff']};
				var result = [];

				result.push(['Country', 'Users']);

				sessionData.map(function(row) { result.push([row[0], parseInt(row[1])]); });

				var data = google.visualization.arrayToDataTable(result);
				var chart = new google.visualization.GeoChart(document.getElementById('countries-analytics-chart'));
				chart.draw(data, options);
			}
		});		
	</script>
@endsection