@extends('layouts.app')

@section('css')
	<!-- Data Table CSS -->
	<link href="{{URL::asset('plugins/awselect/awselect.min.css')}}" rel="stylesheet" />
@endsection

@section('page-header')
	<!-- PAGE HEADER -->
	<div class="page-header mt-5-7">
		<div class="page-leftheader">
			<h4 class="page-title mb-0"> {{ __('Google AdSense Settings') }}</h4>
			<ol class="breadcrumb mb-2">
				<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-globe mr-2 fs-12"></i>{{ __('Admin') }}</a></li>
				<li class="breadcrumb-item" aria-current="page"><a href="{{url('#')}}"> {{ __('General Settings') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{url('#')}}"> {{ __('AdSense Settings') }}</a></li>
			</ol>
		</div>
	</div>
	<!-- END PAGE HEADER -->
@endsection
@section('content')					
	<div class="row">
		<div class="col-lg-6 col-md-12 col-xm-12">
			<div class="card overflow-hidden border-0">
				<div class="card-header">
					<h3 class="card-title">{{ __('Setup Google AdSense Settings') }}</h3>
				</div>
				<div class="card-body">
				
					<form action="{{ route('admin.settings.adsense.store') }}" method="POST" enctype="multipart/form-data">
						@csrf

						<div class="row">
							<div class="col-md-4 col-sm-12">									
								<h6 class="fs-12 font-weight-bold mb-4">Google AdSense</h6>								
								<div class="form-group">
									<label class="custom-switch">
										<input type="checkbox" name="enable-adsense" class="custom-switch-input" @if ( config('adsense.status')  == 'on') checked @endif>
										<span class="custom-switch-indicator"></span>
										<span class="custom-switch-description">{{ __('Enable') }}</span>
									</label>
								</div> 
							</div>
						</div>
						
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12">													
								<div class="input-box">								
									<h6>{{ __('Google Adsense Client ID') }}</h6>
									<div class="form-group">							    
										<input type="text" class="form-control @error('client-id') is-danger @enderror" id="client-id" name="client-id" value="{{ config('adsense.client_id') }}" autocomplete="off">
										@error('client-id')
											<p class="text-danger">{{ $errors->first('client-id') }}</p>
										@enderror
									</div> 
								</div> 
							</div>								
						</div>
						
						<!-- SAVE CHANGES ACTION BUTTON -->
						<div class="border-0 text-right mb-2 mt-1">
							<a href="{{ route('admin.dashboard') }}" class="btn btn-cancel mr-2">{{ __('Cancel') }}</a>
							<button type="submit" class="btn btn-primary">{{ __('Save') }}</button>							
						</div>				

					</form>

				</div>
			</div>
		</div>
	</div>	
@endsection

@section('js')
	<!-- Awselect JS -->
	<script src="{{URL::asset('plugins/awselect/awselect.min.js')}}"></script>
	<script src="{{URL::asset('js/awselect.js')}}"></script>
	<!-- File Uploader -->
	<script src="{{URL::asset('js/file-upload.js')}}"></script>
@endsection