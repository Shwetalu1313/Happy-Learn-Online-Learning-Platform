<form action="{{ route('language.switch') }}" method="post" class="inline-block" >
    @csrf
    <select name="language" onchange="this.form.submit()" class="p-2 rounded bg-primay-100 text-gray-800" id="">
enEnglish
        <option value="en" {{app()->getLocale() === 'en' ? 'selected' : ' '}}>English</option>
        <option value="mm" {{app()->getLocale() === 'mm' ? 'selected' : ' '}}>Myanmar</option>
    </select>
</form>
