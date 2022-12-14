@extends('layouts.app')

@section('page-header')
	<!--PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('TTS Dashboard') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-wand-sparkles mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.tts.dashboard') }}"> {{ __('TTS Management') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="#"> {{ __('TTS Dashboard') }}</a></li>
			</ol>
		</div>
	</div>
	<!--END PAGE HEADER -->
@endsection

@section('content')	
	<!-- TOP CSP BOX INFO -->
	<div class="row">
		<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
			<div class="card overflow-hidden border-0">
				<div class="card-body">
					<div class="d-flex align-items-end justify-content-between">
						<div>
							<p class=" mb-3 fs-12 font-weight-bold">{{ __('AWS Characters Used') }} <span class="text-muted">({{ __('Current Month') }})</span></p>
							<h2 class="mb-0"><span class="number-font-chars">{{ number_format($vendor_data['aws_month'][0]['data']) }}</span></h2>									
						</div>
						<img src="{{URL::asset('img/csp/aws-lg.png')}}" class="csp-brand-img" alt="AWS Logo">
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Current Year') }} ({{ __('Total Usage') }}):</span>
							<span class="number-font fs-12"><i class="fa fa-bookmark mr-1 text-info"></i>{{ number_format($vendor_data['aws_year'][0]['data']) }}</span>
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
							<p class=" mb-3 fs-12 font-weight-bold">{{ __('GCP Characters Used') }} <span class="text-muted">({{ __('Current Month') }})</span></p>
							<h2 class="mb-0"><span class="number-font-chars">{{ number_format($vendor_data['gcp_month'][0]['data']) }}</span></h2>
						</div>
						<img src="{{URL::asset('img/csp/gcp-lg.png')}}" class="csp-brand-img" alt="GCP Logo">
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Current Year') }} ({{ __('Total Usage') }})</span>
							<span class="number-font fs-12"><i class="fa fa-bookmark mr-1 text-info"></i>{{ number_format($vendor_data['gcp_year'][0]['data']) }}</span>
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
							<p class=" mb-3 fs-12 font-weight-bold">{{ __('Azure Characters Used') }} <span class="text-muted">({{ __('Current Month') }})</span></p>
							<h2 class="mb-0"><span class="number-font-chars">{{ number_format($vendor_data['azure_month'][0]['data']) }}</span></h2>
						</div>
						<img src="{{URL::asset('img/csp/azure-lg.png')}}" class="csp-brand-img" alt="Azure Logo">
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Current Year') }} ({{ __('Total Usage') }})</span>
							<span class="number-font fs-12"><i class="fa fa-bookmark mr-1 text-info"></i>{{ number_format($vendor_data['azure_year'][0]['data']) }}</span>
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
							<p class=" mb-3 fs-12 font-weight-bold">{{ __('IBM Characters Used') }} <span class="text-muted">({{ __('Current Month') }})</span></p>
							<h2 class="mb-0"><span class="number-font-chars">{{ number_format($vendor_data['ibm_month'][0]['data']) }}</span></h2>
						</div>
						<img src="{{URL::asset('img/csp/ibm-lg.png')}}" class="csp-brand-img" alt="IBM Logo">
					</div>
					<div class="d-flex mt-2">
						<div>
							<span class="text-muted fs-12 mr-1">{{ __('Current Year') }} ({{ __('Total Usage') }})</span>
							<span class="number-font fs-12"><i class="fa fa-bookmark mr-1 text-info	"></i>{{ number_format($vendor_data['ibm_year'][0]['data']) }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- END TOP CSP BOX INFO -->

	<!-- CSP ANALYSIS & CHARACTER USAGE METRICS -->
	<div class="row">
		<div class="col-xl-4  col-md-12">
			<div class="card overflow-hidden border-0">
				<div class="card-header">
					<h3 class="card-title">{{ __('Cloud Vendor TTS Service Usage') }} <span class="text-muted">({{ __('Current Year') }})</span></h3>
				</div>
				<div class="card-body">
					<div class="country-card">
						<div class="mb-3">
							<div class="d-flex">
								<span class="fs-12 font-weight-semibold"><img src="{{URL::asset('img/csp/aws-sm.png')}}" class="w-5 h-5 mr-2" alt="">Amazon Web Services</span>
								<div class="ml-auto"><span class="text-success mr-1"></span><span class="number-font fs-14">{{ number_format($vendor_data['aws_year'][0]['data']) }}</span> <span class="text-muted fs-12">(<span id="aws"></span>)</span></div>
							</div>
							<div class="progress h-2  mt-1">
								<div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" id="aws-bar"></div>
							</div>
						</div>
						<div class="mb-3">
							<div class="d-flex">
								<span class="fs-12 font-weight-semibold"><img src="{{URL::asset('img/csp/gcp-sm.png')}}" class="w-5 h-5 mr-2" alt="">Google Cloud Platform</span>
								<div class="ml-auto"><span class="text-success mr-1"></span><span class="number-font fs-14">{{ number_format($vendor_data['gcp_year'][0]['data']) }}</span> <span class="text-muted fs-12">(<span id="gcp"></span>)</span></div>
							</div>
							<div class="progress h-2  mt-1">
								<div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="gcp-bar"></div>
							</div>
						</div>
						<div class="mb-3">
							<div class="d-flex">
								<span class="fs-12 font-weight-semibold"><img src="{{URL::asset('img/csp/azure-sm.png')}}" class="w-5 h-5 mr-2" alt="">Microsoft Azure</span>
								<div class="ml-auto"><span class="text-danger mr-1"></span><span class="number-font fs-14">{{ number_format($vendor_data['azure_year'][0]['data']) }}</span> <span class="text-muted fs-12">(<span id="azure"></span>)</span></div>
							</div>
							<div class="progress h-2  mt-1">
								<div class="progress-bar progress-bar-striped progress-bar-animated bg-info" id="azure-bar"></div>
							</div>
						</div>
						<div class="mb-0">
							<div class="d-flex">
								<span class="fs-12 font-weight-semibold"><img src="{{URL::asset('img/csp/ibm-sm.png')}}" class="w-5 h-5 mr-2 pb-1" alt="">IBM</span>
								<div class="ml-auto"><span class="text-success mr-1"></span><span class="number-font fs-14">{{ number_format($vendor_data['ibm_year'][0]['data']) }}</span> <span class="text-muted fs-12">(<span id="ibm"></span>)</span></div>
							</div>
							<div class="progress h-2  mt-1">
								<div class="progress-bar progress-bar-striped progress-bar-animated bg-dark" id="ibm-bar"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-8 col-md-12">
			<div class="row">
				<div class="col-lg-6 col-md-6 col-xm-12">
					<div class="card overflow-hidden border-0">
						<div class="card-body">
							<p class=" mb-3 mt-1 fs-12">{{ __('Free Characters Used') }} <span class="text-muted">({{ __('Current Month') }})</span></p>
							<h2 class="mb-2 number-font-light">{{ number_format($tts_data_monthly['free_chars'][0]['data']) }}</h2>
							<small class="fs-12 text-muted">{{ __('Compared to Last Month') }} (<span id="free_chars_past"></span>): </small>
							<span class="fs-12" id="free_chars"></span>
						</div>									
					</div>
				</div>
				<div class="col-lg-6 col-md-6 col-xm-12">
					<div class="card overflow-hidden border-0">
						<div class="card-body">
							<p class=" mb-3 mt-1 fs-12">{{ __('Standard Characters Used') }} <span class="text-muted">({{ __('Current Month') }})</span></p>
							<h2 class="mb-2 number-font-light">{{ number_format($tts_data_monthly['standard_chars'][0]['data']) }}</h2>
							<small class="fs-12 text-muted">{{ __('Compared to Last Month') }} (<span id="standard_chars_past"></span>):</small>
							<span class="fs-12" id="standard_chars"></span>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-md-6 col-xm-12">
					<div class="card overflow-hidden border-0">
						<div class="card-body">
							<p class=" mb-3 mt-1 fs-12">{{ __('Paid Characters Used') }} <span class="text-muted">({{ __('Current Month') }})</span></p>
							<h2 class="mb-2 number-font-light">{{ number_format($tts_data_monthly['paid_chars'][0]['data']) }}</h2>
							<small class="fs-12 text-muted">{{ __('Compared to Last Month') }} (<span id="paid_chars_past"></span>): </small>
							<span class="fs-12" id="paid_chars"></span>
						</div>
					</div>
				</div>				
				<div class="col-lg-6 col-md-6 col-xm-12">
					<div class="card overflow-hidden border-0">
						<div class="card-body">
							<p class=" mb-3 mt-1 fs-12">{{ __('Neural Characters Used ') }}<span class="text-muted">({{ __('Current Month') }})</span></p>
							<h2 class="mb-2 number-font-light">{{ number_format($tts_data_monthly['neural_chars'][0]['data']) }}</h2>
							<small class="fs-12 text-muted">{{ __('Compared to Last Month') }} (<span id="neural_chars_past"></span>):</small>
							<span class="fs-12" id="neural_chars"></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- END CSP ANALYSIS & CHARACTER USAGE METRICS -->

	<!-- CURRENT YEAR USAGE ANALYTICS -->
	<div class="row mt-4">
		<div class="col-lg-12 col-md-12">
			<div class="card overflow-hidden border-0">
				<div class="card-header">
					<h3 class="card-title">{{ __('Characters Usage') }} <span class="text-muted">({{ __('Current Year') }})</span></h3>
				</div>
				<div class="card-body">
					<div class="row mb-5 mt-2">
						<div class="col">
							<p class="mb-1 fs-12">{{ __('Total Free Characters') }}</p>
							<h3 class="mb-0 fs-20 number-font">{{ number_format($tts_data_yearly['total_free_chars'][0]['data']) }}</h3>
						</div>
						<div class="col ">
							<p class=" mb-1 fs-12">{{ __('Total Paid Characters') }}</p>
							<h3 class="mb-0 fs-20 number-font">{{ number_format($tts_data_yearly['total_paid_chars'][0]['data']) }}</h3>
						</div>
						<div class="col">
							<p class=" mb-1 fs-12">{{ __('Total Standard Characters') }}</p>
							<h3 class="mb-0 fs-20 number-font">{{ number_format($tts_data_yearly['total_standard_chars'][0]['data']) }}</h3>
						</div>
						<div class="col ">
							<p class=" mb-1 fs-12">{{ __('Total Neural Characters') }}</p>
							<h3 class="mb-0 fs-20 number-font">{{ number_format($tts_data_yearly['total_neural_chars'][0]['data']) }}</h3>
						</div>
						<div class="col ">
							<p class=" mb-1 fs-12">{{ __('Total Audio Files Created') }}</p>
							<h3 class="mb-0 fs-20 number-font">{{ number_format($tts_data_yearly['total_audio_files'][0]['data']) }}</h3>
						</div>
						<div class="col ">
							<p class=" mb-1 fs-12">{{ __('Total Listen Mode Results') }}</p>
							<h3 class="mb-0 fs-20 number-font">{{ number_format($tts_data_yearly['total_listen_results'][0]['data']) }}</h3>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="">
								<canvas id="chart-tts-dashboard" class="h-400"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- CURRENT YEAR USAGE ANALYTICS -->

@endsection

@section('js')
	<!-- Chart JS -->
	<script src="{{URL::asset('plugins/chart/chart.bundle.js')}}"></script>
	<script type="text/javascript">
		$(function() {
	
			'use strict';
			
			var freeData = JSON.parse(`<?php echo $chart_data['free_chars']; ?>`);
			var freeDataset = Object.values(freeData);
			var paidData = JSON.parse(`<?php echo $chart_data['paid_chars']; ?>`);
			var paidDataset = Object.values(paidData);

			var ctx = document.getElementById('chart-tts-dashboard');
			new Chart(ctx, {
				type: 'bar',
				data: {
					labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
					datasets: [{
						label: '{{ __('Free Characters Used') }} ',
						data: freeDataset,
						backgroundColor: '#007bff',
						borderWidth: 1,
						fill: true
					}, {
						label: '{{ __('Paid Characters Used ') }}',
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

			// Vendor Usage
			var aws_year = JSON.parse(`<?php echo $percentage['aws_year']; ?>`);				
			var azure_year = JSON.parse(`<?php echo $percentage['azure_year']; ?>`);			
			var gcp_year= JSON.parse(`<?php echo $percentage['gcp_year']; ?>`);				
			var ibm_year = JSON.parse(`<?php echo $percentage['ibm_year']; ?>`);

			(aws_year[0]['data'] == null) ? aws_year = 0 : aws_year = aws_year[0]['data'];
			(azure_year[0]['data'] == null) ? azure_year = 0 : azure_year = azure_year[0]['data'];
			(gcp_year[0]['data'] == null) ? gcp_year = 0 : gcp_year = gcp_year[0]['data'];
			(ibm_year[0]['data'] == null) ? ibm_year = 0 : ibm_year = ibm_year[0]['data'];

			var aws_total = parseInt(aws_year);	
			var azure_total = parseInt(azure_year);	
			var ibm_total = parseInt(ibm_year);	
			var gcp_total = parseInt(gcp_year);

			var total = aws_total + azure_total + ibm_total + gcp_total;

			var aws = vendorPercentage(aws_total, total);
			var azure = vendorPercentage(azure_total, total);
			var gcp = vendorPercentage(gcp_total, total);
			var ibm = vendorPercentage(ibm_total, total);
			
			document.getElementById('aws').innerHTML = aws;
			document.getElementById('azure').innerHTML = azure;
			document.getElementById('gcp').innerHTML = gcp;
			document.getElementById('ibm').innerHTML = ibm;

			document.getElementById('aws-bar').style.width = aws;
			document.getElementById('azure-bar').style.width = azure;
			document.getElementById('gcp-bar').style.width = gcp;
			document.getElementById('ibm-bar').style.width = ibm;

			function vendorPercentage(value, total){
				if (total == 0) {
           			 return 0;
        		}

        		return ((value / total) * 100).toFixed(2) + '%';
			} 

			// Percentage Difference
			var free_current_month = JSON.parse(`<?php echo $percentage['free_current']; ?>`);				
			var paid_current_month = JSON.parse(`<?php echo $percentage['paid_current']; ?>`);			
			var standard_current_month= JSON.parse(`<?php echo $percentage['standard_current']; ?>`);				
			var neural_current_month = JSON.parse(`<?php echo $percentage['neural_current']; ?>`);

			(free_current_month[0]['data'] == null) ? free_current_month = 0 : free_current_month = free_current_month[0]['data'];
			(paid_current_month[0]['data'] == null) ? paid_current_month = 0 : paid_current_month = paid_current_month[0]['data'];
			(standard_current_month[0]['data'] == null) ? standard_current_month = 0 : standard_current_month = standard_current_month[0]['data'];
			(neural_current_month[0]['data'] == null) ? neural_current_month = 0 : neural_current_month = neural_current_month[0]['data'];

			var free_current_total = parseInt(free_current_month);	
			var paid_current_total = parseInt(paid_current_month);	
			var neural_current_total = parseInt(neural_current_month);	
			var standard_current_total = parseInt(standard_current_month);


			var free_past_month = JSON.parse(`<?php echo $percentage['free_past']; ?>`);				
			var paid_past_month = JSON.parse(`<?php echo $percentage['paid_past']; ?>`);			
			var standard_past_month= JSON.parse(`<?php echo $percentage['standard_past']; ?>`);				
			var neural_past_month = JSON.parse(`<?php echo $percentage['neural_past']; ?>`);

			(free_past_month[0]['data'] == null) ? free_past_month = 0 : free_past_month = free_past_month[0]['data'];
			(paid_past_month[0]['data'] == null) ? paid_past_month = 0 : paid_past_month = paid_past_month[0]['data'];
			(standard_past_month[0]['data'] == null) ? standard_past_month = 0 : standard_past_month = standard_past_month[0]['data'];
			(neural_past_month[0]['data'] == null) ? neural_past_month = 0 : neural_past_month = neural_past_month[0]['data'];

			var free_past_total = parseInt(free_past_month);	
			var paid_past_total = parseInt(paid_past_month);	
			var neural_past_total = parseInt(neural_past_month);	
			var standard_past_total = parseInt(standard_past_month);

			document.getElementById('free_chars_past').innerHTML = new Intl.NumberFormat().format(free_past_total);
			document.getElementById('paid_chars_past').innerHTML = new Intl.NumberFormat().format(paid_past_total);
			document.getElementById('standard_chars_past').innerHTML = new Intl.NumberFormat().format(standard_past_total);
			document.getElementById('neural_chars_past').innerHTML = new Intl.NumberFormat().format(neural_past_total);

			var free_change = vendorPercentageDifference(free_past_total, free_current_total);
			var paid_change = vendorPercentageDifference(paid_past_total, paid_current_total);
			var standard_change = vendorPercentageDifference(standard_past_total, standard_current_total);
			var neural_change = vendorPercentageDifference(neural_past_total, neural_current_total);

			document.getElementById('free_chars').innerHTML = free_change;
			document.getElementById('paid_chars').innerHTML = paid_change;
			document.getElementById('standard_chars').innerHTML = standard_change;
			document.getElementById('neural_chars').innerHTML = neural_change;

			function vendorPercentageDifference(past, current) {
				if (past == 0) {
					var change = (current == 0) ? '<span class="text-muted"> 0% No Change</span>' : '<span class="text-success"> 100% Increase</span>';   					
					return change;
				} else if(current == 0) {
					var change = (past == 0) ? '<span class="text-muted"> 0% No Change</span>' : '<span class="text-danger"> 100% Decrease</span>';
					return change;
				} else if(past == current) {
					var change = '<span class="text-muted"> 0% No Change</span>';
					return change; 
				}

				var difference = current - past;
    			var difference_value;
				var result;
    			
				var totalDifference = Math.abs(difference);
				var change = (totalDifference/past) * 100;				

				if (difference > 0) {
					result = '<span class="text-success">' + change.toFixed(1) + '% Increase</span>';
				} else if(difference < 0) {
					result = '<span class="text-danger">' + change.toFixed(1) + '% Decrease</span>';
				} else {
					difference_value = '<span class="text-muted">' + change.toFixed(1) + '% No Change</span>';
				}

				return result;
			}
		});		
	</script>
@endsection