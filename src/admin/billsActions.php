<?php
require "../auth/sessions.php";
checkLoggedInStatus();
require "adminController.php";
require_once __DIR__ . '/../common/database.php';
$pdo = Database::connect();

// handel bill get request
if (isset($_GET['action']) ) {
  // action cases

  switch ($_GET['action']) {
    case 'deleteBill':
      $billId = $_GET['bill_id'];
      deleteBill($pdo, $billId);
      header('Location: bills.php');
      break;

    case 'validateBill':
      $billId = $_GET['bill_id'];
      validateBill($pdo, $billId);
      header('Location: bills.php');
      break;
    case 'declineBill':
      $billId = $_GET['bill_id'];
      declineBill($pdo, $billId);
      header('Location: bills.php');
      break;

    default:
      break;
  }
  
}

function declineBill($pdo, $billId) {
  $sql = "UPDATE bills SET validation_status = 'invalid' WHERE bill_id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$billId]);
}




