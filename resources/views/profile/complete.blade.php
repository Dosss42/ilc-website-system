<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Profile — IEMELIF Learning Center</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    <link rel="stylesheet" href="/css/admission.css">
    <style>
        :root {
            --primary-color: #1a3a6c;
            --secondary-color: #f4b942;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --light-bg: #f8f9fa;
            --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
            --hover-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }

        .auth-wrapperr {
            padding: 30px 20px;
            width:750px;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--card-shadow);
            border-radius: 20px;
            padding: 30px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .progress-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2c5aa0 100%);
            color: white;
            padding: 20px 24px;
            border-radius: 14px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .progress-title {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .progress-percentage {
            font-size: 20px;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 14px;
            border-radius: 20px;
        }

        .progress {
            height: 8px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--secondary-color), #ffc107);
            height: 100%;
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        .section-list {
            margin-bottom: 24px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e9ecef;
        }

        .section-row {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            background: white;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s ease;
            gap: 14px;
        }

        .section-row:last-child {
            border-bottom: none;
        }

        .section-row:hover {
            background: #f8f9ff;
        }

        .section-row .row-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: white;
            flex-shrink: 0;
        }

        .section-row .row-icon.complete {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .section-row .row-icon.incomplete {
            background: linear-gradient(135deg, #adb5bd, #ced4da);
        }

        .section-row .row-info {
            flex: 1;
            min-width: 0;
        }

        .section-row .row-info h6 {
            margin: 0;
            font-weight: 600;
            color: var(--primary-color);
            font-size: 14px;
        }

        .section-row .row-info small {
            color: #6c757d;
            font-size: 12px;
        }

        .status-badge {
            font-size: 10px;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .status-complete {
            background: #d4edda;
            color: #155724;
        }

        .status-incomplete {
            background: #fff3cd;
            color: #856404;
        }

        .btn-complete {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 5px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
            cursor: pointer;
        }

        .btn-complete:hover {
            background: #2c5aa0;
        }

        .form-section {
            display: none;
            background: var(--light-bg);
            border-radius: 12px;
            padding: 20px;
            margin-top: 16px;
            border: 1px solid #e0e0e0;
            animation: slideDown 0.3s ease;
        }

        .form-section.active {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-section h5 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--secondary-color);
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(26, 58, 108, 0.25);
            background: white;
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #2c5aa0);
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(26, 58, 108, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26, 58, 108, 0.4);
            background: linear-gradient(135deg, #2c5aa0, var(--primary-color));
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .navigation-footer {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
        }

        .btn-outline-secondary {
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border: none;
            color: #155724;
            border-radius: 12px;
            padding: 16px 20px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .auth-card {
                padding: 20px;
                margin: 10px;
            }
            
            .section-row {
                padding: 12px 14px;
            }
            
            .progress-header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<div class="bg-image"></div>

<div class="auth-wrapperr">
    <div class="auth-card">

        <div class="text-center mb-3">
            <h4 style="color: var(--primary-color); font-weight: 700; margin-bottom: 6px;">
                <i class="bi bi-person-circle me-2"></i>Complete Your Profile
            </h4>
            <p class="text-muted mb-0" style="font-size: 13px;">Complete your information to proceed with enrollment</p>
        </div>

        <!-- Progress Overview -->
        <div class="progress-section">
            <div class="progress-header">
                <div class="progress-title">
                    <i class="bi bi-clipboard-check"></i>
                    Profile Completion
                </div>
                <div class="progress-percentage" id="overallPercentage">
                @php
                    $d = $studentData ?? [];
                    $p = $profile ?? null;
                    $paymentStatus = $enrollment->payment_status ?? 'pending';
                    $completion = [
                        'personal' => (!empty($d['first_name']) || ($p && !empty($p->first_name))) && (!empty($d['last_name']) || ($p && !empty($p->last_name))) && (!empty($d['birthdate']) || ($p && !empty($p->birthdate))) && (!empty($d['gender']) || ($p && !empty($p->gender))),
                        'health' => !empty($d['blood_type']) || !empty($d['allergies']) || !empty($d['medical_conditions']) || ($p && (!empty($p->blood_type) || !empty($p->allergies) || !empty($p->medical_conditions))),
                        'address' => (!empty($d['province']) || ($address && !empty($address->province))) && (!empty($d['city']) || ($address && !empty($address->city))) && (!empty($d['barangay']) || ($address && !empty($address->barangay))) && (!empty($d['street_address']) || ($address && !empty($address->street_address))),
                        'guardian' => (!empty($d['guardian_name']) || ($guardian && !empty($guardian->name))) && (!empty($d['relationship']) || ($guardian && !empty($guardian->relationship))) && (!empty($d['guardian_phone']) || ($guardian && !empty($guardian->contact))),
                        'school' => !empty($d['last_school']) || ($p && !empty($p->last_school)),
                        'enrollment' => (!empty($d['grade_level']) || ($enrollment && !empty($enrollment->grade_level))) && (!empty($d['student_type']) || ($p && !empty($p->student_type))),
                        'payment' => $paymentStatus === 'paid',
                    ];
                    $totalComplete = count(array_filter($completion));
                    $totalSections = count($completion);
                    $percentage = ($totalComplete / $totalSections) * 100;
                @endphp
                {{ round($percentage) }}%
            </div>
            </div>
            <div class="progress">
                <div class="progress-bar" id="overallProgress" role="progressbar" style="width: {{ round($percentage) }}%"></div>
            </div>
        </div>

        <!-- Success Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Profile Sections -->
        <div class="section-list">
            @php
                $sections = [
                    ['key' => 'personal', 'icon' => 'bi-person-fill', 'title' => 'Personal Information', 'id' => 'personalStatus'],
                    ['key' => 'health', 'icon' => 'bi-heart-pulse-fill', 'title' => 'Health Information', 'id' => 'healthStatus'],
                    ['key' => 'address', 'icon' => 'bi-geo-alt-fill', 'title' => 'Address', 'id' => 'addressStatus'],
                    ['key' => 'guardian', 'icon' => 'bi-people-fill', 'title' => 'Guardian', 'id' => 'guardianStatus'],
                    ['key' => 'school', 'icon' => 'bi-building', 'title' => 'Previous School', 'id' => 'schoolStatus'],
                    ['key' => 'enrollment', 'icon' => 'bi-mortarboard-fill', 'title' => 'Enrollment', 'id' => 'enrollmentStatus'],
                    ['key' => 'payment', 'icon' => 'bi-credit-card-fill', 'title' => 'Payment', 'id' => 'paymentStatus'],
                ];
            @endphp
            @foreach($sections as $sec)
            <div class="section-row">
                <div class="row-icon {{ $completion[$sec['key']] ? 'complete' : 'incomplete' }}">
                    <i class="bi {{ $completion[$sec['key']] ? 'bi-check-lg' : $sec['icon'] }}"></i>
                </div>
                <div class="row-info">
                    <h6>{{ $sec['title'] }}</h6>
                </div>
                <span id="{{ $sec['id'] }}" class="status-badge {{ $completion[$sec['key']] ? 'status-complete' : 'status-incomplete' }}">
                    {{ $completion[$sec['key']] ? 'Complete' : 'Incomplete' }}
                </span>
                <button class="btn-complete" onclick="toggleSection('{{ $sec['key'] }}')">
                    {{ $sec['key'] === 'payment' ? 'View' : ($completion[$sec['key']] ? 'Edit' : 'Complete') }}
                </button>
            </div>
            @endforeach
        </div>

        <!-- Form Sections (Hidden by default) -->
        
        <!-- Personal Information Form -->
        <div id="personal-form" class="form-section mt-4">
            <h5 class="mb-3">Personal Information</h5>
            <form method="POST" action="{{ route('profile.personal.update') }}">
                @csrf
                <div class="row g-2 mb-2">
                    <div class="col-3">
                        <label class="form-label">First Name *</label>
                        <input type="text" name="first_name" class="form-control" placeholder="Juan"
                               value="{{ $profile->first_name ?? $d['first_name'] ?? '' }}" required oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control" placeholder="Santos"
                               value="{{ $profile->middle_name ?? $d['middle_name'] ?? '' }}" oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Last Name *</label>
                        <input type="text" name="last_name" class="form-control" placeholder="Cruz"
                               value="{{ $profile->last_name ?? $d['last_name'] ?? '' }}" required oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Suffix</label>
                        <select name="suffix" class="form-control">
                            <option value="">None</option>
                            <option value="Jr." {{ ($profile->suffix ?? '') === 'Jr.' ? 'selected' : '' }}>Jr.</option>
                            <option value="Sr." {{ ($profile->suffix ?? '') === 'Sr.' ? 'selected' : '' }}>Sr.</option>
                            <option value="II" {{ ($profile->suffix ?? '') === 'II' ? 'selected' : '' }}>II</option>
                            <option value="III" {{ ($profile->suffix ?? '') === 'III' ? 'selected' : '' }}>III</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-3">
                        <label class="form-label">Date of Birth *</label>
                        <input type="date" name="birthdate" class="form-control" 
                               value="{{ $profile->birthdate ?? $d['birthdate'] ?? '' }}" required max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Gender *</label>
                        <select name="gender" class="form-control" required>
                            <option value="" disabled>Select Gender</option>
                            @php $gen = $profile->gender ?? $d['gender'] ?? ''; @endphp
                            <option value="male" {{ $gen === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ $gen === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Place of Birth *</label>
                        <input type="text" name="place_of_birth" class="form-control" placeholder="General Tinio, Nueva Ecija"
                               value="{{ $profile->place_of_birth ?? $d['place_of_birth'] ?? '' }}" required oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Nationality *</label>
                        <input type="text" name="nationality" class="form-control" placeholder="Filipino"
                               value="{{ $profile->nationality ?? $d['nationality'] ?? 'Filipino' }}" required oninput="capitalizeFirst(this)">
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label">Mother's Full Name *</label>
                        <input type="text" name="mother_name" class="form-control" placeholder="Mother's full name"
                               value="{{ $mother?->name ?? $d['mother_name'] ?? '' }}" required oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Mother's Age *</label>
                        <input type="number" name="mother_age" class="form-control" placeholder="Age"
                               value="{{ $mother?->age ?? $d['mother_age'] ?? '' }}" required min="1" max="120">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Religious Affiliation *</label>
                        <input type="text" name="religious_affiliation" class="form-control" placeholder="e.g., Roman Catholic"
                               value="{{ $profile->religious_affiliation ?? $d['religious_affiliation'] ?? '' }}" required oninput="capitalizeFirst(this)">
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Father's Full Name *</label>
                        <input type="text" name="father_name" class="form-control" placeholder="Father's full name"
                               value="{{ $father?->name ?? $d['father_name'] ?? '' }}" required oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Father's Age *</label>
                        <input type="number" name="father_age" class="form-control" placeholder="Age"
                               value="{{ $father?->age ?? $d['father_age'] ?? '' }}" required min="1" max="120">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact" class="form-control" placeholder="09XX-XXX-XXXX"
                               value="{{ $profile->contact ?? $d['guardian_phone'] ?? '' }}">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" onclick="toggleSection('personal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Personal Info</button>
                </div>
            </form>
        </div>

        <!-- Health Information Form -->
        <div id="health-form" class="form-section mt-4">
            <h5 class="mb-3">Health Information</h5>
            <form method="POST" action="{{ route('profile.health.update') }}">
                @csrf
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label">Blood Type</label>
                        <select name="blood_type" class="form-control">
                            <option value="">Select Blood Type</option>
                            @php $bt = $profile->blood_type ?? $d['blood_type'] ?? ''; @endphp
                            <option value="A+" {{ $bt === 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ $bt === 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ $bt === 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ $bt === 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="O+" {{ $bt === 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ $bt === 'O-' ? 'selected' : '' }}>O-</option>
                            <option value="AB+" {{ $bt === 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ $bt === 'AB-' ? 'selected' : '' }}>AB-</option>
                            <option value="unknown" {{ $bt === 'unknown' ? 'selected' : '' }}>Unknown</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Allergies</label>
                        <input type="text" name="allergies" class="form-control" placeholder="List any known allergies"
                               value="{{ $profile->allergies ?? $d['allergies'] ?? '' }}" oninput="capitalizeFirst(this)">
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <label class="form-label">Medical Conditions</label>
                        <input type="text" name="medical_conditions" class="form-control" placeholder="Any chronic conditions or medications"
                               value="{{ $profile->medical_conditions ?? $d['medical_conditions'] ?? '' }}" oninput="capitalizeFirst(this)">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" onclick="toggleSection('health')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Health Info</button>
                </div>
            </form>
        </div>

        <!-- Address Form -->
        <div id="address-form" class="form-section mt-4">
            <h5 class="mb-3">Address Information</h5>
            <form method="POST" action="{{ route('profile.address.update') }}">
                @csrf
                <div class="row g-2 mb-2">
                    <div class="col-3">
                        <label class="form-label">Province *</label>
                        <input type="text" name="province" class="form-control" placeholder="Province"
                               value="{{ $address->province ?? $d['province'] ?? '' }}" required oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-3">
                        <label class="form-label">City / Municipality *</label>
                        <input type="text" name="city" class="form-control" placeholder="City/Municipality"
                               value="{{ $address->city ?? $address->municipality ?? $d['city'] ?? '' }}" required oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Barangay *</label>
                        <input type="text" name="barangay" class="form-control" placeholder="Barangay"
                               value="{{ $address->barangay ?? $d['barangay'] ?? '' }}" required oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Street / Purok / House No. *</label>
                        <input type="text" name="street_address" class="form-control" placeholder="House No., Street, Purok"
                               value="{{ $address->street_address ?? $d['street_address'] ?? '' }}" required oninput="capitalizeFirst(this)">
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Zip Code</label>
                        <input type="text" name="zip_code" class="form-control" placeholder="3100"
                               value="{{ $address->zip_code ?? $d['zip_code'] ?? '' }}">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" onclick="toggleSection('address')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Address</button>
                </div>
            </form>
        </div>

        <!-- Guardian Form -->
        <div id="guardian-form" class="form-section mt-4">
            <h5 class="mb-3">Parent / Guardian Information</h5>
            <form method="POST" action="{{ route('profile.guardian.update') }}">
                @csrf
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="guardian_name" class="form-control" placeholder="Full name of parent/guardian"
                               value="{{ $guardian->name ?? $d['guardian_name'] ?? '' }}" required oninput="capitalizeFirst(this)">
                    </div>
                    <div class="col-3">
                        <label class="form-label">Relationship *</label>
                        <select name="relationship" class="form-control" required>
                            <option value="" disabled selected>Select Type</option>
                            @php $rel = strtolower($guardian->relationship ?? $d['relationship'] ?? ''); @endphp
                            <option value="father" {{ $rel === 'father' ? 'selected' : '' }}>Father</option>
                            <option value="mother" {{ $rel === 'mother' ? 'selected' : '' }}>Mother</option>
                            <option value="guardian" {{ $rel === 'guardian' ? 'selected' : '' }}>Guardian</option>
                            <option value="sibling" {{ $rel === 'sibling' ? 'selected' : '' }}>Sibling</option>
                            <option value="grandparent" {{ $rel === 'grandparent' ? 'selected' : '' }}>Grandparent</option>
                            <option value="relative" {{ $rel === 'relative' ? 'selected' : '' }}>Relative</option>
                        </select>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Occupation</label>
                        <input type="text" name="guardian_occupation" class="form-control" placeholder="Job title or profession"
                               value="{{ $guardian->occupation ?? $d['guardian_occupation'] ?? '' }}" oninput="capitalizeFirst(this)">
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label">Phone *</label>
                        <input type="tel" name="guardian_phone" class="form-control" placeholder="09XX-XXX-XXXX"
                               pattern="09[0-9]{2}-[0-9]{3}-[0-9]{4}|09[0-9]{9}"
                               title="Please enter a valid Philippine mobile number (09XX-XXX-XXXX or 09XXXXXXXXX)"
                               value="{{ $guardian->contact ?? $d['guardian_phone'] ?? '' }}" required>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Student Email Address *</label>
                        <input type="email" name="student_email" class="form-control" placeholder="This will be used to create your student login"
                               value="{{ $profile->student_email ?? $d['student_email'] ?? '' }}" required>
                    </div>
                    <div class="col-6">
                        <div style="background:#fff8ec; border:1px solid #f5d98a; border-radius:6px; padding:12px 14px; font-size:12px; color:#7d5a00; margin-top:22px;">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Your login credentials will be sent to this email once approved by the admin.
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" onclick="toggleSection('guardian')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Guardian Info</button>
                </div>
            </form>
        </div>

        <!-- School Form -->
        <div id="school-form" class="form-section mt-4">
            <h5 class="mb-3">Previous School Information</h5>
            <form method="POST" action="{{ route('profile.school.update') }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label">School Name</label>
                    <input type="text" name="school_name" class="form-control" 
                           value="{{ $previousSchool->school_name ?? $d['last_school'] ?? '' }}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">School Address</label>
                    <input type="text" name="school_address" class="form-control" 
                           value="{{ $previousSchool->school_address ?? '' }}">
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label">Last Grade Completed</label>
                        <select name="last_grade_completed" class="form-control" required>
                            <option value="" disabled>Select</option>
                            <option value="Grade 1" {{ ($previousSchool->last_grade_completed ?? '') === 'Grade 1' ? 'selected' : '' }}>Grade 1</option>
                            <option value="Grade 2" {{ ($previousSchool->last_grade_completed ?? '') === 'Grade 2' ? 'selected' : '' }}>Grade 2</option>
                            <option value="Grade 3" {{ ($previousSchool->last_grade_completed ?? '') === 'Grade 3' ? 'selected' : '' }}>Grade 3</option>
                            <option value="Grade 4" {{ ($previousSchool->last_grade_completed ?? '') === 'Grade 4' ? 'selected' : '' }}>Grade 4</option>
                            <option value="Grade 5" {{ ($previousSchool->last_grade_completed ?? '') === 'Grade 5' ? 'selected' : '' }}>Grade 5</option>
                            <option value="Grade 6" {{ ($previousSchool->last_grade_completed ?? '') === 'Grade 6' ? 'selected' : '' }}>Grade 6</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">School Year Graduated</label>
                        <input type="number" name="school_year_graduated" class="form-control" 
                               value="{{ $previousSchool->school_year_graduated ?? '' }}" 
                               min="2000" max="{{ date('Y') }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">General Average</label>
                    <input type="text" name="general_average" class="form-control" 
                           value="{{ $previousSchool->general_average ?? '' }}" 
                           placeholder="e.g., 85, 90">
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" onclick="toggleSection('school')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save School Info</button>
                </div>
            </form>
        </div>

        <!-- Enrollment Form -->
        <div id="enrollment-form" class="form-section mt-4">
            <h5 class="mb-3">Enrollment Information</h5>
            <form method="POST" action="{{ route('profile.enrollment.update') }}">
                @csrf
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <label class="form-label">Grade Level to Enroll *</label>
                        <select name="grade_level" class="form-control" required>
                            <option value="" disabled selected>Select Grade</option>
                            @php $gl = $enrollment->grade_level ?? $d['grade_level'] ?? ''; @endphp
                            <option value="nursery" {{ $gl === 'nursery' ? 'selected' : '' }}>Nursery</option>
                            <option value="kindergarten" {{ $gl === 'kindergarten' ? 'selected' : '' }}>Kindergarten</option>
                            <option value="grade1" {{ $gl === 'grade1' ? 'selected' : '' }}>Grade 1</option>
                            <option value="grade2" {{ $gl === 'grade2' ? 'selected' : '' }}>Grade 2</option>
                            <option value="grade3" {{ $gl === 'grade3' ? 'selected' : '' }}>Grade 3</option>
                            <option value="grade4" {{ $gl === 'grade4' ? 'selected' : '' }}>Grade 4</option>
                            <option value="grade5" {{ $gl === 'grade5' ? 'selected' : '' }}>Grade 5</option>
                            <option value="grade6" {{ $gl === 'grade6' ? 'selected' : '' }}>Grade 6</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label">Type of Student *</label>
                        <select name="student_type" class="form-control" required>
                            <option value="" disabled selected>Select Type</option>
                            @php $st = $profile->student_type ?? $d['student_type'] ?? ''; @endphp
                            <option value="new" {{ $st === 'new' ? 'selected' : '' }}>New Student</option>
                            <option value="transferee" {{ $st === 'transferee' ? 'selected' : '' }}>Transferee</option>
                            <option value="returning" {{ $st === 'returning' ? 'selected' : '' }}>Returning Student</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label">Last School Attended</label>
                        <input type="text" name="last_school" class="form-control" placeholder="School Name"
                               value="{{ $profile->last_school ?? $d['last_school'] ?? '' }}" oninput="capitalizeFirst(this)">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" onclick="toggleSection('enrollment')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Enrollment Info</button>
                </div>
            </form>
        </div>

        <!-- Payment Info (Read-only for student) -->
        <div id="payment-form" class="form-section mt-4">
            <h5 class="mb-3">Payment Information</h5>
            @php
                $paidAmt = $enrollment->payment_amount ?? 0;
                $totalFee = 5000; // Adjust total enrollment fee as needed
                $balance = $totalFee - $paidAmt;
            @endphp
            <div style="background:#f0f4ff; border-radius:10px; padding:16px; margin-bottom:12px;">
                <div class="row text-center">
                    <div class="col-4">
                        <div style="font-size:11px; color:#6c757d; text-transform:uppercase; letter-spacing:0.5px;">Total Fee</div>
                        <div style="font-size:20px; font-weight:700; color:var(--primary-color);">₱{{ number_format($totalFee, 2) }}</div>
                    </div>
                    <div class="col-4">
                        <div style="font-size:11px; color:#6c757d; text-transform:uppercase; letter-spacing:0.5px;">Paid</div>
                        <div style="font-size:20px; font-weight:700; color:#28a745;">₱{{ number_format($paidAmt, 2) }}</div>
                    </div>
                    <div class="col-4">
                        <div style="font-size:11px; color:#6c757d; text-transform:uppercase; letter-spacing:0.5px;">Balance</div>
                        <div style="font-size:20px; font-weight:700; color:{{ $balance > 0 ? '#dc3545' : '#28a745' }};">₱{{ number_format(max($balance, 0), 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <label class="form-label">Payment Status</label>
                    <div class="form-control" style="background:#e9ecef;">
                        @if($paymentStatus === 'paid')
                            <span style="color:#28a745; font-weight:600;"><i class="bi bi-check-circle-fill me-1"></i>Fully Paid</span>
                        @elseif($paymentStatus === 'partial')
                            <span style="color:#f0ad4e; font-weight:600;"><i class="bi bi-clock-fill me-1"></i>Partial (Installment)</span>
                        @else
                            <span style="color:#dc3545; font-weight:600;"><i class="bi bi-x-circle-fill me-1"></i>Pending</span>
                        @endif
                    </div>
                </div>
                <div class="col-6">
                    <label class="form-label">Payment Method</label>
                    <div class="form-control" style="background:#e9ecef;">
                        {{ ucfirst($enrollment->payment_method ?? 'Not yet paid') }}
                    </div>
                </div>
            </div>
            @if($enrollment->payment_reference ?? false)
            <div class="mb-2">
                <label class="form-label">Reference Number</label>
                <div class="form-control" style="background:#e9ecef;">{{ $enrollment->payment_reference }}</div>
            </div>
            @endif
            <div style="background:#fff8ec; border:1px solid #f5d98a; border-radius:8px; padding:12px; font-size:12px; color:#7d5a00;">
                <i class="bi bi-info-circle-fill me-1"></i>
                Payment is managed by the school admin. You may pay via <strong>GCash</strong> or <strong>Cash</strong>. Installment plans are available — contact the admin for details.
            </div>
            <div class="mt-3">
                <button type="button" class="btn btn-secondary" onclick="toggleSection('payment')">Close</button>
            </div>
        </div>

        <!-- Navigation -->
        <div class="navigation-footer">
            <a href="{{ route('student.portal') }}" class="btn-outline-secondary">
                <i class="bi bi-arrow-left-circle me-2"></i> Back to Student Portal
            </a>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Capitalize first letter of each word
    function capitalizeFirst(input) {
        input.value = input.value.replace(/\b\w/g, c => c.toUpperCase());
    }

    // Toggle form sections
    function toggleSection(section) {
        const formId = section + '-form';
        const form = document.getElementById(formId);
        
        // Hide all forms first
        document.querySelectorAll('.form-section').forEach(f => {
            f.classList.remove('active');
        });
        
        // Toggle current form
        if (form.style.display === 'block' || form.classList.contains('active')) {
            form.classList.remove('active');
        } else {
            form.classList.add('active');
            form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    // Load completion status on page load
    document.addEventListener('DOMContentLoaded', function() {
        fetch('{{ route("profile.status") }}')
            .then(response => response.json())
            .then(data => {
                // Update progress bar and percentage
                document.getElementById('overallPercentage').textContent = data.percentage + '%';
                document.getElementById('overallProgress').style.width = data.percentage + '%';
                
                // Update status badges
                const updateStatus = (elementId, isComplete) => {
                    const element = document.getElementById(elementId);
                    if (element) {
                        element.className = isComplete ? 'status-badge status-complete' : 'status-badge status-incomplete';
                        element.textContent = isComplete ? 'Complete' : 'Incomplete';
                    }
                };
                
                updateStatus('personalStatus', data.completion.personal);
                updateStatus('healthStatus', data.completion.health);
                updateStatus('addressStatus', data.completion.address);
                updateStatus('guardianStatus', data.completion.guardian);
                updateStatus('schoolStatus', data.completion.school);
                updateStatus('enrollmentStatus', data.completion.enrollment);
                updateStatus('paymentStatus', data.completion.payment);
            })
            .catch(error => console.error('Error loading completion status:', error));
    });

    // Handle form submissions with AJAX for better UX
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            // Let the form submit normally for now
            // Could add AJAX here for better UX
        });
    });

    // Auto-capitalize first letter of each word on text inputs
    function capitalizeFirst(input) {
        if (!input.value) return;
        var pos = input.selectionStart;
        input.value = input.value.replace(/\b\w/g, function(c) { return c.toUpperCase(); });
        input.setSelectionRange(pos, pos);
    }
    document.addEventListener('input', function(e) {
        var el = e.target;
        var isText = (el.tagName === 'INPUT' && el.type === 'text');
        var isTextarea = (el.tagName === 'TEXTAREA');
        if (!isText && !isTextarea) return;
        var skip = ['email','search','password','zip','zip_code'];
        var name = (el.name || '').toLowerCase();
        var id = (el.id || '').toLowerCase();
        var ph = (el.placeholder || '').toLowerCase();
        if (skip.some(function(s) { return name.indexOf(s) > -1 || id.indexOf(s) > -1; })) return;
        if (ph.indexOf('search') > -1 || ph.indexOf('09') === 0) return;
        capitalizeFirst(el);
    });
</script>
</body>
</html>
