
<?php
// Start the session
session_start();

function redirectToLogin()
{
  header('Location: /tp2/templates/loginform.php');
  exit;
}
function redirectToDashboardBasedOnRole($roleId)
{
  $currentScriptName = basename($_SERVER['PHP_SELF']);

  switch ($roleId) {
    case 1: // Admin
      if ($currentScriptName !== 'index.php') { // Assuming 'index.php' is the admin dashboard
        header('Location: ../admin/index.php');
        exit;
      }
      break;
    case 2: // Customer
      if ($currentScriptName !== 'bills.php') { // Assuming 'index.php' is the customer dashboard
        header('Location: ../customer/bills.php');
        exit;
      }
      break;
    default:
      header('Location: /error.php');
      exit;
  }
}

function isLoggedIn()
{
  return isset($_SESSION['user_id']);
}

// Function to redirect users if they are not logged in
function checkLoggedInStatus()
{
  if (!isLoggedIn()) {
    redirectToLogin();
  }
}

// Function to log out the user and destroy the session
function logout()
{
  session_destroy();
  redirectToLogin();
}
