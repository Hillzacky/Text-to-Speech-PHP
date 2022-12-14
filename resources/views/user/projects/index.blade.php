@extends('layouts.app')
@section('css')
	<!-- Data Table CSS -->
	<link href="{{URL::asset('plugins/datatable/datatables.min.css')}}" rel="stylesheet" />
	<!-- Awselect CSS -->
	<link href="{{URL::asset('plugins/awselect/awselect.min.css')}}" rel="stylesheet" />
	<!-- Green Audio Player CSS -->
	<link href="{{ URL::asset('plugins/audio-player/green-audio-player.css') }}" rel="stylesheet" />
	<!-- Sweet Alert CSS -->
	<link href="{{URL::asset('plugins/sweetalert/sweetalert2.min.css')}}" rel="stylesheet" />	
@endsection
@section('page-header')
<!-- PAGE HEADER -->
<div class="page-header mt-5-7">
	<div class="page-leftheader">
		<h4 class="page-title mb-0">{{ __('My Text to Speech Projects') }}</h4>
		<ol class="breadcrumb mb-2">
			<li class="breadcrumb-item"><a href="{{url('/' . $page='#')}}"><i class="fa-solid fa-boxes-packing mr-2 fs-12"></i>{{ __('User') }}</a></li>
			<li class="breadcrumb-item active" aria-current="page"><a href="{{url('/' . $page='#')}}"> {{ __('My TTS Projects') }}</a></li>
		</ol>
	</div>
</div>
<!-- END PAGE HEADER -->
@endsection
@section('content')	
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="card border-0">	
				<div class="card-header">
					<h3 class="card-title">{{ __('TTS Projects') }}</h3>
				</div>			
				<div class="card-body pt-5">
					<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="row">
								<div class="col-md-7 col-sm-12">
									<div class="form-group" id="tts-project">
										<select id="project" name="project" data-placeholder="{{ __('Select Project Name') }}" data-callback="changeProjectName">	
											<option value="all"> All Projects</option>
											@foreach ($projects as $project)
												<option value="{{ $project->name }}" @if (strtolower(auth()->user()->project) == strtolower($project->name)) selected @endif> {{ ucfirst($project->name) }}</option>
											@endforeach											
										</select>
									</div>
								</div>
								<div class="col-md-3 col-sm-12">
									<div class="dropdown">
										<button class="btn btn-special create-project mr-4" type="button" id="add-project" data-tippy-content="{{ __('Create New Project') }}" ><i class="mdi mdi-animation"></i></button>
										<button class="btn btn-special create-project mr-4" type="button" id="default-project" data-tippy-content="{{ __('Set Default Project') }}"><i class="mdi mdi-arrange-bring-to-front"></i></button>
										<button class="btn btn-special create-project" type="button" id="delete-project" data-tippy-content="{{ __('Delete Project') }}"><i class="mdi mdi-delete"></i></button>												
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-6 col-sm-12">
							<div class="row">
								<div class="col-md-4 col-sm-12 pt-2">
									<span class="fs-12 font-weight-bold">{{ __('Total Synthesize Results') }}: <span id="total-results">{{ $data['results']}}</span></span>												
								</div>
								<div class="col-md-4 col-sm-12 pt-2">
									<span class="fs-12 font-weight-bold">{{ __('Total Characters Used') }}: <span id="total-chars">{{ $data['chars']}}</span></span>												
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="card border-0">
				
				<div class="card-body pt-2">
					<!-- SET DATATABLE -->
					<table id='resultsTable' class='table' width='100%'>
							<thead>
								<tr>
									<th width="3%"></th>
									<th width="10%">{{ __('Created On') }}</th> 
									<th width="15%">{{ __('Text Title') }}</th> 
									<th width="10%">{{ __('Language') }}</th>
									<th width="7%">{{ __('Voice') }}</th>
									<th width="7%">{{ __('Gender') }}</th>
									<th width="7%">{{ __('Voice Engine') }}</th>
									<th width="4%"><i class="fa fa-music fs-14"></i></th>							
									<th width="4%"><i class="fa fa-cloud-download fs-14"></i></th>								
									<th width="5%">{{ __('Format') }}</th>	
									<th width="5%">{{ __('Chars') }}</th>								           								    						           	
									<th width="5%">{{ __('Actions') }}</th>
								</tr>
							</thead>
					</table> <!-- END SET DATATABLE -->
				</div>
			</div>
		</div>
	</div>

	<!-- SET DEFAULT PROJECT MODAL -->
	<div class="modal fade" id="defaultProjectModal" tabindex="-1" role="dialog" aria-labelledby="projectModalLabel" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog modal-dialog-centered modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header mb-1">
					<h4 class="modal-title" id="myModalLabel"><i class="mdi mdi-arrange-bring-to-front"></i> {{ __('Select Default Project') }}</h4>
					<button type="button" class="close" data-dismiss="modal" id="modal-close" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="{{ route('user.project.update') }}" method="POST" enctype="multipart/form-data">
					@method('PUT')
					@csrf
					<div class="modal-body pb-0 pl-6 pr-6">        
						<div class="input-box">	
							<select id="set-project" name="project" data-placeholder="Select Default Project:">			
								@foreach ($projects as $project)
									<option value="{{ $project->name }}" @if (strtolower(auth()->user()->project) == strtolower($project->name)) selected @endif> {{ ucfirst($project->name) }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="modal-footer pr-6 pb-3 modal-footer-awselect">
						<button type="button" class="btn btn-cancel mb-4" data-dismiss="modal">{{ __('Cancel') }}</button>
						<button type="submit" class="btn btn-primary mb-4" id="new-project-button">{{ __('Save') }}</button>
					</div>
				</form>				
			</div>
		</div>
	</div>
	<!-- END SET DEFAULT PROJECT MODAL -->

	<!-- DELETE PROJECT MODAL -->
	<div class="modal fade" id="deleteProjectModal" tabindex="-1" role="dialog" aria-labelledby="projectModalLabel" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog modal-dialog-centered modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header mb-1">
					<h4 class="modal-title" id="myModalLabel"><i class="mdi mdi-alert-circle-outline color-red"></i> {{ __('Delete Project') }}</h4>
					<button type="button" class="close" data-dismiss="modal" id="modal-close" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="{{ route('user.project.delete') }}" method="POST" enctype="multipart/form-data">
					@method('DELETE')
					@csrf
					<div class="modal-body pb-0 pl-6 pr-6">       
						<p class="mb-4">{{ __('Warning! This will also delete all synthesize results under selected project name') }}</p> 
						<div class="input-box">	
							<select id="del-project" name="project" data-placeholder="Select Project to Delete:">			
								@foreach ($projects as $project)
									<option value="{{ $project->name }}" @if (strtolower(auth()->user()->project) == strtolower($project->name)) selected @endif> {{ ucfirst($project->name) }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="modal-footer pr-6 pb-3 modal-footer-awselect">
						<button type="button" class="btn btn-cancel mb-4" data-dismiss="modal">{{ __('Cancel') }}</button>
						<button type="submit" class="btn btn-confirm mb-4" id="new-project-button">{{ __('Delete') }}</button>
					</div>
				</form>				
			</div>
		</div>
	</div>
	<!-- END DELETE PROJECT MODAL -->
@endsection

@section('js')
	<!-- Green Audio Player JS -->
	<script src="{{ URL::asset('plugins/audio-player/green-audio-player.js') }}"></script>
	<script src="{{ URL::asset('js/audio-player.js') }}"></script>
	<!-- Data Tables JS -->
	<script src="{{URL::asset('plugins/datatable/datatables.min.js')}}"></script>
	<script src="{{URL::asset('plugins/datatable/dataTables.checkboxes.min.js')}}"></script>
	<script src="{{URL::asset('plugins/sweetalert/sweetalert2.all.min.js')}}"></script>
	<!-- Awselect JS -->
	<script src="{{URL::asset('plugins/awselect/awselect.min.js')}}"></script>
	<script src="{{URL::asset('js/awselect.js')}}"></script>
	<script type="text/javascript">
	var table;
		$(function () {

			"use strict";

			$('#default-project').on('click', function() {
				$('#defaultProjectModal').modal('show');
			});

			$('#delete-project').on('click', function() {
				$('#deleteProjectModal').modal('show');
			});

			function format(d) {
				// `d` is the original data object for the row
				return '<div class="slider">'+
						'<table class="details-table">'+
							'<tr>'+
								'<td class="details-title" width="10%">Text Clean:</td>'+
								'<td>'+ d.text +'</td>'+
							'</tr>'+
							'<tr>'+
								'<td class="details-title" width="10%">Text Raw:</td>'+
								'<td>'+ d.text_raw +'</td>'+
							'</tr>'+
							'<tr>'+
								'<td class="details-result" width="10%">Synthesized Result:</td>'+
								'<td><audio controls preload="none">' +
									'<source src="'+ d.result +'" type="audio/mpeg">' +
								'</audio></td>'+
							'</tr>'+
						'</table>'+
					'</div>';
			}

			table = $('#resultsTable').DataTable({
				"lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
				responsive: {
					details: {type: 'column'}
				},
				colReorder: true,		
				language: {
					"emptyTable": "<div><img id='no-results-img' src='{{ URL::asset('img/files/no-result.png') }}'><br>Project does not have any results stored yet</div>",
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
				ajax: "{{ route('user.projects') }}",
				columns: [{
						"className":      'details-control',
						"orderable":      false,
						"searchable":     false,
						"data":           null,
						"defaultContent": ''
					},
					{
						data: 'created-on',
						name: 'created-on',
						orderable: true,
						searchable: true
					},		
					{
						data: 'title',
						name: 'title',
						orderable: true,
						searchable: true
					},			
					{
						data: 'custom-language',
						name: 'custom-language',
						orderable: true,
						searchable: true
					},
					{
						data: 'voice',
						name: 'voice',
						orderable: true,
						searchable: true
					},
					{
						data: 'gender',
						name: 'gender',
						orderable: true,
						searchable: true
					},
					{
						data: 'custom-voice-type',
						name: 'custom-voice-type',
						orderable: true,
						searchable: true
					},
					{
						data: 'single',
						name: 'single',
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
						data: 'result_ext',
						name: 'result_ext',
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
						data: 'actions',
						name: 'actions',
						orderable: false,
						searchable: false
					},
				]
			});


			$('#resultsTable tbody').on('click', 'td.details-control', function () {
				var tr = $(this).closest('tr');
				var row = table.row( tr );
		
				if ( row.child.isShown() ) {
					// This row is already open - close it
					$('div.slider', row.child()).slideUp( function () {
						row.child.hide();
						tr.removeClass('shown');
					} );
				}
				else {
					// Open this row
					row.child( format(row.data()), 'no-padding' ).show();
					tr.addClass('shown');
		
					$('div.slider', row.child()).slideDown();
				}
			});


			// CREATE NEW PROJECT
			$(document).on('click', '#add-project', function(e) {

				e.preventDefault();

				Swal.fire({
					title: 'Create New Project',
					showCancelButton: true,
					confirmButtonText: 'Create',
					reverseButtons: true,
					closeOnCancel: true,
					input: 'text',
				}).then((result) => {
					if (result.value) {
						var formData = new FormData();
						formData.append("new-project", result.value);
						$.ajax({
							headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
							method: 'post',
							url: 'project',
							data: formData,
							processData: false,
							contentType: false,
							success: function (data) {
								if (data['status'] == 'success') {
									Swal.fire('Project Created', 'New project has been successfully created', 'success');	
									location.reload();								
								} else {
									Swal.fire('Project Creation Error', data['message'], 'error');
								}      
							},
							error: function(data) {
								Swal.fire({ type: 'error', title: 'Oops...', text: 'Something went wrong!' })
							}
						})
					} else if (result.dismiss !== Swal.DismissReason.cancel) {
						Swal.fire('No Project Name Entered', 'Make sure to provide a new project name before creating', 'error')
					}
				})
			});


			// DELETE SYNTHESIZE RESULT
			$(document).on('click', '.deleteResultButton', function(e) {

				e.preventDefault();

				Swal.fire({
					title: 'Confirm Result Deletion',
					text: 'It will permanently delete this synthesize result',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Delete',
					reverseButtons: true,
				}).then((result) => {
					if (result.isConfirmed) {
						var formData = new FormData();
						formData.append("id", $(this).attr('id'));
						$.ajax({
							headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
							method: 'post',
							url: 'project/result/delete',
							data: formData,
							processData: false,
							contentType: false,
							success: function (data) {
								if (data == 'success') {
									Swal.fire('Result Deleted', 'Synthesize result has been successfully deleted', 'success');	
									$("#resultsTable").DataTable().ajax.reload();								
								} else {
									Swal.fire('Delete Failed', 'There was an error while deleting this result', 'error');
								}      
							},
							error: function(data) {
								Swal.fire('Oops...','Something went wrong!', 'error')
							}
						})
					} 
				})
			});
		});


		// CHANGE PROJECT NAME
		function changeProjectName(value) {
			
			$.get("{{ route('user.projects.change') }}", { group: value}, 
				function(data){
					table = $('#resultsTable').DataTable({
						"lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
						responsive: {
							details: {type: 'column'}
						},
						colReorder: true,
						destroy: true,
						language: {
							"emptyTable": "<div><img id='no-results-img' src='{{ URL::asset('img/files/no-result.png') }}'><br>Project does not have any results stored yet</div>",
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
						data: data['data'],
						columns: [{
								"className":      'details-control',
								"orderable":      false,
								"searchable":     false,
								"data":           null,
								"defaultContent": ''
							},
							{
								data: 'created-on',
								name: 'created-on',
								orderable: true,
								searchable: true
							},		
							{
								data: 'title',
								name: 'title',
								orderable: true,
								searchable: true
							},			
							{
								data: 'custom-language',
								name: 'custom-language',
								orderable: true,
								searchable: true
							},
							{
								data: 'voice',
								name: 'voice',
								orderable: true,
								searchable: true
							},
							{
								data: 'gender',
								name: 'gender',
								orderable: true,
								searchable: true
							},
							{
								data: 'custom-voice-type',
								name: 'custom-voice-type',
								orderable: true,
								searchable: true
							},
							{
								data: 'single',
								name: 'single',
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
								data: 'result_ext',
								name: 'result_ext',
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
								data: 'actions',
								name: 'actions',
								orderable: false,
								searchable: false
							},
						]	
				});

			}).fail(function(){
				console.log("Error getting datatable results");
			});


			$.get("{{ route('user.projects.change.stats') }}", { project: value}, 
				function(data){
					document.getElementById('total-results').innerHTML = data['results']['total'];
					document.getElementById('total-chars').innerHTML = data['chars']['total'];
			});

		}
	</script>
@endsection