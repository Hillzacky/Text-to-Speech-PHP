<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<!-- Meta data -->
		<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="">
	    <meta name="keywords" content="">
	    <meta name="description" content="">
		
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Title -->
        <title>{{ config('app.name') }}</title>

        <!--CSS Files -->
        <link href="{{URL::asset('plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
	    <link href="{{URL::asset('plugins/awselect/awselect.min.css')}}" rel="stylesheet" />
		<!-- Style css -->
		<link href="{{URL::asset('plugins/tippy/scale-extreme.css')}}" rel="stylesheet" />
		<link href="{{URL::asset('plugins/tippy/material.css')}}" rel="stylesheet" />

		@include('layouts.header')

	</head>

	<body class="app sidebar-mini">

        <!-- LOADER -->
		<div id="global-loader" >
			<img src="{{URL::asset('img/svgs/loader.svg')}}" alt="loader">           
		</div>
		<!-- END LOADER -->

		<!-- Page -->
		<div class="page">
			<div class="page-main">
				
				<!-- App-Content -->			
				<div class="main-content">
					<div class="side-app">

						@yield('content')

					</div>                   
				</div>
		
		</div><!-- End Page -->

		@include('layouts.footer-scripts-guest')
        
	</body>
</html>


