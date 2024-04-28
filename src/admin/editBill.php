<?php
require "../auth/sessions.php";
checkLoggedInStatus();
// require "adminController.php";
require_once __DIR__ . '/../common/database.php';
$pdo = Database::connect();
include_once "../../templates/header.php";
//  including sidbar 
include_once "../../templates/sideBar.php";


function calculateIssueAndDueDates($consumptionDate)
{
  $issueDate = new DateTime($consumptionDate);
  $dueDate = (new DateTime($consumptionDate))->modify('+1 month');

  return [$issueDate->format('Y-m-d'), $dueDate->format('Y-m-d')];
}

function getRateInfo($pdo, $monthlyConsumption)
{
  $query = "SELECT rate_id, price_per_kwh FROM rates WHERE ? BETWEEN consumption_from AND consumption_to LIMIT 1";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$monthlyConsumption]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}


function updateBill($pdo, $data)
{
  list($issueDate, $dueDate) = calculateIssueAndDueDates($data['consumption_date']);

  $rateInfo = getRateInfo($pdo, $data['monthly_consumption']);
  if (!$rateInfo) {
    $rateInfo = ['rate_id' => 3, 'price_per_kwh' => 1];
  }

  $amountHT = $data['monthly_consumption'] * $rateInfo['price_per_kwh'];
  $amountTTC = $amountHT * 1.14; //  VAT is 14%

  $query = "UPDATE bills SET issue_date = ?, due_date = ?, amount_ht = ?, amount_ttc = ?, monthly_consumption = ?,  rate_id = ?, validation_status = ?, invalid_cause = ? WHERE bill_id = ?";
  $stmt = $pdo->prepare($query);

  $success = $stmt->execute([
    $issueDate,
    $dueDate,
    $amountHT,
    $amountTTC,
    $data['monthly_consumption'],
    $rateInfo['rate_id'],
    $data['validation_status'],
    $data['invalid_cause'],
    $data['bill_id']
  ]);

  return $success ? true : "Failed to update the bill.";
}

function getTotalValidatedConsumption($pdo, $customerId)
{
  $query = "SELECT SUM(monthly_consumption) AS total_consumption FROM bills WHERE customer_id = ? AND validation_status = 'validated'";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$customerId]);

  // Fetch the result
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($result && $result['total_consumption'] !== null) {
    return $result['total_consumption'];
  } else {
    return 0;
  }
}

?>
<div class="content w-full">
  <!-- Start Head -->
  <div class="head bg-white p-15 between-flex">
    <div class="search p-relative">
      <input class="p-10" type="search" placeholder="Type A Keyword" />
    </div>
    <div class="icons d-flex align-center">
      <span class="notification p-relative">
        <i class="fa-regular fa-bell fa-lg"></i>
      </span>
      <img src="../../public/images/avatar.png" alt="" />
    </div>
  </div>
  <!-- End Head -->
  <div class="test">
    <h1 class="p-relative">Bills</h1>
  </div>

  <div class="m-20 d-grid gap-20">

    <?php


    if (isset($_GET['action']) && $_GET['action'] === 'editBill' && isset($_GET['bill_id'])) {
      $billId = $_GET['bill_id'];

      // Fetch the details of the bill that needs to be edited
      $stmt = $pdo->prepare("SELECT * FROM bills WHERE bill_id = ?");
      $stmt->execute([$billId]);
      $bill = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($bill) {
        // Convert HTML entities to prevent XSS attacks
        $monthly_consumption = htmlspecialchars($bill['monthly_consumption']);
        $issue_date = htmlspecialchars(substr($bill['issue_date'], 0, 7));
        $bill_id = htmlspecialchars($bill['bill_id']);
        $image = htmlspecialchars($bill['photo_url']);
        $totalValidatedConsumption = getTotalValidatedConsumption($pdo, $bill['customer_id']);
        $currentMonthConsumption = $monthly_consumption - $totalValidatedConsumption;
        $data['monthly_consumption'] = $currentMonthConsumption; 
        ;
        echo '<div class="p-20 bg-white rad-10 w-full">
      <h2 class="mt-0 mb-10">Edit Bill</h2>
      <p class="mt-0 mb-20 c-grey fs-15">Modify the bill information below.</p>
      <form id="editBillForm" class="bill-form" action="editBill.php" method="post" enctype="multipart/form-data">
        <div class="p-20 bg-white rad-10">
          <div class="mb-15">
            <label class="fs-14 c-grey d-block mb-10" for="monthly_consumption">Monthly Consumption (kWh):</label>
            <input type="number" id="monthly_consumption" name="monthly_consumption" class="b-none border-ccc p-10 rad-6 d-block w-full" value="' . $totalValidatedConsumption + $monthly_consumption . '" required>
          </div>
          <div class="mb-15">
            <img src="../../public/images/' . $image . '"width="200" alt="Meter Photo" class="rad-10 mb-10">
          </div>
          <div class="mb-15">
            <label class="fs-14 c-grey d-block mb-10" for="consumption_date">Date: (should be ordered)</label>
            <input type="month" id="consumption_date" name="consumption_date" class="b-none border-ccc p-10 rad-6 d-block w-full" value="' . $issue_date . '" required>
          </div>
          <div class="buttons">
            <input type="submit" value="Update" class="b-none border-ccc p-10 rad-6 d-block w-full c-blue bg-white cursor-pointer">
            <input type="hidden" name="action" value="updateBill">
            <input type="hidden" name="bill_id" value="' . $bill_id . '">
            <input type="hidden" name="customer_id" value="' . $bill['customer_id'] . '">
          </div>
        </div>
      </form>
    </div>';
      } else {
        echo "Bill not found.";
      }
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'updateBill') {
      $totalValidatedConsumption = getTotalValidatedConsumption($pdo, $_POST['customer_id']);

      $data = [
        'bill_id' => $_POST['bill_id'],
        'monthly_consumption' => $_POST['monthly_consumption'] - $totalValidatedConsumption - $monthly_consumption,
        'consumption_date' => $_POST['consumption_date'],
        'validation_status' => 'validated', 
      ];
      $updateResult = updateBill($pdo, $data);
      if ($updateResult === true) {
        header('Location: bills.php');
      } else {
        echo "error";
        // Display the error message
        echo '<div class="alert alert-danger">' . $updateResult . '</div>';
      }
    }
