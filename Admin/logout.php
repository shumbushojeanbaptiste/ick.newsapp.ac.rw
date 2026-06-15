<?php
require_once __DIR__ . '/config/auth.php';

logout(); // destroys the session + clears remember token

// Start a fresh session just to carry the "logged out" flash to the login page.
session_start();
set_flash('success', 'You have been logged out successfully.');

redirect('login.php');
