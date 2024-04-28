<?php

//start the session
session_start();

// Include database connection settings
include_once '../common/database.php'; // Adjust the path as needed

// Function to redirect user based on role
function redirectBasedOnRole($roleId)
{
  switch ($roleId) {
    case 1: // Admin
      header('Location: ../admin/index.php');
      exit;
    case 2: // Customer
      header('Location: ../customer/bills.php');
      exit;
    default:
      header('Location: /error.php');
      exit;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve data from POST
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';
  // Create a new PDO connection
  $pdo = Database::connect();

  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
  $stmt->bindParam(':email', $email, PDO::PARAM_STR);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && $password === $user['password']) {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE user_id = :user_id LIMIT 1");
    $stmt->bindParam(':user_id', $user['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role_id'] = $user['role_id'];

    // Check if customer is fetched successfully
    if ($customer) {

      $_SESSION['customer_id'] = $customer['customer_id'];
    } else {
      $_SESSION['customer_id'] = null;
    }
  } else {
    echo "Login failed";
    exit;
  }

  redirectBasedOnRole($user['role_id']);
} else {
  exit;
}
