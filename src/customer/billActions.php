<?php
require_once __DIR__ . '/../common/database.php';
$pdo = Database::connect();


require "customerController.php";




if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $uploadResult = uploadMeterPhoto($_FILES['meter_photo']);

  if (is_string($uploadResult) && file_exists('../../public/images/' . $uploadResult)) {
    $resultData = handleBillSubmission($pdo, $_POST, $uploadResult);
    header('Location: bills.php');
  } else {
    echo "File upload error: " . $uploadResult;
  }
}



// Check if the action and id parameters are provided
if (isset($_GET['action']) && isset($_GET['id'])) {
  $action = $_GET['action'];
  $billId = $_GET['id'];
  $customer_id =  isset($_GET['customer_id']) ?  $_GET['customer_id'] : $_SESSION['customer_id'];
  switch ($action) {

    case 'download':

      if (generatePDF($pdo, $customer_id, $billId)) {
        exit;
      } else {
        echo " will be aveliable soon.";
      }
      break;

    case 'pay':

      if (payBill($pdo, $billId)) {
        header('Location: bills.php');
        exit;
      } else {
        echo "Error processing payment.";
      }
      break;

    default:
      echo "Unknown action.";
      break;
  }
}
