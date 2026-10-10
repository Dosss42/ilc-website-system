<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $orNumber }}</title>
<style>
    {{-- Standalone receipt slip, deliberately not using pdf.shared._styles
         (that shared stylesheet now relies on @page margin + the running
         header/footer decorator, which this small one-page slip doesn't
         need). Margin via plain body{padding}. 'DejaVu Sans' not Arial/
         Helvetica — those have no ₱ glyph and render it as "?". --}}
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a1a1a; padding: 20px 18px; }

    .rc-logo { text-align: center; margin-bottom: 4px; }
    .rc-logo img { width: 42px; height: 42px; }
    .rc-school { text-align: center; font-size: 12px; font-weight: bold; color: #1a3a6c; text-transform: uppercase; }
    .rc-addr { text-align: center; font-size: 8.5px; color: #555; margin-bottom: 8px; }

    .rc-title { text-align: center; font-size: 12px; font-weight: bold; text-transform: uppercase; border-top: 1px dashed #999; border-bottom: 1px dashed #999; padding: 5px 0; margin-bottom: 8px; }
    .rc-or { text-align: center; font-size: 10px; font-weight: bold; color: #1a3a6c; margin-bottom: 10px; }

    table.rc-fields { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    table.rc-fields td { padding: 3px 0; font-size: 9.5px; vertical-align: top; }
    table.rc-fields td.rc-label { color: #666; width: 38%; }
    table.rc-fields td.rc-value { font-weight: bold; text-align: right; }

    .rc-amount-box { border: 1.5px solid #1a3a6c; border-radius: 4px; padding: 8px; text-align: center; margin: 10px 0; }
    .rc-amount-label { font-size: 8.5px; color: #666; text-transform: uppercase; }
    .rc-amount-value { font-size: 18px; font-weight: bold; color: #1a3a6c; margin-top: 2px; }

    .rc-sig { margin-top: 28px; text-align: center; }
    .rc-sig-line { border-top: 1px solid #333; width: 70%; margin: 0 auto; padding-top: 3px; font-size: 9px; }
    .rc-sig-label { font-size: 8px; color: #666; text-transform: uppercase; }

    .rc-footer { text-align: center; font-size: 7.5px; color: #999; margin-top: 14px; }
</style>
</head>
<body>
    <div class="rc-logo">
        @if($schoolLogoBase64)<img src="{{ $schoolLogoBase64 }}" alt="">@endif
    </div>
    <div class="rc-school">{{ $schoolName }}</div>
    <div class="rc-addr">{{ $schoolAddress }}</div>

    <div class="rc-title">Official Receipt</div>
    <div class="rc-or">{{ $orNumber }}</div>

    <table class="rc-fields">
        <tr>
            <td class="rc-label">Date</td>
            <td class="rc-value">{{ $transaction->created_at->format('M d, Y g:i A') }}</td>
        </tr>
        <tr>
            <td class="rc-label">Received From</td>
            <td class="rc-value">{{ $transaction->user->name ?? 'N/A' }}</td>
        </tr>
        @if($transaction->enrollment)
        <tr>
            <td class="rc-label">Grade / S.Y.</td>
            <td class="rc-value">{{ $transaction->enrollment->grade_level ?? '—' }} / {{ $transaction->enrollment->school_year ?? '—' }}</td>
        </tr>
        @endif
        <tr>
            <td class="rc-label">For</td>
            <td class="rc-value">{{ $description }}</td>
        </tr>
        <tr>
            <td class="rc-label">Payment Method</td>
            <td class="rc-value">{{ strtoupper($transaction->payment_method) }}</td>
        </tr>
        @if($transaction->reference_number)
        <tr>
            <td class="rc-label">Reference No.</td>
            <td class="rc-value">{{ $transaction->reference_number }}</td>
        </tr>
        @endif
    </table>

    <div class="rc-amount-box">
        <div class="rc-amount-label">Amount Received</div>
        <div class="rc-amount-value">₱{{ number_format($transaction->amount, 2) }}</div>
    </div>

    <div class="rc-sig">
        <div class="rc-sig-line">{{ $receivedBy }}</div>
        <div class="rc-sig-label">Received By</div>
    </div>

    <div class="rc-footer">This is a system-generated receipt. Printed {{ $generatedAt->format('M d, Y g:i A') }}</div>
</body>
</html>
