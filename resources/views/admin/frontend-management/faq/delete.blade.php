<form action="{{ route('admin.settings.faq.destroy', $id->id) }}" method="POST" enctype="multipart/form-data">
    @method('DELETE')
    @csrf
        
    <div class="modal-body">        
		<p>{{ __('Do you want to delete selected FAQ answer') }}?</p>     
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-cancel mr-2" data-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="submit" class="btn btn-confirm">{{ __('Confirm') }}</button>
    </div>
</form>