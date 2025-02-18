<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, 'user']);
        $_SESSION['success'] = "Registration successful! Please login.";
        header("Location: login.php");
        exit();
    } catch(PDOException $e) {
        $error = "Registration failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TOKO GAPTECH KOMPUTER - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<style>
    :root {
        --primary-color: #333;
        --secondary-color: #555;
        --background-color: #f2f2f2;
    }

    body {
        font-family: 'Roboto', sans-serif;
        background: var(--background-color);
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    h1, h2, h3, h4, h5 {
        font-family: 'Playfair Display', serif;
    }

    .brand-logo {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
        text-decoration: none;
        margin-bottom: 2rem;
        display: block;
        text-align: center;
    }

    .auth-card {
        background: white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: none;
        max-width: 450px;
        margin: auto;
    }

    .auth-card .card-header {
        background: transparent;
        border-bottom: none;
        padding: 2rem 2rem 0;
    }

    .auth-card .card-body {
        padding: 2rem;
    }

    .auth-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
        text-align: center;
    }

    .auth-subtitle {
        color: #6b7280;
        text-align: center;
        margin-bottom: 2rem;
    }

    .form-label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-control {
        padding: 0.75rem 1rem;
        border: 1px solid #e5e7eb;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(51, 51, 51, 0.1);
    }

    .input-group-text {
        background: transparent;
        border-left: none;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--primary-color);
        border: none;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        width: 100%;
        margin-top: 1rem;
    }

    .btn-primary:hover {
        background: var(--secondary-color);
    }

    .auth-footer {
        text-align: center;
        margin-top: 1.5rem;
        color: #6b7280;
    }

    .auth-footer a {
        color: var(--primary-color);
        text-decoration: none;
    }

    .alert {
        margin-bottom: 1.5rem;
    }
</style>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <a href="index.php" class="brand-logo">
                   <h3 class="text-center"> <i class="bi bi-pc-display"></i> <strong>GAPTECH KOMPUTER</strong></h3>
                </a>
                
                <div class="auth-card card">
                    <div class="card-header">
                        <h1 class="auth-title">Create Account</h1>
                        <p class="auth-subtitle">Get started with your free account</p>
                    </div>
                    
                    <div class="card-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <div class="input-group">
                                    <input type="text" name="username" class="form-control" required>
                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <div class="input-group">
                                    <input type="email" name="email" class="form-control" required>
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control" required id="password">
                                    <span class="input-group-text" onclick="togglePassword()">
                                        <i class="bi bi-eye" id="togglePassword"></i>
                                    </span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Create Account</button>

                            <div class="auth-footer">
                                Already have an account? <a href="login.php">Sign in</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePassword');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>


