@if (session('status'))
    <div class="flash flash-success">{{ explode('|', session('status'))[0] }}</div>
@endif

@if (session('error'))
    <div class="flash flash-error">{{ explode('|', session('error'))[0] }}</div>
@endif

@if ($errors->any())
    <div class="flash flash-error">
        <b data-id="Periksa kembali isian Anda:" data-en="Please review your input:">Periksa kembali isian Anda:</b>
        <ul style="margin:6px 0 0;padding-left:18px">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
