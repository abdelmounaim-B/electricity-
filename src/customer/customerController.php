<?php
require_once '../../public/dompdf/autoload.inc.php';
require_once __DIR__ . '/../auth/sessions.php';
require_once __DIR__ . '/../common/database.php';
$pdo = Database::connect();
$id = $_SESSION['customer_id'];

use Dompdf\Dompdf;
use Dompdf\Options;

function getAllBills($pdo, $customerId)
{
  $query = "SELECT b.* FROM bills b WHERE b.customer_id = :customerId";

  $stmt = $pdo->prepare($query);

  $stmt->bindParam(':customerId', $customerId, PDO::PARAM_INT);

  $stmt->execute();

  $bills = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $bills;
}

function getCustomerById($pdo, $id)
{
  $query = "SELECT c.*, u.email, u.password FROM customers c
              JOIN users u ON c.user_id = u.user_id
              WHERE c.customer_id = ?";
  $statement = $pdo->prepare($query);
  $statement->execute([$id]);
  return $statement->fetch(PDO::FETCH_ASSOC);
}

function displayBillsTable($bills)
{
  echo '<table id="billsTable" class="fs-15 w-full">
            <thead>
                <tr>
                    <td>Amount</td>
                    <td>Issue Date</td>
                    <td>Due Date</td>
                    <td>Status</td>
                    <td>Declaration Date</td>
                    <td>Consumption (kWh)</td>
                    <td>Photo URL</td>
                    <td>Download</td>
                    <td>Pay</td>
                </tr>
            </thead>
            <tbody>';

  foreach ($bills as $bill) {
    $declarationDateFormatted = (new DateTime($bill['declaration_date']))->format('Y-m-d');
    $declarationDateTimeHover = (new DateTime($bill['declaration_date']))->format('Y-m-d H:i:s');

    // Add data-status and data-validation-status attributes to the <tr> tag
    echo '<tr data-status="' . htmlspecialchars($bill['status']) . '" data-validation-status="' . htmlspecialchars($bill['validation_status']) . '">';
    echo '<td>' . htmlspecialchars($bill['amount_ht']) . ' HT / ' . htmlspecialchars($bill['amount_ttc']) . ' TTC</td>';
    echo '<td>' . htmlspecialchars($bill['issue_date']) . '</td>';
    echo '<td>' . htmlspecialchars($bill['due_date']) . '</td>';
    echo '<td>' . htmlspecialchars($bill['status']) . '</td>';
    echo '<td title="Declared on: ' . $declarationDateTimeHover . '">' . $declarationDateFormatted . '</td>';
    echo '<td>' . htmlspecialchars($bill['monthly_consumption']) . '</td>';

    $photoUrl = !empty($bill['photo_url']) ? htmlspecialchars($bill['photo_url']) : 'No photo available';
    echo '<td>' . ($photoUrl !== 'No photo available' ? '<a href="../../public/images/' . $photoUrl . '" target="_blank">View Photo</a>' : $photoUrl) . '</td>';

    // Only show Download and Pay options if the bill is validated
    if ($bill['validation_status'] === 'validated') {
      echo '<td><a href="billActions.php?action=download&id=' . $bill['bill_id'] . '"><img class="mr-10" src="../../public/images/pdf.svg" alt="Download Bill"></a></td>';
      if ($bill['status'] === 'unpaid') {
        echo '<td><a href="billActions.php?action=pay&id=' . $bill['bill_id'] . '" class="pay-button">Pay</a></td>';
      } else {
        echo '<td></td>'; // No action for paid bills
      }
    } else {
      echo '<td><span class="label btn-shape bg-red c-whit">Pending Validation</span></td>';
      echo '<td></td>'; // No Pay option for bills pending validation
    }

    echo '</tr>';
  }

  echo '</tbody></table>';
}

function payBill($pdo, $billId)
{
  $sql = "UPDATE bills SET status = 'paid' WHERE bill_id = :billId";
  $stmt = $pdo->prepare($sql);

  $stmt->bindParam(':billId', $billId, PDO::PARAM_INT);

  if ($stmt->execute()) {
    return true;
  } else {
    return false;
  }
}

function uploadMeterPhoto($file)
{
  $uploadDir = '../../public/images/';
  $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
  $maxFileSize = 5 * 1024 * 1024;

  // Check for upload errors
  if ($file['error'] != 0) {
    return "Error uploading file.";
  }

  // Validate file size
  if ($file['size'] > $maxFileSize) {
    return "The file is too large. Maximum size is 5MB.";
  }

  // Validate file type
  if (!in_array($file['type'], $allowedTypes)) {
    return "Invalid file type. Only JPG, PNG, and GIF are allowed.";
  }

  // Sanitize and create a unique filename to prevent overwrites
  $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
  $fileName = uniqid("photo_", true) . '.' . $fileExt;
  $filePath = $uploadDir . $fileName;

  // Attempt to move the uploaded file to the target directory
  if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    return "Failed to save the file.";
  }
  return $fileName;
}

function generatePDF($pdo, $customer_id, $bill_id)
{


  // Fetch customer data
  $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = :customer_id");
  $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
  $stmt->execute();
  $customer = $stmt->fetch(PDO::FETCH_ASSOC);

  // Fetch bill data
  $stmt = $pdo->prepare("SELECT * FROM bills WHERE bill_id = :bill_id AND customer_id = :customer_id");
  $stmt->bindParam(':bill_id', $bill_id, PDO::PARAM_INT);
  $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
  $stmt->execute();
  $bill = $stmt->fetch(PDO::FETCH_ASSOC);

  // Fetch user data
  $stmt = $pdo->prepare("SELECT u.email FROM users u
              JOIN customers c ON u.user_id = c.user_id
              WHERE c.customer_id = :customer_id");
  $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$customer || !$bill) {
    return false;
  }

  $options = new Options();
  $options->set('isRemoteEnabled', true);
  $dompdf = new Dompdf($options);

  // HTML content
  $html = file_get_contents("../../templates/factureTemplate.html");
  $html = str_replace(
    ["{{status}}", "{{bill_id}}", "{{issue_date}}", "{{due_date}}", "{{monthly_consumption}}", "{{amount_ht}}", "{{amount_ttc}}", "{{first_name}}", "{{last_name}}", "{{address}}", "{{email}}", "{{phone}}", "{{profile}}", "{{image}}"],
    [$bill['status'], $bill['bill_id'], $bill['issue_date'], $bill['due_date'], $bill['monthly_consumption'], $bill['amount_ht'], $bill['amount_ttc'], $customer['first_name'], $customer['last_name'], $customer['address'], $user['email'], $customer['phone'], $customer['profile'], $bill['photo_url']],
    $html
  );


  $dompdf->loadHtml($html);
  $dompdf->setPaper('A4', 'portrait');
  $dompdf->render();

  $dompdf->stream("invoice-$bill_id.pdf", array("Attachment" => true));

  return true;
}


function selectReclamations($pdo, $id)
{
  $query = "SELECT r.*, c.first_name, c.last_name, c.profile FROM reclamations r 
              INNER JOIN customers c ON r.customer_id = c.customer_id where r.customer_id = ?";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$id]);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function insertBill($pdo, $data, $file)
{
  list($issueDate, $dueDate) = calculateIssueAndDueDates($data['consumption_date']);

  $rateInfo = getRateInfo($pdo, $data['monthly_consumption']);
  if (!$rateInfo) {
    // Default rate info in case no appropriate rate is found
    $rateInfo = ['rate_id' => 3, 'price_per_kwh' => 1];
  }

  $amountHT = $data['monthly_consumption'] * $rateInfo['price_per_kwh'];
  $amountTTC = $amountHT * 1.14; //TVA is 14%
  echo $data['customer_id'];

  $query = "INSERT INTO bills (issue_date, due_date, amount_ht, amount_ttc, status, customer_id, declaration_date, monthly_consumption, photo_url, rate_id, validation_status, invalid_cause)
              VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)";
  $stmt = $pdo->prepare($query);

  $success = $stmt->execute([
    $issueDate,
    $dueDate,
    $amountHT,
    $amountTTC,
    'unpaid',
    $data['customer_id'],
    $data['monthly_consumption'],
    $file,
    $rateInfo['rate_id'],
    $data['validation_status'],
    $data['invalid_cause']
  ]);

  return $success ? true : "Failed to insert the bill.";
}

function getMostRecentConsumption($pdo, $customerId)
{
  $query = "SELECT monthly_consumption FROM bills WHERE customer_id = ? ORDER BY issue_date DESC LIMIT 1";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$customerId]);

  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($result) {
    return $result['monthly_consumption'];
  } else {
    return 0;
  }
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

//bill validators and handlers

function checkBillExists($pdo, $consumptionDate, $customerId)
{
  $monthYear = explode('-', $consumptionDate);
  $query = "SELECT COUNT(*) FROM bills WHERE YEAR(issue_date) = ? AND MONTH(issue_date) = ? AND customer_id = ?";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$monthYear[0], $monthYear[1], $customerId]);
  return $stmt->fetchColumn() > 0;
}

function validateBillData($pdo, $data)
{
  if (new DateTime($data['consumption_date']) > new DateTime()) {
    return "The consumption date cannot be in the future.";
  }

  if ($data['monthly_consumption'] <= 0 || $data['monthly_consumption'] > 1000) {
    return "The monthly consumption is unacceptably high.";
  }

  // Check for existing bill
  if (checkBillExists($pdo, $data['consumption_date'], $data['customer_id'])) {
    return "A bill for the specified month already exists.";
  }

  return null;
}

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

function handleBillSubmission($pdo, $data, $file)
{
  $totalValidatedConsumption = getTotalValidatedConsumption($pdo, $data['customer_id']);

  $currentMonthConsumption = $data['monthly_consumption'] - $totalValidatedConsumption;

  if ($currentMonthConsumption < 0 || $currentMonthConsumption > 1000) {
    $data['validation_status'] = 'pending_validation';
    $data['invalid_cause'] = "Invalid consumption calculated: {$currentMonthConsumption} kWh.";
    $data['monthly_consumption'] = $currentMonthConsumption;
  } else {
    $validationMessage = validateBillData($pdo, $data);

    if ($validationMessage !== null) {
      $data['validation_status'] = 'pending_validation';
      $data['invalid_cause'] = $validationMessage;
    } else {
      $data['validation_status'] = 'validated';
      $data['invalid_cause'] = NULL;
      $data['monthly_consumption'] = $currentMonthConsumption;
    }
  }

  insertBill($pdo, $data, $file);

  return $data;
}


function getLastValidatedBillDate($pdo, $customerId)
{
  $query = "SELECT MAX(issue_date) AS last_date FROM bills WHERE customer_id = ? AND validation_status = 'validated'";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$customerId]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result ? $result['last_date'] : null;
}

function printComplaints($complaints)
{
  echo '<div class="projects-page d-grid m-20 gap-20">';

  foreach ($complaints as $complaint) {
    // Determine the progress bar color and status icon based on complaint status
    $progressBarColor = $complaint['status'] === 'in_review' ? 'bg-red' : 'bg-green';
    $statusIcon = $complaint['status'] === 'in_review' ? 'fa-hourglass-start' : 'fa-check';
    $statusText = $complaint['status'] === 'in_review' ? 'In Review' : 'Reviewed';
    $statusBarre = $complaint['status'] === 'in_review' ? '20%' : '99%';

    echo '<div class="project bg-white p-20 rad-6 p-relative">';
    echo '<span class="date fs-13 c-grey">' . htmlspecialchars($complaint['date']) . '</span>';
    echo '<h4 class="m-0">' . htmlspecialchars($complaint['type']) . '</h4>';
    echo '<p class="c-grey mt-10 mb-10 fs-14">' . htmlspecialchars($complaint['description']) . '</p>';
    echo '<div class="info between-flex">';
    echo '<div class="prog bg-eee">';
    echo '<span class="' . $progressBarColor . '" style="width: ' . $statusBarre . '"></span>';
    echo '</div>';
    echo '<div class="fs-14 c-grey">';
    echo '<i class="fa-solid ' . $statusIcon . '"></i> ';
    echo $statusText;
    echo '</div>';
    echo '</div>';

    echo '</div>';
  }

  echo '</div>';
}

function insertComplaint($pdo, $data, $id)
{
  $query = "INSERT INTO reclamations (type, description, status, date, customer_id) VALUES (?, ?, ?, NOW(), ?)";
  $stmt = $pdo->prepare($query);
  if ($data['complaint_title'] == 'other') {
    $data['complaint_title'] = $data['other_title'];
  }
  $success = $stmt->execute([$data['complaint_title'], $data['complaint_description'], 'in_review', $id]);

  return $success ? true : "Failed to insert the complaint.";
}
