<form action="{{ route('language.switch') }}" method="post" class="m-0">
    @csrf
    <select name="language" onchange="this.form.submit()" class="form-select form-select-sm bg-dark text-light border-secondary">
        <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
        <option value="mm" {{ app()->getLocale() === 'mm' ? 'selected' : '' }}>Myanmar</option>
    </select>
</form>
