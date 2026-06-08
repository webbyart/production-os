<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production OS - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }
        body {
            background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 10px 0;
        }
        .login-header p {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }
        .login-logo {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .login-form {
            padding: 40px 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.1);
        }
        .form-check {
            margin-top: 15px;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }
        .btn-login:hover {
            background: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }
        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .demo-credentials {
            background: #f0f9ff;
            border-left: 4px solid var(--primary);
            padding: 12px;
            border-radius: 4px;
            font-size: 12px;
            margin-top: 20px;
            line-height: 1.6;
        }
        .demo-credentials strong {
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-industry"></i>
                </div>
                <h1>Production OS</h1>
                <p>Manufacturing Management System</p>
            </div>

            <div class="login-form">
                <?php if (isset($flash) && $flash): ?>
                    <div class="alert alert-<?php echo escape($flash['type']); ?>" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo escape($flash['message']); ?>
                    </div>
                <?php endif; ?>

                <form id="loginForm" method="POST" action="<?php echo baseUrl('auth/processLogin'); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

                    <div class="form-group">
                        <label class="form-label" for="username">
                            <i class="fas fa-user"></i> Username
                        </label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember_me" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">
                            Remember me
                        </label>
                    </div>

                    <button type="submit" class="btn-login" id="submitBtn">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form>

                <div class="demo-credentials">
                    <strong>Demo Credentials:</strong><br>
                    👤 Admin: <code>admin</code> / <code>password</code><br>
                    👤 Manager: <code>manager</code> / <code>password</code><br>
                    👤 Operator: <code>operator</code> / <code>password</code>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                const submitBtn = $('#submitBtn');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Signing in...');

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.redirect;
                        } else {
                            showAlert('danger', response.message);
                            submitBtn.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        showAlert('danger', response.message || 'An error occurred');
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            function showAlert(type, message) {
                const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
                $('.login-form').prepend(alertHtml);
                setTimeout(function() {
                    $('.alert').fadeOut('slow', function() { $(this).remove(); });
                }, 5000);
            }
        });
    </script>
</body>
</html>
