{{-- Automatic logout after inactivity (Admin › Settings › Security › Session Timeout).
     Include once on each portal page, before </body>: @include('partials.idle-logout', ['portal' => 'web|finance|cashier']) --}}
@php
    $idlePortal = $portal ?? 'web';
@endphp
@if(auth($idlePortal)->check())
<script src="{{ asset('js/idle-logout.js') }}?v={{ @filemtime(public_path('js/idle-logout.js')) }}"
        data-minutes="{{ \App\Support\SecurityPolicy::sessionMinutes() }}"
        data-ping-url="{{ route('session.ping') }}"
        data-expire-url="{{ route('session.expire') }}"
        data-expired-url="{{ route('session.expired', ['portal' => $idlePortal]) }}"
        data-csrf="{{ csrf_token() }}"></script>
@endif
