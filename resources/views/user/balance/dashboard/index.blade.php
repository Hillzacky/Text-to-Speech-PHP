@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER-->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('My Balance Dashboard') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{route('user.tts')}}"><i class="fa-solid fa-badge-dollar mr-2 fs-12"></i>{{ __('User') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{url('#')}}"> {{ __('My Balance') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('My Balance Dashboard') }}</a></li>
			</ol>
		</div>
	</div>
	<!--END PAGE HEADER -->
@endsection

@section('content')						
	<!-- TOP BOX INFO -->
	<div class="row">	
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden border-0">
				<div class="card-body">
					<i class="fa fa-bank fs-35 mt-3 float-right"></i>	
					<p class=" mb-3 fs-12 font-weight-bold mt-1">{{ __('Available Balance') }}</p>
					<h2 class="mb-0"><span class="number-font-chars">{!! config('payment.default_system_currency_symbol') !!}{{ number_format(auth()->user()->balance) }}</span></h2>									
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card border-0">
				<div class="card-body">
					<i class="fa fa-language fs-35 mt-3 text-primary float-right"></i>	
					<p class=" mb-3 fs-12 font-weight-bold mt-1">{{ __('Available Characters') }}</p>
					<h2 class="mb-0"><span class="number-font-chars">{{ number_format(auth()->user()->available_chars) }}</span></h2>					
									
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card border-0">
				<div class="card-body">
					<i class="mdi mdi-battery-charging-90 mt-1 text-success fs-35 float-right"></i>	
					<p class=" mb-3 fs-12 font-weight-bold mt-1">{{ __('Total Characters Purchased') }}</p>
					<h2 class="mb-0"><span class="number-font-chars">{{ number_format($total_data['total_purchased_chars'][0]['data']) }}</span></h2>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card border-0">
				<div class="card-body">
					<i class="mdi mdi-battery-charging-30 mt-1 text-danger fs-35 float-right"></i>	
					<p class=" mb-3 fs-12 font-weight-bold mt-1">{{ __('Total Characters Used') }}</p>
					<h2 class="mb-0"><span class="number-font-chars">{{ number_format($total_data['total_chars_used'][0]['data']) }}</span></h2>
				</div>
			</div>
		</div>	
	</div>
	<!-- END TOP BOX INFO -->

	<!-- CHART JS GRAPH ANALYSIS -->
	<div class="row mt-4">
		<div class="col-lg-12 col-md-12">
			<div class="card overflow-hidden border-0">
				<div class="card-header">
					<h3 class="card-title">{{ __('Spending Analysis ') }}<span class="text-muted">({{ __('Current Year') }})</span></h3>
				</div>
				<div class="card-body">
					<div class="row mb-5 mt-2">	
						<div class="col-xl-3 col-12 ">
							<p class=" mb-1 fs-12">{{ __('Total Spending') }}</p>
							<h3 class="mb-0 fs-20 number-font">{!! config('payment.default_system_currency_symbol') !!}{{ number_format((float)$total_data['total_payment'][0]['data'], 2, '.', '') }}</h3>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="">
								<canvas id="chart-user-payments-dashboard" class="h-330"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- END CHART JS GRAPH ANALYSIS -->

@endsection

@section('js')
	<!-- Chart JS -->
	<script src="{{URL::asset('plugins/chart/chart.bundle.js')}}"></script>
	<script type="text/javascript">
		$(function() {

			"use strict";

			// Total Income Chart
			var incomeData = JSON.parse(`<?php echo $chart_data['total_payments']; ?>`);
			var incomeDataset = Object.values(incomeData);
			var ctx = document.getElementById('chart-user-payments-dashboard');
			new Chart(ctx, {
				type: 'bar',
				data: {
					labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
					datasets: [{
						label: 'Total Spending ({{ config('payment.default_system_currency') }}) ',
						data: incomeDataset,
						backgroundColor: '#008c00',
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
								stepSize: 100,
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