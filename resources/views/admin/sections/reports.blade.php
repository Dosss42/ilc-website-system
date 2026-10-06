    <div id="section-reports" class="dash-section" style="display:none;">
        {{-- Unlike every other converted section, reports-content.blade.php
             was never written to handle its own data being undefined — it's
             1,500+ lines of raw $rpt*/$kpi* references with no isset()
             guards anywhere (the old architecture always computed and
             passed real data on every load, so none were ever needed).
             Rather than hand-guard every one of those references, this one
             top-level guard keeps the whole partial from rendering at all
             until sectionReports() actually supplies the data — exactly
             what every other section's own scattered isset() checks already
             achieve, just in one place instead of fifty. --}}
        @isset($rptTotalStudents)
            @include('admin.sections.reports-content')
        @endisset
    </div>{{-- /section-reports --}}
