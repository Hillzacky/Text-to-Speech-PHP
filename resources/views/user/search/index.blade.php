@extends('layouts.app')
@section('css')
	<!-- Data Table CSS -->
	<link href="{{URL::asset('plugins/datatable/datatables.min.css')}}" rel="stylesheet" />
@endsection
@section('page-header')
<!-- PAGE HEADER -->
<div class="page-header mt-5-7">
	<div class="page-leftheader">
		<h4 class="page-title mb-0">{{ __('Search Results Data') }}</h4>
		<ol class="breadcrumb mb-2">
			<li class="breadcrumb-item"><a href="{{route('user.tts')}}"><i class="fa fa-search mr-2 fs-12"></i>{{ __('User') }}</a></li>
			<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('Search Results') }}</a></li>
		</ol>
	</div>
</div>
<!-- END PAGE HEADER -->
@endsection
@section('content')	
	<div class="row">
		<div class="col-lg-12 col-md-12 col-xm-12">
			<div class="card border-0">
				<div class="card-header">
					<h3 class="card-title">{{ __('Seach Keyword: ') }} <span class="text-info">{{ $searchValue }}</span></h3>
				</div>
				<div class="card-body pt-4">
					<!-- SET DATATABLE -->
					<table id='resultsTable' class='table' width='100%'>
							<thead>
								<tr>
									<th width="3%"></th>
									<th width="10%">{{ __('Created On') }}</th> 
									<th width="5%">{{ __('Vendor') }}</th> 
									<th width="10%">{{ __('Language') }}</th>
									<th width="7%">{{ __('Voice') }}</th>
									<th width="7%">{{ __('Gender') }}</th>
									<th width="10%">{{ __('Voice Engine') }}</th>
									<th width="5%">{{ __('Characters') }}</th>																	           	
									<th width="20%">{{ __('Text Title') }}</th>     						           	
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

		var data = <?php echo $data; ?>;
		// var free = Object.values(data);
		console.log(data.original.data);

		// INITILIZE DATATABLE
		var table = $('#resultsTable').DataTable({
			"lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
			responsive: {
				details: {type: 'column'}
			},
			colReorder: true,
			language: {
				"emptyTable": "<div><img id='no-results-img' src='{{ URL::asset('img/files/no-result.png') }}'><br>No search results were found</div>",
			},
			"bFilter": false,
			"paging": false,
			pagingType : 'full_numbers',
			processing: false,
			serverSide: false,
			data: data.original.data,
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
					data: 'vendor',
					name: 'vendor',
					orderable: true,
					searchable: true
				},				
				{
					data: 'language',
					name: 'language',
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
					data: 'characters',
					name: 'characters',
					orderable: true,
					searchable: true
				},	
				{
					data: 'title',
					name: 'title',
					orderable: true,
					searchable: true
				}
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

	});
</script>
@endsection