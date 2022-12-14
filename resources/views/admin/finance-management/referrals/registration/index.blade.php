@extends('layouts.app')

@section('css')
	<!-- Data Table CSS -->
	<link href="{{URL::asset('plugins/datatable/datatables.min.css')}}" rel="stylesheet" />
@endsection

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('Referral Registrations') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="fa-solid fa-sack-dollar mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.finance.dashboard') }}"> {{ __('Finance Management') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.referral.settings') }}"> {{ __('Referral System') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('Referral Registrations') }}</a></li>
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
					<h3 class="card-title">{{ __('Referral Registrations') }} <span class="text-muted">({{ __('All Time') }})</span></h3>
				</div>
				<div class="card-body pt-2">
					<!-- DATATABLE -->
					<table id='listUsersTable' class='table listUsersTable' width='100%'>
						<thead>
							<tr>		
								<th width="10%" class="fs-10">{{ __('Registation Date') }}</th> 								
								<th width="10%" class="fs-10">{{ __('User Name') }}</th>   						           	
								<th width="10%" class="fs-10">{{ __('User Email') }}</th> 
								<th width="10%" class="fs-10">{{ __('Group') }}</th>  						           	
								<th width="10%" class="fs-10">{{ __('Referrer Email') }}</th>   						           	
								<th width="10%" class="fs-10">{{ __('Referrer TTS Bonus') }}</th>   						           	
								<th width="10%" class="fs-10">{{ __('Status') }}</th>   						           	
								<th width="10%" class="fs-10">{{ __('Country') }}</th>   					           	      	      	
							</tr>
						</thead>
					</table>
					<!-- END DATATABLE -->
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

			var registrationTable = $('#listUsersTable').DataTable({
				"lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
				responsive: true,
				colReorder: true,
				"order": [[ 0, "desc" ]],
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
				ajax: "{{ route('admin.referral.registration') }}",
				columns: [{
						data: 'created-on',
						name: 'created-on',
						orderable: true,
						searchable: true
					},
					{
						data: 'name',
						name: 'name',
						orderable: false,
						searchable: true
					},
					{
						data: 'email',
						name: 'email',
						orderable: false,
						searchable: true
					},
					{
						data: 'custom-group',
						name: 'custom-group',
						orderable: false,
						searchable: true
					},
					{
						data: 'referrer_email',
						name: 'referrer_email',
						orderable: true,
						searchable: true
					},
					{
						data: 'credits',
						name: 'credits',
						orderable: true,
						searchable: true
					},
					{
						data: 'custom-status',
						name: 'custom-status',
						orderable: true,
						searchable: true
					},
					{
						data: 'country',
						name: 'country',
						orderable: true,
						searchable: true
					},

				]
			});
		});
	</script>
@endsection