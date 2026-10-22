{{-- "You have been logged out" notice shown in front of a login form after an automatic logout
     (SessionTimeoutController::expired flashes `session_notice`). Closes on OK, on a click
     outside it, or Esc. Uses the page's own font. --}}
@php $notice = session('session_notice'); @endphp
@if(is_array($notice))
<div id="ilc-session-notice" role="alertdialog" aria-modal="true" aria-labelledby="ilc-session-notice-title"
     style="position:fixed;inset:0;z-index:2147483000;background:rgba(15,23,42,.55);display:flex;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;max-width:380px;width:100%;padding:28px 24px 22px;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.35);font-family:inherit;">
        <div style="width:56px;height:56px;border-radius:50%;background:#fff8ec;color:#b45309;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:26px;">&#128339;</div>
        <div id="ilc-session-notice-title" style="font-size:17px;font-weight:700;color:#1a3a6c;margin-bottom:8px;">You have been logged out</div>
        <div style="font-size:13px;color:#64748b;line-height:1.6;margin-bottom:20px;">
            @if(($notice['reason'] ?? 'idle') === 'expired')
                Your session has expired, so you were logged out for your security. Please log in again.
            @else
                For your security, you were logged out automatically after
                <strong style="color:#334155;">{{ \App\Support\SecurityPolicy::sessionMinutesLabel((int) ($notice['minutes'] ?? 15)) }}</strong> of inactivity. Please log in again.
            @endif
        </div>
        <button type="button" id="ilc-session-notice-ok"
                style="width:100%;border:none;border-radius:10px;padding:11px;font-size:14px;font-weight:700;color:#fff;background:linear-gradient(135deg,#1a3a6c,#1e5799);cursor:pointer;font-family:inherit;">
            OK
        </button>
    </div>
</div>
<script>
    (function () {
        var box = document.getElementById('ilc-session-notice');
        function close() {
            if (!box) return;
            box.remove(); box = null;
            var email = document.querySelector('input[type=email], input[name=email]');
            if (email) { try { email.focus(); } catch (e) {} }
        }
        document.getElementById('ilc-session-notice-ok').addEventListener('click', close);
        box.addEventListener('click', function (e) { if (e.target === box) close(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' || e.key === 'Enter') close(); });
        try { document.getElementById('ilc-session-notice-ok').focus(); } catch (e) {}
    })();
</script>
@endif
