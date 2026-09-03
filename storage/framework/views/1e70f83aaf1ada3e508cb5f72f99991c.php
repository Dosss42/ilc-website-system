<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents - Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/dashboard.css">
    <style>
        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            background: #fafafa;
        }
        .upload-area:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        .upload-area.dragover {
            border-color: #667eea;
            background: #f0f4ff;
        }
        .document-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        .document-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .document-icon {
            font-size: 40px;
            color: #667eea;
            margin-bottom: 15px;
        }
        .file-info {
            font-size: 12px;
            color: #666;
        }
        .status-badge {
            font-size: 11px;
            padding: 4px 12px;
            border-radius: 20px;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-verified { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .required-doc {
            position: relative;
        }
        .required-doc::after {
            content: '*';
            color: #dc3545;
            position: absolute;
            top: 0;
            right: -8px;
        }
    </style>
</head>
<body>
    <?php echo $__env->make('partials.student-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="container-fluid">
        <div class="row">
            <?php echo $__env->make('partials.student-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Document Upload</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <span class="text-muted">Complete your requirements</span>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <?php if(session('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?php echo e(session('success')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php echo e(session('error')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Upload Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-cloud-upload-fill me-2"></i>Upload New Document</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('student.documents.upload')); ?>" method="POST" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="document_type" class="form-label required-doc">Document Type</label>
                                        <select name="document_type" id="document_type" class="form-select" required>
                                            <option value="">Select Document Type</option>
                                            <option value="birth_certificate">Birth Certificate</option>
                                            <option value="form_137">Form 137 (Permanent Record)</option>
                                            <option value="report_card">Grades / Report Card</option>
                                            <option value="two_by_two_picture">2x2 ID Picture</option>
                                            <option value="others">Other Documents</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description (Optional)</label>
                                        <input type="text" name="description" id="description" class="form-control" 
                                               placeholder="Add any notes about this document">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required-doc">Select File</label>
                                        <div class="upload-area" id="uploadArea">
                                            <i class="bi bi-cloud-upload document-icon"></i>
                                            <h6>Drop file here or click to browse</h6>
                                            <p class="text-muted">Supported formats: PDF, JPG, PNG (Max 5MB)</p>
                                            <input type="file" name="file" id="fileInput" class="d-none" accept=".pdf,.jpg,.jpeg,.png" required>
                                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('fileInput').click()">
                                                <i class="bi bi-folder2-open me-2"></i>Browse Files
                                            </button>
                                        </div>
                                        <div id="fileInfo" class="file-info mt-2 d-none">
                                            <i class="bi bi-file-earmark me-2"></i>
                                            <span id="fileName"></span>
                                            <span id="fileSize" class="ms-2"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload me-2"></i>Upload Document
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Document List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Uploaded Documents</h5>
                    </div>
                    <div class="card-body">
                        <?php if($documents->count() > 0): ?>
                            <div class="row">
                                <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="document-card">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="d-flex align-items-start">
                                                    <div class="me-3">
                                                        <?php switch($document->document_type):
                                                            case ('birth_certificate'): ?>
                                                                <i class="bi bi-file-earmark-person document-icon"></i>
                                                                <?php break; ?>
                                                            <?php case ('form_137'): ?>
                                                                <i class="bi bi-file-earmark-medical document-icon"></i>
                                                                <?php break; ?>
                                                            <?php case ('report_card'): ?>
                                                                <i class="bi bi-file-earmark-check document-icon"></i>
                                                                <?php break; ?>
                                                            <?php case ('two_by_two_picture'): ?>
                                                                <i class="bi bi-person-bounding-box document-icon"></i>
                                                                <?php break; ?>
                                                            <?php default: ?>
                                                                <i class="bi bi-file-earmark document-icon"></i>
                                                        <?php endswitch; ?>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1"><?php echo e($document->document_type_display); ?></h6>
                                                        <p class="mb-1 text-muted"><?php echo e($document->original_name); ?></p>
                                                        <div class="file-info">
                                                            <i class="bi bi-hdd me-1"></i><?php echo e($document->file_size_display); ?>

                                                            <span class="ms-3">
                                                                <i class="bi bi-calendar me-1"></i>
                                                                <?php echo e($document->created_at->format('M d, Y h:i A')); ?>

                                                            </span>
                                                        </div>
                                                        <?php if($document->description): ?>
                                                            <p class="mb-0 small text-muted"><?php echo e($document->description); ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="mb-2"><?php echo e($document->status_badge); ?></div>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?php echo e(asset('storage/' . $document->file_path)); ?>"
                                                           class="btn btn-outline-primary" target="_blank" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <form action="<?php echo e(route('student.documents.delete', $document)); ?>" 
                                                              method="POST" style="display: inline-block;">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="btn btn-outline-danger" 
                                                                    onclick="return confirm('Are you sure you want to delete this document?')" 
                                                                    title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <h5 class="text-muted mt-3">No documents uploaded yet</h5>
                                <p class="text-muted">Start by uploading your required documents above.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Requirements Guide -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-info-circle-fill me-2"></i>Document Requirements Guide</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><span class="text-danger">*</span> Required Documents:</h6>
                                <ul class="small">
                                    <li><strong>Birth Certificate:</strong> PSA Birth Certificate (original or certified true copy)</li>
                                    <li><strong>Form 137:</strong> Permanent Record from previous school</li>
                                    <li><strong>Form 138:</strong> Report Card from previous school</li>
                                    <li><strong>Good Moral:</strong> Certificate of Good Moral Character</li>
                                    <li><strong>Barangay Clearance:</strong> Certificate of Residency</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>File Specifications:</h6>
                                <ul class="small">
                                    <li>Accepted formats: PDF, JPG, JPEG, PNG</li>
                                    <li>Maximum file size: 5MB per document</li>
                                    <li>Ensure documents are clear and readable</li>
                                    <li>Files should be in color format</li>
                                    <li>Upload recent documents only</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload functionality
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');

        // Click to upload
        uploadArea.addEventListener('click', () => {
            fileInput.click();
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                updateFileInfo(files[0]);
            }
        });

        // File selection
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                updateFileInfo(e.target.files[0]);
            }
        });

        function updateFileInfo(file) {
            fileName.textContent = file.name;
            fileSize.textContent = `(${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            fileInfo.classList.remove('d-none');
            
            // Validate file size
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                fileInput.value = '';
                fileInfo.classList.add('d-none');
            }
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\ron28\Desktop\ILC SYSTEM\ilc-website-system\resources\views\student\documents.blade.php ENDPATH**/ ?>