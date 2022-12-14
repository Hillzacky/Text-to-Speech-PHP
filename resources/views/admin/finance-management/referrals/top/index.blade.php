@extends('layouts.app')

@section('css')
	<!-- Data Table CSS -->
	<link href="{{URL::asset('plugins/datatable/datatables.min.css')}}" rel="stylesheet" />
@endsection

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('Top Referrers') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="fa-solid fa-sack-dollar mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.finance.dashboard') }}"> {{ __('Finance Management') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.referral.settings') }}"> {{ __('Referral System') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('Top Referrers') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection
@section('content')	
	<div class="row">
		<div class="col-lg-12 col-md-12 col-xm-12">
			<div class="card overflow-hidden border-0">
				<div class="card-header">
					<h3 class="card-title">{{ __('Top Referrers') }} <span class="text-muted">({{ __('All Time') }})</span></h3>
				</div>
				<div class="card-body pt-2">
					<!-- SET DATATABLE -->
					<table id='topReferralTable' class='table' width='100%'>
						<thead>
							<tr>
								<th width="10%" class="fs-10">{{ __('Referrer Name') }}</th>
								<th width="10%" class="fs-10">{{ __('Referrer Email') }}</th>								
								<th width="10%" class="fs-10">{{ __('Referral ID') }}</th>	
								<th width="5%" class="fs-10">{{ __('Group') }}</th>								
								<th width="5%" class="fs-10">{{ __('# of Users') }}</th>								
								<th width="10%" class="fs-10">{{ __('Total TTS Bonus Credits') }}</th>																									
								<th width="10%" class="fs-10">{{ __('Total Commissions') }} ({{ config('payment.default_system_currency') }})</th>
							</tr>
						</thead>
					</table> <!-- END SET DATATABLE -->
				</div>
			</div>
		</div>
	</div>
@endsection

@section('js')
	<!-- Data Tables JS -->
	<script src="{{URL::asset('plugins/datatable/datatables.min.js')}}"></script>
	<script type="text/javascript">
		$(function () {

			"use strict";

			// INITILIZE DATATABLE
			var table = $('#topReferralTable').DataTable({
				"lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
				responsive: true,
				colReorder: true,
				"order": [[ 4, "desc" ]],
				language: {
					search: "<i class='fa fa-search search-icon'></i>",
					lengthMenu: '_MENU_ ',
					paginate : {
						first    : '<i class="fa fa-angle-double-left"></i>',
						last     : '<i class="fa fa-angle-double-right"></i>',
						previous : '<i class="fa fa-angle-left"></i>',
						next     : '<i class="fa fa-angle-right"></i>'
					}
				},
				pagingType : 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: "{{ route('admin.referral.top') }}",
				columns: [{
						data: 'name',
						name: 'name',
						orderable: true,
						searchable: true
					},
					{
						data: 'email',
						name: 'email',
						orderable: true,
						searchable: true
					},															
					{
						data: 'referral_id',
						name: 'referral_id',
						orderable: true,
						searchable: true
					},
					{
						data: 'custom-group',
						name: 'custom-group',
						orderable: true,
						searchable: true
					},						
					{
						data: 'total_referred',
						name: 'total_referred',
						orderable: true,
						searchable: true
					},	
					{
						data: 'total_credits',
						name: 'total_credits',
						orderable: true,
						searchable: true
					},			
					{
						data: 'custom_total_commission',
						name: 'custom_total_commission',
						orderable: false,
						searchable: false
					},
				]
			});
		});
	</script>
@endsection