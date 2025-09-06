<?php
require_once __DIR__ . '/config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect(ADMIN_URL . '/dashboard.php');
    } else {
        redirect(USER_URL . '/index.php');
    }
}

$pageTitle = 'Login';
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
        
        <!-- Login Type Selection -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Select Login Type</h2>
            
            <div class="grid grid-cols-2 gap-4">
                <a href="login.php?type=admin" class="bg-secondary hover:bg-secondary-dark text-white font-bold py-3 px-4 rounded-lg transition duration-300 flex flex-col items-center justify-center">
                    <i class="fas fa-user-shield text-2xl mb-2"></i>
                    <span>Admin</span>
                </a>
                <a href="login.php?type=user" class="bg-primary hover:bg-primary-dark text-white font-bold py-3 px-4 rounded-lg transition duration-300 flex flex-col items-center justify-center">
                    <i class="fas fa-user text-2xl mb-2"></i>
                    <span>Customer</span>
                </a>
            </div>
        </div>
        
        <div class="text-center text-gray-600">
            <p>Don't have an account? <a href="register.php" class="text-primary hover:underline">Register now</a></p>
        </div>
    </div>
</body>
</html>
