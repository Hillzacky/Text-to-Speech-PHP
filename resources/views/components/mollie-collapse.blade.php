<div>
    <div class="input-box">								
        <h6>Full Name <span class="text-muted">(Required)</span></h6>
        <div class="form-group">							    
            <input type="text" class="form-control @error('mollie_name') is-danger @enderror" id="mollie_name" name="mollie_name" value="{{ auth()->user()->name }}" autocomplete="off" required>
        </div>
            @error('mollie_name')
            <p class="text-danger">{{ $errors->first('mollie_name') }}</p>
        @enderror
    </div> 
    <div class="input-box">								
        <h6>Email Address <span class="text-muted">(Required)</span></h6>
        <div class="form-group">							    
            <input type="text" class="form-control @error('mollie_email') is-danger @enderror" id="mollie_email" name="mollie_email" value="{{ auth()->user()->email }}" autocomplete="off" required>
        </div>
            @error('mollie_email')
            <p class="text-danger">{{ $errors->first('mollie_email') }}</p>
        @enderror
    </div>    
</div>