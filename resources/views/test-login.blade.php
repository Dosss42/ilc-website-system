<!DOCTYPE html>
<html>
<head>
    <title>Test Login Redirect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Test Login Redirect</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('fix.login') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="remember" class="form-check-input">
                                    <label class="form-check-label">Remember me</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Test Login</button>
                        </form>
                        
                        <hr>
                        <p class="text-muted">
                            This test bypasses some checks to isolate the redirect issue.
                            Use your admin/superadmin/teacher credentials.
                        </p>
                        
                        <p><a href="{{ route('debug.login') }}">Check Database Users</a></p>
                        <p><a href="{{ route('test.login') }}">Route Test</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
