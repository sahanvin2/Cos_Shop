<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect(ADMIN_URL . '/dashboard.php');
    } else {
        redirect(USER_URL . '/index.php');
    }
}

// Check login type
$userType = isset($_GET['type']) ? $_GET['type'] : '';
if (!in_array($userType, ['admin', 'user'])) {
    setFlashMessage('error', 'Invalid login type');
    redirect(SITE_URL . '/index.php');
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password']; // No need to sanitize password
    
    // Validate input
    $errors = [];
    if (empty($username)) {
        $errors[] = 'Username is required';
    }
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    if (empty($errors)) {
        // Attempt to login
        $result = loginUser($username, $password, $userType);
        
        if ($result['success']) {
            setFlashMessage('success', 'Login successful');
            
            if ($userType === 'admin') {
                redirect(ADMIN_URL . '/dashboard.php');
            } else {
                redirect(USER_URL . '/index.php');
            }
        } else {
            $loginError = $result['message'];
        }
    }
}

$pageTitle = ucfirst($userType) . ' Login';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . ' - ' . SITE_NAME; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            light: '#F9A8D4', // Pink-300
                            DEFAULT: '#EC4899', // Pink-500
                            dark: '#BE185D',   // Pink-700
                        },
                        secondary: {
                            light: '#93C5FD', // Blue-300
                            DEFAULT: '#3B82F6', // Blue-500
                            dark: '#1E40AF',   // Blue-700
                        },
                        accent: {
                            light: '#FCD34D', // Amber-300
                            DEFAULT: '#F59E0B', // Amber-500
                            dark: '#B45309',   // Amber-700
                        },
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-auto p-6">
        <!-- Logo -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-primary">Beauty<span class="text-accent">Shop</span></h1>
            <p class="text-gray-600 mt-2">Your beauty journey starts here</p>
        </div>
        
        <!-- Flash Messages -->
        <?php
        $flashMessage = getFlashMessage();
        if ($flashMessage):
            $alertClass = '';
            switch ($flashMessage['type']) {
                case 'success':
                    $alertClass = 'bg-green-100 border-green-400 text-green-700';
                    $icon = 'fa-check-circle';
                    break;
                case 'error':
                    $alertClass = 'bg-red-100 border-red-400 text-red-700';
                    $icon = 'fa-times-circle';
                    break;
                case 'warning':
                    $alertClass = 'bg-yellow-100 border-yellow-400 text-yellow-700';
                    $icon = 'fa-exclamation-circle';
                    break;
                default:
                    $alertClass = 'bg-blue-100 border-blue-400 text-blue-700';
                    $icon = 'fa-info-circle';
            }
        ?>
        <div id="flash-message" class="border px-4 py-3 rounded relative mb-6 <?php echo $alertClass; ?>">
            <div class="flex items-center">
                <i class="fas <?php echo $icon; ?> mr-2"></i>
                <span><?php echo $flashMessage['message']; ?></span>
            </div>
            <button class="absolute top-0 right-0 mt-3 mr-4" onclick="document.getElementById('flash-message').style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>
        
        <!-- Login Form -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
                <?php echo $userType === 'admin' ? 'Admin Login' : 'Customer Login'; ?>
            </h2>
            
            <?php if (isset($loginError)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <div class="flex items-center">
                    <i class="fas fa-times-circle mr-2"></i>
                    <span><?php echo $loginError; ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($errors) && !empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <form action="login.php?type=<?php echo $userType; ?>" method="POST">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-user text-gray-400"></i>
                        </span>
                        <input type="text" id="username" name="username" 
                               value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>"
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Enter your username">
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-lock text-gray-400"></i>
                        </span>
                        <input type="password" id="password" name="password" 
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Enter your password">
                    </div>
                </div>
                
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                    </div>
                    
                    <a href="#" class="text-sm text-primary hover:underline">Forgot password?</a>
                </div>
                
                <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-md transition duration-300">
                    Login
                </button>
            </form>
        </div>
        
        <div class="text-center mt-4">
            <p class="text-gray-600">
                <?php if ($userType === 'admin'): ?>
                <a href="login.php?type=user" class="text-primary hover:underline">Login as Customer</a>
                <?php else: ?>
                <a href="login.php?type=admin" class="text-primary hover:underline">Login as Admin</a>
                <?php endif; ?>
                | <a href="register.php" class="text-primary hover:underline">Register</a>
            </p>
            <p class="text-gray-600 mt-2">
                <a href="<?php echo SITE_URL; ?>/pages/user/index.php" class="text-primary hover:underline">
                    <i class="fas fa-home mr-1"></i> Back to Home
                </a>
            </p>
        </div>
    </div>
</body>
</html>
