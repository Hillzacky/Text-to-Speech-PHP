@extends('layouts.app')

@section('css')
	<!-- Data Table CSS -->
	<link href="{{URL::asset('plugins/datatable/datatables.min.css')}}" rel="stylesheet" />
@endsection

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('Subscription Plan Types') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-sack-dollar mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.finance.dashboard') }}"> {{ __('Finance Management') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('Subscription Plan Types') }}</a></li>
			</ol>
		</div>
		<div class="page-rightheader">
			<a href="{{ route('admin.finance.subscriptions.create') }}" class="btn btn-primary mt-1">{{ __('Create New Subscription Plan') }}</a>
		</div>
	</div>	
	<!-- END PAGE HEADER -->
@endsection

@section('content')	
	<div class="row">
		<div class="col-lg-12 col-md-12 col-xm-12">
			<div class="card border-0">
				<div class="card-header">
					<h3 class="card-title">{{ __('Subscription Plan Types') }}</h3>
				</div>
				<div class="card-body pt-2">
					<!-- SET DATATABLE -->
					<table id='subscriptionsAdminTable' class='table' width='100%'>
							<thead>
								<tr>
									<th width="10%">{{ __('Plan Type') }}</th>
									<th width="10%">{{ __('Plan Name') }}</th>
									<th width="7%">{{ __('Price') }}</th>
									<th width="7%">{{ __('Plan Status') }}</th>
									<th width="12%">{{ __('Included Characters') }}</th>																						
									<th width="12%">{{ __('Bonus Characters') }}</th>																						
									<th width="10%">{{ __('Pricing Plan') }}</th>
									<th width="5%">{{ __('Featured') }}</th>
									<th width="5%">{{ __('Free') }}</th>
									<th width="10%">{{ __('Created On') }}</th>
									<th width="5%">{{ __('Action') }}</th>
								</tr>
							</thead>
					</table> <!-- END SET DATATABLE -->

				</div>
			</div>
		</div>
	</div>

	<!-- MODAL -->
	<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel"><i class="mdi mdi-alert-circle-outline color-red"></i> {{ __('Confirm Plan Deletion') }}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="deleteModalBody">
					<div>
						<!-- DELETE CONFIRMATION -->
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- END MODAL -->
@endsection

@section('js')
	<!-- Data Tables JS -->
	<script src="{{URL::asset('plugins/datatable/datatables.min.js')}}"></script>
	<script type="text/javascript">
		$(function () {

			"use strict";
			
			// INITILIZE DATATABLE
			var table = $('#subscriptionsAdminTable').DataTable({
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
				ajax: "{{ route('admin.finance.subscriptions') }}",
				columns: [{
						data: 'custom-plan-type',
						name: 'custom-plan-type',
						orderable: true,
						searchable: true
					},
					{
						data: 'custom-name',
						name: 'custom-name',
						orderable: false,
						searchable: true
					},
					{
						data: 'custom-cost',
						name: 'custom-cost',
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
						data: 'characters',
						name: 'characters',
						orderable: true,
						searchable: true
					},
					{
						data: 'bonus',
						name: 'bonus',
						orderable: true,
						searchable: true
					},
					{
						data: 'custom-pricing',
						name: 'custom-pricing',
						orderable: true,
						searchable: true
					},
					{
						data: 'custom-featured',
						name: 'custom-featured',
						orderable: true,
						searchable: true
					},
					{
						data: 'custom-free',
						name: 'custom-free',
						orderable: true,
						searchable: true
					},
					{
						data: 'created-on',
						name: 'created-on',
						orderable: true,
						searchable: true
					},							
					{
						data: 'actions',
						name: 'actions',
						orderable: false,
						searchable: false
					},
				]
			});

			
			// DELETE CONFIRMATION MODAL
			$(document).on('click', '#deleteSubscriptionButton', function(event) {
				event.preventDefault();
				let href = $(this).attr('data-attr');
				$.ajax({
					url: href
					, beforeSend: function() {
						$('#loader').show();
					},
					// return the result
					success: function(result) {
						$('#deleteModal').modal("show");
						$('#deleteModalBody').html(result).show();
					}
					, error: function(jqXHR, testStatus, error) {
						console.log(error);
						alert("Page " + href + " cannot open. Error:" + error);
						$('#loader').hide();
					}
					, timeout: 8000
				})
			});

		});
	</script>
@endsection