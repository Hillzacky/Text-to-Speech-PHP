@extends('layouts.app')

@section('page-header')
	<!-- PAGE HEADER-->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('Finance Dashboard') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-sack-dollar mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.finance.dashboard') }}"> {{ __('Finance Management') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="#"> {{ __('Finance Dashboard') }}</a></li>
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
					<div class="d-flex align-items-end justify-content-between">
						<div>
							<p class=" mb-3 fs-12 font-weight-bold">{{ __('Total Income') }} <span class="text-muted">({{ __('Current Month') }})</span></p>
							<h2 class="mb-0"><span class="number-font fs-20">{!! config('payment.default_system_currency_symbol') !!}{{ number_format((float)$total_data_monthly['income_current_month'][0]['data'], 2) }}</span><span class="ml-2 text-muted fs-11 data-percentage-change"><span class="fs-12" id="income_difference"></span> {{ __('this month') }}</span></h2>
						</div>
						<span class="text-success fs-50 mt-m1"><i class="zmdi zmdi-money"></i></span>
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Last Month') }}:</span>
							<span class="number-font fs-12"><i class="fa fa-chain mr-1 text-success"></i>{!! config('payment.default_system_currency_symbol') !!}{{ number_format((float)$total_data_monthly['income_past_month'][0]['data'], 2, '.', '') }}</span>
						</div>
						<div class="ml-auto">
							<span class="text-muted fs-12 mr-1">{{ __('Current Year') }}:</span>
							<span class="number-font fs-12"><i class="fa fa-bookmark mr-1 text-success"></i>{!! config('payment.default_system_currency_symbol') !!}{{ number_format((float)$total_data_yearly['total_income'][0]['data'], 2, '.', '') }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden border-0">
				<div class="card-body">
					<div class="d-flex align-items-end justify-content-between">
						<div>
							<p class=" mb-3 fs-12 font-weight-bold">{{ __('Total Estimated Spending') }} <span class="text-muted">({{ __('Current Month') }})</span></p>
							<h2 class="mb-0"><span class="number-font fs-20">${{ number_format((float)$total_data_monthly['spending_current_month'], 2, '.', '') }}</span><span class="ml-2 text-muted fs-11 data-percentage-change"><span class="fs-12" id="spending_difference"></span> {{ __('this month') }}</span></h2>
						</div>
						<span class="text-secondary fs-50 mt-m1"><i class="zmdi zmdi-money-off"></i></span>
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Last Month') }}:</span>
							<span class="number-font fs-12"><i class="fa fa-chain mr-1 text-danger"></i>${{ number_format((float)$total_data_monthly['spending_past_month'], 2, '.', '') }}</span>
						</div>
						<div class="ml-auto">
							<span class="text-muted fs-12 mr-1">{{ __('Current Year') }}:</span>
							<span class="number-font fs-12"><i class="fa fa-bookmark mr-1 text-danger"></i>${{ number_format((float)$total_data_yearly['total_spending'], 2, '.', '') }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden border-0">
				<div class="card-body">
					<div class="d-flex align-items-end justify-content-between">
						<div>
							<p class=" mb-3 fs-12 font-weight-bold">{{ __('Total Chars Purchased') }} <span class="text-muted">({{ __('Current Month') }})</span></p>
							<h2 class="mb-0"><span class="number-font fs-20">{{ number_format($total_data_monthly['purchased_chars'][0]['data']) }}</span><span class="ml-2 text-muted fs-11 data-percentage-change"><span class="fs-12" id="purchased_chars_difference"></span> {{ __('this month') }}</span></h2>

						</div>
						<span class="text-primary fs-50 mt-m1"><i class="mdi mdi-account-switch"></i></span>
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Last Month') }}:</span>
							<span class="number-font fs-12"><i class="fa fa-chain mr-1 text-success"></i>{{ number_format($total_data_monthly['purchased_chars_past'][0]['data']) }}</span>
						</div>
						<div class="ml-auto">
							<span class="text-muted fs-12 mr-1">{{ __('Current Year') }}:</span>
							<span class="number-font fs-12"><i class="fa fa-bookmark mr-1 text-success"></i>{{ number_format($total_data_yearly['total_purchased_chars'][0]['data']) }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden border-0">
				<div class="card-body">
					<div class="d-flex align-items-end justify-content-between">
						<div>
							<p class=" mb-3 fs-12 font-weight-bold">{{ __('Total Purchased Chars Used') }} <span class="text-muted">({{ __('Current Month') }})</span></p>
							<h2 class="mb-0"><span class="number-font fs-20">{{ number_format($total_data_monthly['paid_chars'][0]['data']) }}</span><span class="ml-2 text-muted fs-11 data-percentage-change"><span class="fs-12" id="purchased_chars_used_difference"></span> {{ __('this month') }}</span></h2>
						</div>
						<span class="text-info fs-50 mt-m1"><i class="mdi mdi-account-plus"></i></span>
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Last Month') }}:</span>
							<span class="number-font fs-12"><i class="fa fa-chain mr-1 text-success"></i>{{ number_format($total_data_monthly['paid_chars_past'][0]['data']) }}</span>
						</div>
						<div class="ml-auto">
							<span class="text-muted fs-12 mr-1">{{ __('Current Year') }}:</span>
							<span class="number-font fs-12"><i class="fa fa-bookmark mr-1 text-success"></i>{{ number_format($total_data_yearly['total_purchased_chars_used'][0]['data']) }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden border-0">
				<div class="card-body">
					<div class="d-flex align-items-end justify-content-between">
						<div>
							<p class=" mb-3 fs-12 font-weight-bold">{{ __('Estimated AWS Spending') }} <span class="text-muted">({{ __('Current Month') }})</span></p>
							<h2 class="mb-0"><span class="number-font-chars">${{ number_format((float)$total_data_monthly['aws_current_month'], 2, '.', '') }}</span></h2>									
						</div>
						<img src="{{URL::asset('img/csp/aws-lg.png')}}" class="csp-brand-img" alt="AWS Logo">
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Current Year') }}: </span>
							<span class="number-font fs-12"><i class="fa fa-bookmark mr-1 text-danger"></i>${{ number_format((float)$total_data_yearly['aws_current_year'], 2, '.', '') }}</span>
						</div>
					</div>
					<div class="d-flex">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Standard Characters Used') }} ({{ __('Current Year') }}): </span>
							<span class="number-font fs-12">{{ number_format($total_data_yearly['aws_current_year_std'][0]['data']) }}</span>
						</div>
					</div>
					<div class="d-flex">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Neural Characters Used') }} ({{ __('Current Year') }}): </span>
							<span class="number-font fs-12">{{ number_format($total_data_yearly['aws_current_year_nrl'][0]['data']) }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden border-0">
				<div class="card-body">
					<div class="d-flex align-items-end justify-content-between">
						<div>
							<p class=" mb-3 fs-12 font-weight-bold">{{ __('Estimated GCP Spending') }} <span class="text-muted">({{ __('Current Month') }})</span></p>
							<h2 class="mb-0"><span class="number-font-chars">${{ number_format((float)$total_data_monthly['gcp_current_month'], 2, '.', '') }}</span></h2>
						</div>
						<img src="{{URL::asset('img/csp/gcp-lg.png')}}" class="csp-brand-img" alt="GCP Logo">
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Current Year') }}:</span>
							<span class="number-font fs-12"><i class="fa fa-bookmark mr-1 text-danger"></i>${{ number_format((float)$total_data_yearly['gcp_current_year'], 2, '.', '') }}</span>
						</div>
					</div>
					<div class="d-flex">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Standard Characters Used') }} ({{ __('Current Year') }}): </span>
							<span class="number-font fs-12">{{ number_format($total_data_yearly['gcp_current_year_std'][0]['data']) }}</span>
						</div>
					</div>
					<div class="d-flex">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Neural Characters Used') }} ({{ __('Current Year') }}): </span>
							<span class="number-font fs-12">{{ number_format($total_data_yearly['gcp_current_year_nrl'][0]['data']) }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden border-0">
				<div class="card-body">
					<div class="d-flex align-items-end justify-content-between">
						<div>
							<p class=" mb-3 fs-12 font-weight-bold">{{ __('Estimated Azure Spending') }} <span class="text-muted">({{ __('Current Month') }})</span></p>
							<h2 class="mb-0"><span class="number-font-chars">${{ number_format((float)$total_data_monthly['azure_current_month'], 2, '.', '') }}</span></h2>
						</div>
						<img src="{{URL::asset('img/csp/azure-lg.png')}}" class="csp-brand-img" alt="Azure Logo">
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Current Year') }}: </span>
							<span class="number-font fs-12"><i class="fa fa-bookmark mr-1 text-danger"></i>${{ number_format((float)$total_data_yearly['azure_current_year'], 2, '.', '') }}</span>
						</div>
					</div>
					<div class="d-flex">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Standard Characters Used') }} ({{ __('Current Year') }}): </span>
							<span class="number-font fs-12">{{ number_format($total_data_yearly['azure_current_year_std'][0]['data']) }}</span>
						</div>
					</div>
					<div class="d-flex">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Neural Characters Used') }} ({{ __('Current Year') }}): </span>
							<span class="number-font fs-12">{{ number_format($total_data_yearly['azure_current_year_nrl'][0]['data']) }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden border-0">
				<div class="card-body">
					<div class="d-flex align-items-end justify-content-between">
						<div>
							<p class=" mb-3 fs-12 font-weight-bold">{{ __('Estimated IBM Spending ') }}<span class="text-muted">({{ __('Current Month') }})</span></p>
							<h2 class="mb-0"><span class="number-font-chars">${{ number_format((float)$total_data_monthly['ibm_current_month'], 2, '.', '') }}</span></h2>
						</div>
						<img src="{{URL::asset('img/csp/ibm-lg.png')}}" class="csp-brand-img" alt="IBM Logo">
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Current Year') }}: </span>
							<span class="number-font fs-12"><i class="fa fa-bookmark mr-1 text-danger"></i>${{ number_format((float)$total_data_yearly['ibm_current_year'], 2, '.', '') }}</span>
						</div>
					</div>					
					<div class="d-flex">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Neural Characters Used') }} ({{ __('Current Year') }}): </span>
							<span class="number-font fs-12">{{ number_format($total_data_yearly['ibm_current_year_nrl'][0]['data']) }}</span>
						</div>
					</div>
					<div class="d-flex">
						<div>
							<span class="text-muted fs-12 mr-1"></span>
							<span class="number-font fs-12"></span>
						</div>
					</div>
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
					<h3 class="card-title">{{ __('Income Analysis') }} <span class="text-muted">({{ __('Current Year') }})</span></h3>
				</div>
				<div class="card-body">
					<div class="row mb-5 mt-2">	
						<div class="col-xl-3 col-12 ">
							<p class=" mb-1 fs-12">{{ __('Total Income') }}</p>
							<h3 class="mb-0 fs-20 number-font">{!! config('payment.default_system_currency_symbol') !!}{{ number_format((float)$total_data_yearly['total_income'][0]['data'], 2, '.', '') }}</h3>
						</div>
						<div class="col-xl-3 col-12 ">
							<p class=" mb-1 fs-12">{{ __('Total Estimated Spending') }}</p>
							<h3 class="mb-0 fs-20 number-font">${{ number_format((float)$total_data_yearly['total_spending'], 2, '.', '') }}</h3>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="">
								<canvas id="chart-income-dashboard" class="h-330"></canvas>
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
	
			'use strict';
			
			var incomeData = JSON.parse(`<?php echo $chart_data['total_income']; ?>`);
			var spendingData = JSON.parse(`<?php echo $chart_data['total_spending']; ?>`);
			var incomeDataset = Object.values(incomeData);
			var spendingDataset = Object.values(spendingData);

			var ctx = document.getElementById('chart-income-dashboard');
			new Chart(ctx, {
				type: 'bar',
				data: {
					labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
					datasets: [{
						label: '{{ __('Total Income') }} ({{ config('payment.default_system_currency') }}) ',
						data: incomeDataset,
						backgroundColor: '#007bff',
						borderWidth: 1,
						fill: true
					}, {
						label: '{{ __('Total Estimated Spending') }} (USD) ',
						data: spendingDataset,
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
								stepSize: 500,
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


			// Percentage Difference				
			var income_current_month = JSON.parse(`<?php echo $percentage['income_current']; ?>`);			
			var income_past_month = JSON.parse(`<?php echo $percentage['income_past']; ?>`);
			var spending_current_month = JSON.parse(`<?php echo $percentage['spending_current']; ?>`);	
			var spending_past_month = JSON.parse(`<?php echo $percentage['spending_past']; ?>`);
			var purchased_current_month = JSON.parse(`<?php echo $percentage['purchased_current']; ?>`);			
			var purchased_past_month = JSON.parse(`<?php echo $percentage['purchased_past']; ?>`);
			var purchased_used_current_month = JSON.parse(`<?php echo $percentage['purchased_used_current']; ?>`);			
			var purchased_used_past_month = JSON.parse(`<?php echo $percentage['purchased_used_past']; ?>`);

			(income_current_month[0]['data'] == null) ? income_current_month = 0 : income_current_month = income_current_month[0]['data'];
			(income_past_month[0]['data'] == null) ? income_past_month = 0 : income_past_month = income_past_month[0]['data'];
			(spending_current_month == null) ? spending_current_month = 0.0 : spending_current_month = spending_current_month;
			(spending_past_month == null) ? spending_past_month = 0.0 : spending_past_month = spending_past_month;
			(purchased_current_month[0]['data'] == null) ? purchased_current_month = 0 : purchased_current_month = purchased_current_month[0]['data'];
			(purchased_past_month[0]['data'] == null) ? purchased_past_month = 0 : purchased_past_month = purchased_past_month[0]['data'];
			(purchased_used_current_month[0]['data'] == null) ? purchased_used_current_month = 0 : purchased_used_current_month = purchased_used_current_month[0]['data'];
			(purchased_used_past_month[0]['data'] == null) ? purchased_used_past_month = 0 : purchased_used_past_month = purchased_used_past_month[0]['data'];

			var income_current_total = parseInt(income_current_month);	
			var income_past_total = parseInt(income_past_month);
			var purchased_current_total = parseInt(purchased_current_month);	
			var purchased_past_total = parseInt(purchased_past_month);
			var purchased_used_current_total = parseInt(purchased_used_current_month);	
			var purchased_used_past_total = parseInt(purchased_used_past_month);

			var income_change = mainPercentageDifference(income_past_total, income_current_total);
			var spending_change = mainPercentageDifference(spending_past_month, spending_current_month);
			var purchased_change = mainPercentageDifference(purchased_past_total, purchased_current_total);
			var purchased_used_change = mainPercentageDifference(purchased_used_past_total, purchased_used_current_total);

			document.getElementById('income_difference').innerHTML = income_change;
			document.getElementById('spending_difference').innerHTML = spending_change;
			document.getElementById('purchased_chars_difference').innerHTML = purchased_change;
			document.getElementById('purchased_chars_used_difference').innerHTML = purchased_used_change;

			function mainPercentageDifference(past, current) {
				if (past == 0) {
					var change = (current == 0) ? '<span class="text-muted"> 0%</span>' : '<span class="text-success"><i class="fa fa-caret-up"></i> 100%</span>';   					
					return change;
				} else if(current == 0) {
					var change = (past == 0) ? '<span class="text-muted"> 0%</span>' : '<span class="text-danger"><i class="fa fa-caret-down"></i> 100%</span>';
					return change;
				} else if(past == current) {
					var change = '<span class="text-muted"> 0%</span>';
					return change; 
				}

				var difference = current - past;
    			var difference_value, result;

				var totalDifference = Math.abs(difference);
				var change = (totalDifference/past) * 100;				

				if (difference > 0) { result = '<span class="text-success"><i class="fa fa-caret-up"></i> ' + change.toFixed(1) + '%</span>'; }
				else if(difference < 0) {result = '<span class="text-danger"><i class="fa fa-caret-down"></i> ' + change.toFixed(1) + '%</span>'; }
				else { difference_value = '<span class="text-muted"> ' + change.toFixed(1) + '%</span>'; }				

				return result;
			}
		});		
	</script>
@endsection