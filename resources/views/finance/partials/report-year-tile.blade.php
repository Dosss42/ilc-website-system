<div class="rpt-tile">
    <div class="rpt-tile-head">
        <div class="rpt-ico {{ $r['tint'] }}"><i class="bi {{ $r['icon'] }}"></i></div>
        <div>
            <h4>{{ $r['title'] }}</h4>
            <p>{{ $r['desc'] }}</p>
        </div>
    </div>
    <form method="GET" action="{{ route($r['route']) }}" class="rpt-form">
        <div class="rpt-field">
            <label>School year</label>
            <select name="school_year" class="rpt-input">
                @foreach($schoolYears as $year)
                    <option value="{{ $year }}" {{ $year === $currentSchoolYear ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rpt-btn"><i class="bi bi-download"></i> Download PDF</button>
    </form>
</div>
