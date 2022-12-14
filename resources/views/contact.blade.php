@extends('layouts.guest')

@section('css')
	<!-- Data Table CSS -->
	<link href="{{URL::asset('plugins/awselect/awselect.min.css')}}" rel="stylesheet" />
@endsection

@section('content')

    <div class="container">
        <div class="row text-center">
            <div class="col-md-12">
                <div class="section-title">
                    <!-- SECTION TITLE -->
                    <div class="text-center mb-9 mt-9 pt-5" id="contact-row">

                        <div class="title">
                            <h6><span>{{ __('Contact') }}</span> {{ __('Us') }}</h6>
                            <p>{{ __('Got questions? We are here to help at any time.') }}</p>
                        </div>

                    </div> <!-- END SECTION TITLE -->
                </div>
            </div>
        </div>
    </div>

    <section id="contact-wrapper">

        <div class="container pt-8">          
            
            <div class="row">                

                <div class="col-md-12">
                    <form id="" action="{{ route('contact') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row justify-content-md-center">
                            <div class="col-md-6 col-sm-12">
                                <div class="input-box mb-4">                             
                                    <label for="name" class="fs-12 font-weight-bold text-md-right">{{ __('Full Name') }}</label>
                                    <input id="name" type="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" autocomplete="off" placeholder="First and Last Names" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror                            
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-md-center">
                            <div class="col-md-6 col-sm-12">
                                <div class="input-box mb-4">                             
                                    <label for="email" class="fs-12 font-weight-bold text-md-right">{{ __('Email Address') }}</label>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="off"  placeholder="Email Address" required>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror                            
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-md-center">
                            <div class="col-md-6 col-sm-12">
                                <div class="input-box">	
                                    <h6 class="fs-12 font-weight-bold">{{ __('Question Category') }}</h6>
                                    <select id="support-category" name="category" data-placeholder="{{ __('Select Question Category') }}:" required>			
                                        <option value="General Quesion" selected>{{ __('General Question') }}</option>
                                        <option value="Billing Quesion">{{ __('Billing Question') }}</option>
                                        <option value="Technical Question">{{ __('Technical Question') }}</option>
                                        <option value="Bug Report">{{ __('Bug Report') }}</option>
                                        <option value="Improvement Suggestion">{{ __('Improvement Suggestion') }}</option>
                                        <option value="General Feedback">{{ __('General Feedback') }}</option>
                                    </select>
                                    @error('category')
                                        <p class="text-danger">{{ $errors->first('category') }}</p>
                                    @enderror	
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-md-center">
                            <div class="col-md-6 col-sm-12">
                                <div class="input-box">	
                                    <h6 class="fs-12 font-weight-bold">{{ __('Email Message') }}</h6>							
                                    <textarea class="form-control @error('message') is-invalid @enderror" name="message" rows="10" required></textarea>
                                    @error('message')
                                        <p class="text-danger">{{ $errors->first('message') }}</p>
                                    @enderror	
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="recaptcha" id="recaptcha">
                        
                        <div class="row justify-content-md-center">
                            <!-- ACTION BUTTON -->
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary special-action-button">{{ __('Send Message') }}</button>							
                            </div>
                        </div>
                    
                    </form>

                </div>                   
                
            </div>
        
        </div>

    </section>
@endsection

@section('js')
	<!-- Awselect JS -->
	<script src="{{URL::asset('plugins/awselect/awselect.min.js')}}"></script>
	<script src="{{URL::asset('js/awselect.js')}}"></script>
	<script src="{{URL::asset('js/minimize.js')}}"></script>

    @if (config('services.google.recaptcha.enable') == 'on')
         <!-- Google reCaptcha JS -->
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.google.recaptcha.site_key') }}"></script>
        <script>
            grecaptcha.ready(function() {
                grecaptcha.execute('{{ config('services.google.recaptcha.site_key') }}', {action: 'contact'}).then(function(token) {
                    if (token) {
                    document.getElementById('recaptcha').value = token;
                    }
                });
            });
        </script>
    @endif
   
@endsection
