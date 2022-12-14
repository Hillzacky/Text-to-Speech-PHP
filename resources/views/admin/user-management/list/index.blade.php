@extends('layouts.app')

@section('css')
	<!-- Data Table CSS -->
	<link href="{{URL::asset('plugins/datatable/datatables.min.css')}}" rel="stylesheet" />
@endsection

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('All Registered Users') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="mdi mdi-account-convert mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.user.dashboard') }}"> {{ __('User Management') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.user.list') }}"> {{ __('User List') }}</a></li>
			</ol>
		</div>
		<div class="page-rightheader">
			<a href="{{ route('admin.user.create') }}" class="btn btn-primary mt-1">{{ __('Create New User') }}</a>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')
	<!-- USERS LIST DATA TABEL -->
	<div class="row">
		<div class="col-lg-12 col-md-12 col-xm-12">
			<div class="card border-0">
				<div class="card-header">
					<h3 class="card-title">{{ __('Users List') }}</h3>
				</div>
				<div class="card-body pt-2">
					<!-- BOX CONTENT -->
					<div class="box-content">
						
						<!-- DATATABLE -->
						<table id='listUsersTable' class='table listUsersTable' width='100%'>
								<thead>
									<tr>		
										<th width="10%">{{ __('Registation Date') }}</th> 	
										<th width="5%">{{ __('Avatar') }}</th> 	
										<th width="10%">{{ __('Full Name') }}</th> 								
										<th width="10%">{{ __('Email') }}</th>
										<th width="10%">{{ __('Status') }}</th>
										<th width="10%">{{ __('Balance') }} ({{ config('payment.default_system_currency') }})</th>         	
										<th width="10%">{{ __('Characters Left') }}</th>     						           	
										<th width="10%">{{ __('Country') }}</th>     						           	
										<th width="10%">{{ __('User Group') }}</th>										    						           	
										<th width="5%">{{ __('Actions') }}</th>        	      	
									</tr>
								</thead>
						</table>
						<!-- END DATATABLE -->
						
					</div> <!-- END BOX CONTENT -->
				</div>
			</div>
		</div>
	</div>
	<!-- END USERS LIST DATA TABEL -->
	<!-- MODAL -->
	<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel"><i class="mdi mdi-alert-circle-outline color-red"></i> {{ __('Confirm User Deletion') }}</h4>
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
			
			var table = $('#listUsersTable').DataTable({
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
				ajax: "{{ route('admin.user.list') }}",
				columns: [{
						data: 'created-on',
						name: 'created-on',
						orderable: true,
						searchable: true
					},
					{
						data: 'avatar',
						name: 'avatar',
						orderable: true,
						searchable: true
					},
					{
						data: 'name',
						name: 'name',
						orderable: true,
						searchable: true
					},
					{
						data: 'custom-email',
						name: 'custom-email',
						orderable: false,
						searchable: true
					},
					{
						data: 'custom-status',
						name: 'custom-status',
						orderable: true,
						searchable: true
					},
					{
						data: 'custom-balance',
						name: 'custom-balance',
						orderable: true,
						searchable: true
					},
					{
						data: 'custom-chars',
						name: 'custom-chars',
						orderable: true,
						searchable: true
					},
					{
						data: 'country',
						name: 'country',
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
						data: 'actions',
						name: 'actions',
						orderable: false,
						searchable: false
					},
				]
			});

			// DELETE CONFIRMATION MODAL
			$(document).on('click', '#deleteUserButton', function(event) {
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