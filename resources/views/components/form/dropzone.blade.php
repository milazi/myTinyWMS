<form action="{{ $url }}" class="dropzone" id="dropzoneForm" enctype="multipart/form-data">
    <div class="dz-message" data-dz-message><span>@lang('Drop files here')</span></div>
    <div class="fallback">
        <input name="file" type="file" multiple />
    </div>
</form>