<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
<span>
    {{ config('app.name') }}
</span>
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
<p style="margin-bottom: 8px;">© {{ date('Y') }} {{ config('app.name') }}</p>
<p style="color: #94a3b8; font-size: 12px;">{{ __('All rights reserved.') }}</p>
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>