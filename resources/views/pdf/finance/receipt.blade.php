<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $orNumber }}</title>
<style>
    {{-- Same slip as every printed Official Receipt (public/js/official-receipt.js,
         the Cashier's layout): logo, school header, OR No / Date / Time, student,
         description, AMOUNT PAID, Received by + signature. 'DejaVu Sans Mono' is the
         PDF stand-in for Courier New (and, unlike Courier/Helvetica, has the ₱ glyph). --}}
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans Mono', monospace; font-size: 9px; color: #000; padding: 16px 18px; }
    .center { text-align: center; } .bold { font-weight: bold; } .right { text-align: right; }
    .logo img { width: 46px; height: 46px; margin-bottom: 5px; }
    h2 { font-size: 11px; letter-spacing: .3px; }
    h3 { font-size: 8px; font-weight: normal; margin-top: 2px; }
    .solid { border-top: 1.5px solid #000; margin: 7px 0; }
    .dash { border-top: 1px dashed #000; margin: 6px 0; }
    table { width: 100%; border-collapse: collapse; }
    td { padding: 2px 0; vertical-align: top; }
    td.v { text-align: right; padding-left: 6px; }
    .amt { font-size: 11.5px; font-weight: bold; }
    .peso { font-family: 'DejaVu Sans', sans-serif; }
    .paid { margin-top: 14px; border: 1px solid #000; padding: 4px; text-align: center; font-size: 8px; font-weight: bold; letter-spacing: .5px; }
    .sig { margin-top: 26px; border-top: 1px solid #000; padding-top: 3px; text-align: center; font-size: 8.5px; }
    .footer { margin-top: 16px; font-size: 7.5px; text-align: center; color: #444; }
</style>
</head>
<body>
    @php
        $online = $transaction->payment_type === 'online';
        $gradeRaw = $transaction->enrollment->grade_level ?? '';
        $grade = preg_match('/^grade\s*(\d+)$/i', $gradeRaw, $gm) ? 'Grade ' . $gm[1] : ($gradeRaw ? ucfirst($gradeRaw) : '—');
        $methodLabel = ['cash' => 'Cash', 'gcash' => 'GCash', 'maya' => 'Maya', 'grabpay' => 'GrabPay', 'bank' => 'Bank Transfer', 'otc' => 'Over-the-Counter'][$transaction->payment_method]
            ?? ucfirst((string) $transaction->payment_method);
        $when = $transaction->processed_at ?? $transaction->created_at;
    @endphp

    <div class="center">
        @if($schoolLogoBase64)<div class="logo"><img src="{{ $schoolLogoBase64 }}" alt=""></div>@endif
        <h2>IEMELIF LEARNING CENTER</h2>
        <h3>General Tinio, Nueva Ecija</h3>
        <h3>Tel: 0951-989-9685</h3>
    </div>
    <div class="solid"></div>
    <div class="center bold" style="font-size:10px;letter-spacing:1px;">OFFICIAL RECEIPT</div>
    <div class="solid"></div>

    <table>
        <tr><td>OR No.:</td><td class="v bold">{{ $orNumber }}</td></tr>
        <tr><td>Date:</td><td class="v">{{ $when->format('F d, Y') }}</td></tr>
        <tr><td>Time:</td><td class="v">{{ $when->format('h:i A') }}</td></tr>
    </table>
    <div class="dash"></div>
    <table>
        <tr><td>Student:</td><td class="v bold">{{ $transaction->user->name ?? '—' }}</td></tr>
        <tr><td>Grade Level:</td><td class="v">{{ $grade }}</td></tr>
        <tr><td>School Year:</td><td class="v">{{ $transaction->enrollment->school_year ?? '—' }}</td></tr>
    </table>
    <div class="dash"></div>
    <table>
        <tr><td>Description:</td><td class="v">{{ $description }}</td></tr>
        <tr><td>Method:</td><td class="v">{{ $methodLabel }}{{ $online ? ' (Online)' : '' }}</td></tr>
        @if($online && $transaction->xendit_invoice_id)
        <tr><td>Transaction ID:</td><td class="v" style="font-size:7.5px;">{{ $transaction->xendit_invoice_id }}</td></tr>
        @endif
    </table>
    <div class="solid"></div>
    <table>
        <tr><td class="amt">AMOUNT PAID:</td><td class="v amt"><span class="peso">₱</span>{{ number_format($transaction->amount, 2) }}</td></tr>
    </table>
    <div class="solid"></div>

    @if($online)
        <div class="paid">PAID ONLINE · VERIFIED BY PAYMENT GATEWAY</div>
    @else
        <div style="margin-top:10px;font-size:8.5px;">Received by: <span class="bold">{{ $receivedBy }}</span></div>
        <div class="sig">Cashier Signature</div>
    @endif

    <div class="footer">
        <div>Thank you for your payment!</div>
        <div style="margin-top:2px;">This is a computer-generated receipt.{{ $online ? ' No signature required.' : '' }}</div>
    </div>
</body>
</html>
