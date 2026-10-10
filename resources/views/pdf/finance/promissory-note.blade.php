<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportTitle }} — {{ $note->reference_number }}</title>
@include('pdf.shared._styles')
<style>
    .pn-status-row { display: table; width: 100%; margin-bottom: 16px; }
    .pn-status-row .pn-issued { display: table-cell; font-size: 10.5px; color: #666; }
    .pn-status-row .pn-issued b { color: #1a1a1a; }
    .pn-status-row .pn-status { display: table-cell; text-align: right; }
    .pn-badge { display: inline-block; padding: 3px 12px; border-radius: 10px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
    .pn-badge.pending   { background: #e3f2fd; color: #1565c0; }
    .pn-badge.fulfilled { background: #e8f5e9; color: #2e7d32; }
    .pn-badge.broken    { background: #ffebee; color: #c62828; }
    .pn-badge.extended  { background: #fff3e0; color: #e65100; }

    .pn-info-grid { display: table; width: 100%; border-spacing: 20px 0; margin-bottom: 4px; }
    .pn-info-row { display: table-row; }
    .pn-info-cell { display: table-cell; width: 50%; padding-bottom: 10px; border-bottom: 1px solid #ddd; }
    .pn-info-label { font-size: 8.5px; font-weight: bold; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
    .pn-info-value { font-size: 11px; font-weight: bold; color: #1a3a6c; margin-top: 2px; }
    .pn-info-sub { font-size: 8px; color: #888; margin-top: 1px; }

    .pn-amount-box { background: #f0f4ff; border: 2px solid #1a3a6c; border-radius: 6px; padding: 14px 20px; margin: 16px 0 10px; text-align: center; }
    .pn-amount-label { font-size: 9px; color: #666; text-transform: uppercase; letter-spacing: 1px; }
    .pn-amount-value { font-size: 22px; font-weight: bold; color: #1a3a6c; margin-top: 3px; }
    .pn-amount-sub { font-size: 9px; color: #888; margin-top: 3px; }

    .pn-promise-box { border: 2px dashed #f5a623; border-radius: 6px; padding: 10px 16px; margin: 10px 0; background: #fffbf0; }
    .pn-promise-label { font-size: 9px; font-weight: bold; color: #c8860a; text-transform: uppercase; }
    .pn-promise-date { font-size: 15px; font-weight: bold; color: #1a3a6c; margin-top: 3px; }
    .pn-promise-note { font-size: 9px; color: #888; margin-top: 3px; }
    .pn-promise-note.fulfilled { color: #2e7d32; font-weight: bold; }

    .pn-remarks-box { border: 1px solid #ddd; border-radius: 4px; padding: 10px 14px; margin: 10px 0; }
    .pn-remarks-label { font-size: 8.5px; font-weight: bold; color: #888; text-transform: uppercase; margin-bottom: 4px; }
    .pn-remarks-value { font-size: 10px; color: #333; }

    .pn-notice { background: #fff8e1; border-left: 3px solid #f5a623; padding: 8px 12px; margin: 14px 0 20px; font-size: 9px; color: #555; }

    .pn-sig-block { display: table; width: 100%; border-spacing: 30px 0; margin-top: 24px; }
    .pn-sig-cell { display: table-cell; width: 50%; text-align: center; }
    .pn-sig-line { border-top: 1px solid #333; padding-top: 4px; font-size: 10.5px; font-weight: bold; color: #1a3a6c; min-height: 14px; }
    .pn-sig-label { font-size: 8.5px; color: #666; text-transform: uppercase; margin-top: 2px; }
    .pn-sig-email { font-size: 8px; color: #999; margin-top: 1px; }
</style>
</head>
<body>
    @include('pdf.shared._letterhead')

    <div class="pn-status-row">
        <div class="pn-issued">Date Issued: <b>{{ $note->date_issued->format('F d, Y') }}</b></div>
        <div class="pn-status"><span class="pn-badge {{ $note->status }}">{{ ucfirst($note->status) }}</span></div>
    </div>

    <div class="pn-info-grid">
        <div class="pn-info-row">
            <div class="pn-info-cell">
                <div class="pn-info-label">Student Name</div>
                <div class="pn-info-value">{{ $note->student->name ?? '—' }}</div>
            </div>
            <div class="pn-info-cell">
                <div class="pn-info-label">Parent / Guardian</div>
                <div class="pn-info-value">{{ $note->parent_guardian ?: '—' }}</div>
            </div>
        </div>
        <div class="pn-info-row">
            <div class="pn-info-cell">
                <div class="pn-info-label">Grade Level</div>
                <div class="pn-info-value">{{ ucfirst(str_replace(['grade','nursery','kindergarten'], ['Grade ','Nursery','Kindergarten'], $note->enrollment->student_data['grade_level'] ?? '—')) }}</div>
            </div>
            <div class="pn-info-cell">
                <div class="pn-info-label">School Year</div>
                <div class="pn-info-value">S.Y. {{ $note->enrollment->school_year ?? '—' }}</div>
            </div>
        </div>
        <div class="pn-info-row">
            <div class="pn-info-cell">
                <div class="pn-info-label">Enrollment Reference</div>
                <div class="pn-info-value">{{ $note->enrollment->reference_number ?? '—' }}</div>
            </div>
            <div class="pn-info-cell">
                <div class="pn-info-label">Received By</div>
                <div class="pn-info-value">{{ $note->createdBy->name ?? '—' }}</div>
            </div>
        </div>
    </div>

    <div class="pn-amount-box">
        <div class="pn-amount-label">Amount Promised to Pay</div>
        <div class="pn-amount-value">₱{{ number_format($note->amount_promised, 2) }}</div>
        @if($note->amount_overdue > 0)
            <div class="pn-amount-sub">Outstanding balance at time of note: ₱{{ number_format($note->amount_overdue, 2) }}</div>
        @endif
    </div>

    <div class="pn-promise-box">
        <div class="pn-promise-label">Promised Payment Date</div>
        <div class="pn-promise-date">{{ $note->promise_date->format('F d, Y') }}</div>
        @if($note->extended_date)
            <div class="pn-promise-note">Extended to: {{ $note->extended_date->format('F d, Y') }}</div>
        @endif
        @if($note->fulfilled_at)
            <div class="pn-promise-note fulfilled">Fulfilled on {{ $note->fulfilled_at->format('F d, Y') }}</div>
        @endif
    </div>

    @if($note->remarks)
        <div class="pn-remarks-box">
            <div class="pn-remarks-label">Remarks</div>
            <div class="pn-remarks-value">{{ $note->remarks }}</div>
        </div>
    @endif

    <div class="pn-notice">
        This promissory note serves as a written commitment by the parent/guardian to settle the outstanding school fees on the specified date. Failure to comply may result in suspension of privileges and access to school records.
    </div>

    {{-- Custom signature block, not pdf.shared._bottom — the signers here
         are the actual parties to this specific note (the parent/guardian
         who made the promise, and the staff member who received it), not
         the school's standing registrar/officer-in-charge pair every other
         report closes with. --}}
    <div class="pn-sig-block">
        <div class="pn-sig-cell">
            <div class="pn-sig-line">{{ $note->parent_guardian ?: '—' }}</div>
            <div class="pn-sig-label">Parent / Guardian Signature</div>
        </div>
        <div class="pn-sig-cell">
            <div class="pn-sig-line">{{ $note->createdBy->name ?? '—' }}</div>
            <div class="pn-sig-label">Finance Officer / Received By</div>
            @if($note->createdBy?->email)
                <div class="pn-sig-email">{{ $note->createdBy->email }}</div>
            @endif
        </div>
    </div>
</body>
</html>
