<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sections Management - ILC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/favicon.jpg">
    <style>
        :root {
            --blue: #1a3a6c;
            --blue-light: #2471a3;
            --blue-pale: #e8f0fb;
            --gold: #f5a623;
            --red: #e74c3c;
            --green: #27ae60;
            --orange: #e67e22;
            --white: #ffffff;
            --bg: #f0f4f8;
            --border: #e2e8f0;
            --text: #2d3748;
            --muted: #718096;
            --sidebar-w: 240px;
            --topbar-h: 62px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Open Sans', sans-serif; background: var(--bg); color: var(--text); font-size: 14px; }
        .btn { padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600; }
        .btn-primary { background: var(--blue); color: white; }
        .btn-success { background: var(--green); color: white; }
        .btn-danger { background: var(--red); color: white; }
        .btn-sm { padding: 6px 12px; font-size: 13px; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
        .bg-success { background: var(--green); color: white; }
        .bg-secondary { background: #6c757d; color: white; }
    </style>
</head>
<body>

<?php echo $__env->make('partials.admin-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="dash-main">
    <div class="section-header">
        <div>
            <h1>Sections Management</h1>
            <p>Manage class sections and teacher assignments</p>
        </div>
        <a href="#" class="btn-dash btn-primary" onclick="showSectionForm()">
            <i class="bi bi-plus-circle me-2"></i> Add New Section
        </a>
    </div>

    <div class="content-card">
        <div class="content-card-header">
            <h6>All Sections</h6>
            <a href="#" class="btn btn-sm btn-outline-primary" onclick="refreshSections()">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </a>
        </div>
        <div style="overflow-x:auto;">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Section Name</th>
                        <th>Grade Level</th>
                        <th>Adviser</th>
                        <th>Room</th>
                        <th>Capacity</th>
                        <th>Current</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="sectionsTableBody">
                    <!-- Sections will be loaded here via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Section Modal -->
<div class="modal fade" id="sectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="sectionForm">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Section Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Grade Level *</label>
                            <select name="grade_level" class="form-select" required>
                                <option value="">Select Grade</option>
                                <option value="pre-elementary">Pre-Elementary</option>
                                <option value="kindergarten">Kindergarten</option>
                                <option value="grade1">Grade 1</option>
                                <option value="grade2">Grade 2</option>
                                <option value="grade3">Grade 3</option>
                                <option value="grade4">Grade 4</option>
                                <option value="grade5">Grade 5</option>
                                <option value="grade6">Grade 6</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Adviser</label>
                            <select name="teacher_id" class="form-select">
                                <option value="">Select Teacher</option>
                                <!-- Teachers will be loaded here via JavaScript -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Room Number</label>
                            <input type="text" name="room_number" class="form-control" placeholder="e.g., Room 101">
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Max Students *</label>
                            <input type="number" name="max_students" class="form-control" min="1" max="60" value="30" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">School Year *</label>
                            <input type="text" name="school_year" class="form-control" value="2026-2027" required>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" checked>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveSection()">Save Section</button>
            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('partials.admin-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let currentSectionId = null;

    function showSectionForm(sectionId = null) {
        currentSectionId = sectionId;
        const modal = document.getElementById('sectionModal');
        const title = document.getElementById('modalTitle');
        const form = document.getElementById('sectionForm');
        
        if (sectionId) {
            // Edit mode
            title.textContent = 'Edit Section';
            // Load section data
            fetch(`/admin/sections/${sectionId}`)
                .then(response => response.json())
                .then(data => {
                    form.name.value = data.name;
                    form.grade_level.value = data.grade_level;
                    form.teacher_id.value = data.teacher_id;
                    form.room_number.value = data.room_number || '';
                    form.max_students.value = data.max_students || 30;
                    form.school_year.value = data.school_year;
                    form.is_active.checked = data.is_active;
                });
        } else {
            // Add mode
            title.textContent = 'Add Section';
            form.reset();
            form.school_year.value = '2026-2027';
        }
        
        modal.show();
    }

    function saveSection() {
        const form = document.getElementById('sectionForm');
        const formData = new FormData(form);
        
        const url = currentSectionId ? 
            `/admin/sections/${currentSectionId}` : 
            '/admin/sections';
        
        const method = currentSectionId ? 'PUT' : 'POST';
        
        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('sectionModal')).hide();
                refreshSections();
            } else {
                alert('Error: ' + (data.message || 'Unknown error occurred'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }

    function deleteSection(sectionId) {
        if (confirm('Are you sure you want to delete this section?')) {
            fetch(`/admin/sections/${sectionId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    refreshSections();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error occurred'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    }

    function refreshSections() {
        fetch('/admin/sections')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('sectionsTableBody');
                tbody.innerHTML = '';
                
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; padding:40px; color:var(--muted);">No sections found.</td></tr>';
                    return;
                }
                
                data.forEach(section => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${section.name}</td>
                        <td>${section.grade_level}</td>
                        <td>${section.teacher ? section.teacher.name : 'Not assigned'}</td>
                        <td>${section.room_number || '-'}</td>
                        <td>${section.current_enrollment}/${section.max_students || 30}</td>
                        <td>
                            <span class="badge ${section.is_active ? 'bg-success' : 'bg-secondary'}">
                                ${section.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="showSectionForm(${section.id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteSection(${section.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; color:red;">Error loading sections.</td></tr>';
            });
    }

    // Load sections on page load
    document.addEventListener('DOMContentLoaded', refreshSections);
</script>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\admin\sections\index.blade.php ENDPATH**/ ?>