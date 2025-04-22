<?php
session_start();

if (isset($_POST['subscribe'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Here you would typically add code to store the email in a database
        // For this example, we'll just set a success message
        
        $_SESSION['subscription_status'] = 'success';
        $_SESSION['subscription_message'] = 'Thank you for subscribing to our newsletter!';
    } else {
        $_SESSION['subscription_status'] = 'error';
        $_SESSION['subscription_message'] = 'Please enter a valid email address.';
    }
    
    // Redirect back to the referring page
    $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/newf/index.php';
    header("Location: $redirect");
    exit();
}

// If someone accesses this file directly without a form submission
header("Location: /newf/index.php");
exit();
?>
