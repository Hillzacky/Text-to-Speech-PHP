@extends('layouts.app')

@section('css')
	<!-- Data Table CSS -->
	<link href="{{URL::asset('plugins/datatable/datatables.min.css')}}" rel="stylesheet" />
	<!-- Sweet Alert CSS -->
	<link href="{{URL::asset('plugins/sweetalert/sweetalert2.min.css')}}" rel="stylesheet" />
@endsection

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0">{{ __('My Background Music') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{route('user.tts')}}"><i class="fa-solid fa-photo-film-music mr-2 fs-12"></i>{{ __('User') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('Background Music') }}</a></li>
			</ol>			
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection

@section('content')						
	<!-- DATA TABLE -->
	<div class="row">
		<div class="col-lg-12 col-md-12 col-xm-12">
			<div class="card overflow-hidden border-0">
				<div class="card-header">
					<h3 class="card-title">{{ __('My Background Music Files') }}</h3>
					<a href="{{ route('user.studio') }}" class="btn btn-primary pr-5 pl-5" id="return-sound">Sound Studio</a>	
				</div>
				<div class="card-body pt-2">
					<!-- SET DATATABLE -->
					<table id='notificationsTable' class='table' width='100%'>
						<thead>
							<tr>
								<th width="8%">{{ __('Uploaded On') }}</th>
								<th width="15%">{{ __('Audio File Name') }}</th>																
								<th width="5%">{{ __('Duration') }}</th>
								<th width="5%">{{ __('Listen') }}</th>
								<th width="5%">{{ __('Download') }}</th>
								<th width="5%">{{ __('File Size') }}</th>
								<th width="5%">{{ __('Format') }}</th>
								<th width="2%">{{ __('Actions') }}</th>
							</tr>
						</thead>
					</table> <!-- END SET DATATABLE -->
				</div>
			</div>
		</div>
	</div>
	<!-- END DATA TABLE -->
@endsection

@section('js')
	<!-- Data Tables JS -->
	<script src="{{URL::asset('plugins/datatable/datatables.min.js')}}"></script>
	<script src="{{URL::asset('plugins/sweetalert/sweetalert2.all.min.js')}}"></script>
	<script src="{{URL::asset('js/audio-player-project.js')}}"></script>
	<script type="text/javascript">
		$(function () {	

			"use strict";
			
			// INITILIZE DATATABLE
			var table = $('#notificationsTable').DataTable({
				"lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
				responsive: true,
				colReorder: true,
				language: {
					"emptyTable": "<div><img id='no-results-img' src='{{ URL::asset('img/files/no-result.png') }}'><br>There are no music files uploaded yet</div>",
					search: "<i class='fa fa-search search-icon'></i>",
					lengthMenu: '_MENU_ ',
					paginate : {
						first    : '<i class="fa fa-angle-double-left"></i>',
						last     : '<i class="fa fa-angle-double-right"></i>',
						previous : '<i class="fa fa-angle-left"></i>',
						next     : '<i class="fa fa-angle-right"></i>'
					}
				},
				"order": [[ 0, "desc" ]],
				pagingType : 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: "{{ route('user.music.list') }}",
				columns: [{
						data: 'created-on',
						name: 'created-on',
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
						data: 'duration',
						name: 'duration',
						orderable: true,
						searchable: true
					},	
					{
						data: 'play',
						name: 'play',
						orderable: true,
						searchable: true
					},
					{
						data: 'download',
						name: 'download',
						orderable: true,
						searchable: true
					},		
					{
						data: 'custom-size',
						name: 'custom-size',
						orderable: true,
						searchable: true
					},
					{
						data: 'type',
						name: 'type',
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
			

			// DELETE SYNTHESIZE RESULT
			$(document).on('click', '.deleteResultButton', function(e) {

				e.preventDefault();

				Swal.fire({
					title: 'Confirm Audio File Deletion',
					text: 'It will permanently delete this background audio file',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Delete',
					reverseButtons: true,
					closeOnCancel: true,
				}).then((result) => {
					if (result.isConfirmed) {
						var formData = new FormData();
						formData.append("id", $(this).attr('id'));
						$.ajax({
							headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
							method: 'post',
							url: 'delete',
							data: formData,
							processData: false,
							contentType: false,
							success: function (data) {
								if (data == 'success') {
									Swal.fire('Audio File Deleted', 'Selected background audio file has been successfully deleted', 'success');	
									$("#notificationsTable").DataTable().ajax.reload();								
								} else {
									Swal.fire('Delete Failed', 'There was an error while deleting this audio file', 'error');
								}      
							},
							error: function(data) {
								Swal.fire({ type: 'error', title: 'Oops...', text: 'Something went wrong!' })
							}
						})
					} 
				})
			});
		});
	</script>
@endsection