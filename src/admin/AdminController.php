<?php

require_once __DIR__ . '/../common/database.php';
$pdo = Database::connect();

if (isset($_GET['action'])) {
  switch ($_GET['action']) {
    case 'deleteCustomer':
      deleteCustomerById($pdo, $_GET['customer_id']);
      break;
  }
}

function checkSystemHealth($pdo)
{
  try {
    $pdo->query("SELECT 1");
    return "Healthy";
  } catch (PDOException $e) {
    return "critical";
  }
}


function getAllCustomers($pdo)
{
  $query = "SELECT c.* FROM customers c
              JOIN users u ON c.user_id = u.user_id";
  $statement = $pdo->query($query);

  $customers = array();

  while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $customers[] = $row;
  }

  return $customers;
}

// Function to display customers in a table format with action buttons
function displayCustomersTable($customers)
{
  if (empty($customers)) {
    echo '<p>No customers found</p>';
    return;
  }
  echo '
  
  <div class="projects p-20 bg-white rad-10 m-20">
    <h2 class="mt-0 mb-20">Customers</h2>
    <div class="responsive-table"><table class="fs-15 w-full">
            <thead>
                <tr>
                    <td>Profile Image</td>
                    <td>Name</td>
                    <td>Address</td>
                    <td>Phone</td>
                    <td>Action</td>
                </tr>
            </thead>
            <tbody>';

  foreach ($customers as $customer) {
    // Ensure the path to the profile image is correctly specified
    $profileImagePath = !empty($customer['profile']) ? '../../public/images/' . $customer['profile'] : 'default_profile.jpg'; // Fallback to a default image if none is specified

    echo '<tr>';
    // Display profile image
    echo '<td><img src="' . $profileImagePath . '" alt="Profile Image" style="width:50px;height:50px;border-radius:50%;" /></td>'; // Example styling added for profile image
    echo '<td>' . htmlspecialchars($customer['first_name']) . ' ' . htmlspecialchars($customer['last_name']) . '</td>';
    echo '<td>' . htmlspecialchars($customer['address']) . '</td>';
    echo '<td>' . htmlspecialchars($customer['phone']) . '</td>';
    echo '<td>';
    // Edit customer link
    echo '<a href="editCustomer.php?id=' . $customer['customer_id'] . '">Edit</a>';
    echo ' | ';
    // Delete customer link
    // echo '<a href="?action=deleteCustomer&customer_id=' . $customer['customer_id'] . '" onclick="return confirm(\'Are you sure?\')">Delete</a>'; // Added a simple confirmation dialog
    // echo ' | ';
    // See bills link
    echo '<a href="Bills.php?action=seeBills&customer_id=' . $customer['customer_id'] . '">See Bills</a>';
    echo '</td>';
    echo '</tr>';
  }

  echo '</tbody>
        </table>     </div>
  </div>';
}


function displaySystemStatus($system_status)
{
  echo '<div class="reminders p-20 bg-white rad-10 p-relative">';
  echo '<h2 class="mt-0 mb-25">System status</h2>';
  echo '<ul class="m-0">';

  // Get current date and time
  $current_datetime = date("d/m/Y - h:ia");

  switch ($system_status) {
    case 'Healthy':
      echo '<li class="d-flex align-center mt-15">
                <span class="key bg-green mr-15 d-block rad-half"></span>
                <div class="pl-15 green">
                  <p class="fs-14 fw-bold mt-0 mb-5">System is Healthy</p>
                  <span class="fs-13 c-grey">' . $current_datetime . '</span>
                </div>
              </li>';
      break;
    case 'warning':
      echo '<li class="d-flex align-center mt-15">
                <span class="key bg-yellow mr-15 d-block rad-half"></span>
                <div class="pl-15 yellow">
                  <p class="fs-14 fw-bold mt-0 mb-5">System is slow !!</p>
                  <span class="fs-13 c-grey">' . $current_datetime . '</span>
                </div>
              </li>';
      break;
    case 'critical':
      echo ' <li class="d-flex align-center mt-15">
                <span class="key bg-red mr-15 d-block rad-half"></span>
                <div class="pl-15 red">
                  <p class="fs-14 fw-bold mt-0 mb-5">System is down !!</p>
                  <span class="fs-13 c-grey">' . $current_datetime . '</span>
                </div>
              </li>';
      break;
    default:
      echo '<li class="d-flex align-center mt-15">
                <span class="key bg-red mr-15 d-block rad-half"></span>
                <div class="pl-15 red">
                  <p class="fs-14 fw-bold mt-0 mb-5">Could not get the system status</p>
                  <span class="fs-13 c-grey">' . $current_datetime . '</span>
                </div>
              </li>';
      break;
  }

  echo '</ul>';
  echo '</div>';
}

function countCustomers($pdo)
{
  $query = "SELECT COUNT(*) AS customer_count FROM customers";
  $statement = $pdo->query($query);
  $result = $statement->fetch(PDO::FETCH_ASSOC);
  return $result['customer_count'];
}

function deleteCustomerById($pdo, $customer_id)
{

  $query = "DELETE FROM customers WHERE customer_id = ?";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$customer_id]);
}

function countInvoices($pdo)
{
  $query = "SELECT COUNT(*) AS invoice_count FROM bills";
  $statement = $pdo->query($query);
  $result = $statement->fetch(PDO::FETCH_ASSOC);
  return $result['invoice_count'];
}
//comlains section 

function selectlastReclamations($pdo)
{
  $query = "SELECT r.*, c.first_name, c.last_name, c.profile FROM reclamations r 
              INNER JOIN customers c ON r.customer_id = c.customer_id limit 4";
  $statement = $pdo->query($query);
  return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function selectReclamations($pdo)
{
  $query = "SELECT r.*, c.first_name, c.last_name, c.profile FROM reclamations r 
              INNER JOIN customers c ON r.customer_id = c.customer_id";
  $statement = $pdo->query($query);
  return $statement->fetchAll(PDO::FETCH_ASSOC);
}

// Function to print reclamations along with customer details and profile images
function printReclamations1($reclamations)
{
  echo '<div class="latest-news p-20 bg-white rad-10 txt-c-mobile">';
  echo '<h2 class="mt-0 mb-20">Latest Complains</h2>';

  foreach ($reclamations as $reclamation) {
    echo '<div class="news-row d-flex align-center">';
    echo '<img src="../../public/images/' . $reclamation['profile'] . '" alt="' . $reclamation['first_name'] . ' ' . $reclamation['last_name'] . '" />';
    echo '<div class="info">';
    echo '<h3>' . $reclamation['type'] . '</h3>';
    echo '<p class="m-0 fs-14 c-grey">' . $reclamation['description'] . '</p>';
    echo '</div>';
    echo '<div class="btn-shape bg-eee fs-13 label">' . $reclamation['date'] . '</div>';
    echo '</div>';
  }

  echo '</div>';
}

function getRecentInvoices($pdo)
{
  $stmt = $pdo->prepare("SELECT c.customer_id, bill_id, c.first_name AS customer_name FROM bills b JOIN customers c ON b.customer_id = c.customer_id ORDER BY b.declaration_date DESC LIMIT 6");
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function displayLatestUploads($pdo)
{
  $recentInvoices = getRecentInvoices($pdo); // Ensure this function is defined and fetches recent invoice data
if (empty($recentInvoices)) {
  echo '<p>No recent uploads found</p>';
  return;
}
  echo '<div class="latest-uploads p-20 bg-white rad-10">
        <h2 class="mt-0 mb-20">Latest Uploads</h2>
        <ul class="m-0">';

  foreach ($recentInvoices as $invoice) {
    echo '<li class="between-flex pb-10 mb-10">
             <div class="d-flex align-center">
                <a class="pdf-download-link" href="../customer/billActions.php?action=download&id=' . htmlspecialchars($invoice['bill_id']) . '&customer_id=' . htmlspecialchars($invoice['customer_id']) . '">
                    <img class="mr-10" src="../../public/images/pdf.svg" alt="Download Bill" />
                    <div>
                        <span class="d-block">IV202403' .  htmlspecialchars($invoice['bill_id']) . '</span>
                        <span class="fs-15 c-grey">' . htmlspecialchars($invoice['customer_name']) . '</span>
                    </div>
                </a>
            </div>
            <div class="bg-eee btn-shape fs-13">' .
      rand(1.1, 4.5) . 'mb</div>
            
        </li>';
  }

  echo '</ul>
    </div>';
}


function printComplaints($complaints)
{
if (empty($complaints)) {
  echo '<p>No complaints found</p>';
  return;
}

  echo '<div class="test"><h1 class="p-relative">Complaints</h1>';
  printComplaintsFilter();
  echo "</div>";
  echo '<div class="projects-page d-grid m-20 gap-20">';

  foreach ($complaints as $complaint) {
    $progressBarColor = $complaint['status'] === 'in_review' ? 'bg-red' : 'bg-green';
    $statusIcon = $complaint['status'] === 'in_review' ? 'fa-hourglass-start' : 'fa-check';
    $statusText = $complaint['status'] === 'in_review' ? 'In Review' : 'Reviewed';
    $statusBarre = $complaint['status'] === 'in_review' ? '20%' : '99%';


    echo '<div class="project bg-white p-20 rad-6 p-relative" data-status="' . htmlspecialchars($complaint['status']) . '">';
    echo '<span class="date fs-13 c-grey">' . htmlspecialchars($complaint['date']) . '</span>';
    echo '<h4 class="m-0">' . htmlspecialchars($complaint['type']) . ' - ' . htmlspecialchars($complaint['first_name']) . ' ' . htmlspecialchars($complaint['last_name']) . '</h4>';
    echo '<p class="c-grey mt-10 mb-10 fs-14">' . htmlspecialchars($complaint['description']) . '</p>';

    echo '<div class="info between-flex">';
    echo '<div class="prog bg-eee">';
    echo '<span class="' . $progressBarColor . '" style="width: ' . $statusBarre . '"></span>';
    echo '</div>';
    echo '<div class="fs-14 c-grey">';
    echo '<i class="fa-solid ' . $statusIcon . '"></i> ';
    echo $statusText;
    echo '</div>';
    echo '</div>'; // Info and progress bar end here

    if ($complaint['status'] === 'in_review') {
      echo '<div class="actions">';
      echo '<br>';
      echo '<a href="complains.php?action=setReclamationStatusToReviewed&reclamation_id=' . $complaint['reclamation_id'] .  '" class="btn-shape bg-green c-white d-block w-fit">Marke ad reviewed</a>';
      echo '</div>';
    }



    echo '</div>'; // Project div end
  }

  echo '</div>'; // Projects-page div end
}

function setReclamationStatusToReviewed($pdo, $reclamationId)
{
  $query = "UPDATE reclamations SET status = 'reviewed' WHERE reclamation_id = :reclamation_id";
  $stmt = $pdo->prepare($query);

  $stmt->bindParam(':reclamation_id', $reclamationId, PDO::PARAM_INT);
  $stmt->execute();
  header('Location: complains.php');
}

function printComplaintsFilter()
{
  echo '<div class="filter mt-0 mb-20">
            <select class=" mt-30 mr-30 filter-select" id="complaintFilter">
                <option value="all">All</option>
                <option value="in_review">In Review</option>
                <option value="reviewed">Reviewed</option>
            </select>
        </div>';
}

//end of complains section
function addCustomer($pdo, $first_name, $last_name, $address, $phone, $email, $password, $profile_pic)
{

  // Insert user into the 'users' table with role_id 2
  $queryUser = "INSERT INTO users (email, password, role_id) VALUES (?, ?, 2)";
  $statementUser = $pdo->prepare($queryUser);
  $statementUser->execute([$email, $password]);

  $userId = $pdo->lastInsertId();

  $queryCustomer = "INSERT INTO customers (user_id, first_name, last_name, address, phone, profile) VALUES (?, ?, ?, ?, ?, ?)";
  $statementCustomer = $pdo->prepare($queryCustomer);
  $statementCustomer->execute([$userId, $first_name, $last_name, $address, $phone, $profile_pic]);

  return true;
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

function getTotalValidatedConsumption($pdo, $customerId, $year)
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

function validateBill($pdo, $billId)
{
  $sql = "UPDATE bills SET validation_status = 'validated' WHERE bill_id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$billId]);
}

// Function to update customer details
function updateCustomer($pdo, $id, $first_name, $last_name, $address, $phone, $email, $password, $profile_pic)
{
  // Update users table
  $queryUser = "UPDATE users SET email = ?, password = ? WHERE user_id = (SELECT user_id FROM customers WHERE customer_id = ?)";
  $statementUser = $pdo->prepare($queryUser);
  $statementUser->execute([$email, $password, $id]);

  // Prepare the base of the update query for the customers table
  $queryCustomer = "UPDATE customers SET first_name = ?, last_name = ?, address = ?, phone = ?";
  $params = [$first_name, $last_name, $address, $phone];

  // Conditionally add the profile picture to the update if it's provided
  if (!empty($profile_pic)) {
    $queryCustomer .= ", profile = ?";
    $params[] = $profile_pic;
  }
  $queryCustomer .= " WHERE customer_id = ?";
  $params[] = $id;

  // Execute the customer update
  $statementCustomer = $pdo->prepare($queryCustomer);
  if (!$statementCustomer->execute($params)) {
    // If the execute method returns false, handle the error
    throw new Exception('Failed to update customer');
  }

  return true;
}


//fuction to display the customer updating form
function displayCustomerForm($customer)
{
  echo '<form id="customerForm" action="AdminController.php" method="POST" enctype="multipart/form-data">';
  echo '<div class="mb-15">';
  echo '<label class="fs-14 c-grey d-block mb-10" for="first">First Name</label>';
  echo '<input class="b-none border-ccc p-10 rad-6 d-block w-full" name="first" type="text" id="first" placeholder="First Name" value="' . $customer['first_name'] . '" required />';
  echo '</div>';
  echo '<div class="mb-15">';
  echo '<label class="fs-14 c-grey d-block mb-5" for="last">Last Name</label>';
  echo '<input class="b-none border-ccc p-10 rad-6 d-block w-full" name="last" id="last" type="text" placeholder="Last Name" value="' . $customer['last_name'] . '" required />';
  echo '</div>';
  echo '<div class="mb-15">';
  echo '<label class="fs-14 c-grey d-block mb-5" for="address">Address</label>';
  echo '<input class="b-none border-ccc p-10 rad-6 d-block w-full" name="address" id="address" type="text" placeholder="Address" value="' . $customer['address'] . '" required />';
  echo '</div>';
  echo '<div class="mb-15">';
  echo '<label class="fs-14 c-grey d-block mb-5" for="phone">Phone Number</label>';
  echo '<input class="b-none border-ccc p-10 rad-6 d-block w-full" name="phone" id="phone" type="tel" placeholder="Phone Number" value="' . $customer['phone'] . '" required />';
  echo '</div>';
  echo '<div class="mb-15">';
  echo '<label class="fs-14 c-grey d-block mb-5" for="email">Email</label>';
  echo '<input class="b-none border-ccc p-10 rad-6 d-block w-full" name="email" id="email" type="email" placeholder="Email" value="' . $customer['email'] . '" required />';
  echo '</div>';
  echo '<div class="mb-15">';
  echo '<label class="fs-14 c-grey d-block mb-5" for="password">Password</label>';
  echo '<input class="b-none border-ccc p-10 rad-6 d-block w-full" name="password" id="password" type="password" placeholder="Password" value="' . $customer['password'] . '" required />';
  echo '</div>';
  echo '<div class="mb-15">';
  echo '<label class="fs-14 c-grey d-block mb-5">Current Profile Picture</label>';
  // Check if there's an existing profile picture and display it; otherwise, display a default image
  $profilePicPath = !empty($customer['profile']) ? $customer['profile'] : '../../public/images/Default.png';
  echo '<img src="' . $profilePicPath . '" alt="Profile Picture" style="width: 100px; height: 100px; border-radius: 50%;">';
  echo '</div>';

  // Input for uploading a new profile picture
  echo '<div class="mb-15">';
  echo '<label class="fs-14 c-grey d-block mb-5" for="profilePic">Update Profile Picture</label>';
  echo '<input class="b-none border-ccc p-10 rad-6 d-block w-full" name="profilePic" id="profilePic" type="file" />';
  echo '</div>';

  // Other fields...
  echo '<input type="hidden" name="action" value="updateCustomer">';
  echo '<input type="hidden" name="id" value="' . $customer['customer_id'] . '" />';
  echo '<button class="add_button" type="submit">Submit</button>';
  echo '</form>';
}


function calculateTotalTTC($pdo)
{
  $query = "SELECT SUM(amount_ttc) AS total_ttc FROM bills";
  $stmt = $pdo->prepare($query);
  $stmt->execute();

  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result['total_ttc'] ?? 0;
}


function calculateTotalTTCpaid($pdo)
{
  $query = "SELECT SUM(amount_ttc) AS total_ttc FROM bills where status = 'paid'";
  $stmt = $pdo->prepare($query);
  $stmt->execute();

  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result['total_ttc'] ?? 0;
}

function calculateTotalTTCunpaid($pdo)
{
  $query = "SELECT SUM(amount_ttc) AS total_ttc FROM bills where status = 'unpaid'";
  $stmt = $pdo->prepare($query);
  $stmt->execute();

  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result['total_ttc'] ?? 0;
}

function calculatePercentageOfPaidBills($pdo)
{

  $queryAll = "SELECT COUNT(*) FROM bills";
  $stmtAll = $pdo->query($queryAll);
  $totalBills = $stmtAll->fetchColumn();

  // Query to count paid bills
  $queryPaid = "SELECT COUNT(*) FROM bills WHERE status = 'paid'";
  $stmtPaid = $pdo->query($queryPaid);
  $totalPaidBills = $stmtPaid->fetchColumn();

  // Calculate the percentage of paid bills
  if ($totalBills > 0) {
    $percentagePaid = ($totalPaidBills / $totalBills) * 100;
    return $percentagePaid;
  } else {
    return 0; // Return 0 if there are no bills to avoid division by zero
  }
}

function calculatePaidBills($pdo)
{
  $queryPaid = "SELECT COUNT(*) FROM bills WHERE status = 'paid'";
  $stmtPaid = $pdo->query($queryPaid);
  $totalPaidBills = $stmtPaid->fetchColumn();
  if ($totalPaidBills > 0) {
    return $totalPaidBills;
  } else {
    return 0;
  }
}

function calculateUnPaidBills($pdo)
{
  $queryPaid = "SELECT COUNT(*) FROM bills WHERE status = 'unpaid'";
  $stmtPaid = $pdo->query($queryPaid);
  $totalPaidBills = $stmtPaid->fetchColumn();
  if ($totalPaidBills > 0) {
    return $totalPaidBills;
  } else {
    return 0;
  }
}
function countPaidBills($pdo)
{
  $queryPaid = "SELECT COUNT(*) FROM bills WHERE status = 'paid'";
  $stmtPaid = $pdo->query($queryPaid);
  $lPaidBills = $stmtPaid->fetchColumn();
  if ($lPaidBills > 0) {
    return $lPaidBills;
  } else {
    return 0;
  }
}

function calculatePercentageOfUnpaidBills($pdo)
{
  $queryAll = "SELECT COUNT(*) FROM bills";
  $stmtAll = $pdo->query($queryAll);
  $totalBills = $stmtAll->fetchColumn();

  // Query to count unpaid bills
  $queryUnpaid = "SELECT COUNT(*) FROM bills WHERE status != 'paid'";
  $stmtUnpaid = $pdo->query($queryUnpaid);
  $totalUnpaidBills = $stmtUnpaid->fetchColumn();

  if ($totalBills > 0) {
    $percentageUnpaid = ($totalUnpaidBills / $totalBills) * 100;
    return $percentageUnpaid;
  } else {
    return 0;
  }
}

function calculatePercentageOfPaidMoney($pdo)
{
  $queryPaid = "SELECT SUM(amount_ttc) FROM bills WHERE status = 'paid'";
  $stmtPaid = $pdo->query($queryPaid);
  $totalPaidAmount = $stmtPaid->fetchColumn();

  $queryAll = "SELECT SUM(amount_ttc) FROM bills";
  $stmtAll = $pdo->query($queryAll);
  $totalAmount = $stmtAll->fetchColumn();

  if ($totalAmount > 0) {
    $percentagePaidMoney = ($totalPaidAmount / $totalAmount) * 100;
    return $percentagePaidMoney;
  } else {
    return 0;
  }
}


function getBills($pdo, $customerId = null)
{
  if ($customerId) {
    // Fetch bills for a specific user
    $query = "SELECT b.*, c.first_name, c.last_name FROM bills b INNER JOIN customers c ON b.customer_id = c.customer_id WHERE b.customer_id = ? ORDER BY b.issue_date DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$customerId]);
  } else {
    // Fetch all bills
    $query = "SELECT b.*, c.first_name, c.last_name FROM bills b INNER JOIN customers c ON b.customer_id = c.customer_id ORDER BY b.issue_date DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
  }
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function deleteBill($pdo, $billId)
{
  $sql = "DELETE FROM bills WHERE id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$billId]);
}

function displayBillsTable($bills)
{
if (empty($bills)) {
  echo '<p>No bills found</p>';
  return;
}

  echo '<table class="fs-15 w-full">
            <thead>
                <tr>
                    <td>Bill ID</td>
                    <td>Customer Name</td>
                    <td>Issue Date</td>
                    <td>Due Date</td>
                    <td>Amount TTC</td>
                    <td>consumption</td>
                    <td>Status</td>
                    <td>Validation Status</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>';

  foreach ($bills as $bill) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($bill['bill_id']) . '</td>';
    echo '<td>' . htmlspecialchars($bill['first_name']) . ' ' . htmlspecialchars($bill['last_name']) . '</td>';
    echo '<td>' . htmlspecialchars($bill['issue_date']) . '</td>';
    echo '<td>' . htmlspecialchars($bill['due_date']) . '</td>';
    echo '<td>' . htmlspecialchars($bill['amount_ttc']) . '</td>';
    echo '<td>' . htmlspecialchars($bill['monthly_consumption']) . '</td>';
    echo '<td>' . htmlspecialchars($bill['status']) . '</td>';
    echo '<td>' . htmlspecialchars(str_replace('_', ' ', $bill['validation_status'])) . '</td>';
    echo '<td>';
    if ($bill['validation_status'] === 'pending_validation') {
      echo '<div class="tooltip-icon">';
      echo '<i class="fa-solid fa-magnifying-glass"></i>';
      echo '<div class="cause-text">' . htmlspecialchars($bill['invalid_cause']) . '</div>';
      echo '</div> | ';
      echo '<a href="billsActions.php?action=validateBill&bill_id=' . $bill['bill_id'] . '">Validate</a> | ';
      echo '<a href="editBill.php?action=editBill&bill_id=' . $bill['bill_id'] . '">edit</a> |';
      echo '<a href="billsActions.php?action=declineBill&bill_id=' . $bill['bill_id'] . '">Decline</a>';
    } else {
      echo 'N/A';
    }
    echo '</td>';
    echo '</tr>';
  }

  echo '</tbody></table>';
}

//anual consumption section

function printYearLinks()
{
  $currentYear = date('Y');
  $selectedYear = isset($_GET['year']) ? $_GET['year'] : 'all';

  echo '<form action="" method="GET">
            <div class="filter mt-0 mb-20">
                <select class="mt-30 mr-30 filter-select" id="yearFilter" name="year" onchange="this.form.submit()">
                    <option value="all"' . ($selectedYear == 'all' ? ' selected' : '') . '>All Years</option>';

  $startYear = $currentYear - 10; // 10 years before the current year
  for ($year = $currentYear; $year >= $startYear; $year--) {
    echo '<option value="' . $year . '"' . ($selectedYear == $year ? ' selected' : '') . '>' . $year . '</option>';
  }

  echo '   </select>
            </div>
          </form>';
}

function getAnnualConsumptionStats($pdo, $year)
{
  if ($year === 'all') {
    $stmt = $pdo->query("SELECT acf.*, c.first_name, c.last_name FROM annual_consumption_files acf LEFT JOIN customers c ON acf.customer_id = c.customer_id");
  } else {
    $stmt = $pdo->prepare("SELECT acf.*, c.first_name, c.last_name FROM annual_consumption_files acf LEFT JOIN customers c ON acf.customer_id = c.customer_id WHERE acf.year = ?");
    $stmt->execute([$year]);
  }


  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function compareConsumption($pdo, $customerId, $year, $annualConsumption)
{
  $validatedConsumption = getTotalValidatedConsumption($pdo, $customerId);
  $difference = abs($validatedConsumption - $annualConsumption);

  if ($difference > 50) {
    return '<i class="fa-solid fa-xmark"></i>';
  } else {
    return '<i class="fa-solid fa-check"></i>';
  }
}

function displayAnnualConsumptionComparison($pdo)
{
  $year = isset($_GET['year']) && $_GET['year'] != 'all' ? $_GET['year'] : "all";

  $annualStats = getAnnualConsumptionStats($pdo, $year);
if (empty($annualStats)) {
    echo '<p>No data available for the selected year.</p>';
    return;
  }
  echo '<table class="fs-15 w-full">
            <thead>
                <tr>
                    <td>Customer ID</td>
                    <td>Customer Name</td>
                    <td>Year</td>
                    <td>Annual Consumption</td>
                    <td>Validated Consumption</td>
                    <td>Diffrence</td>
                    <td>Difference Indicator</td>
                </tr>
            </thead>
            <tbody>';

  foreach ($annualStats as $stat) {
    $customerId = $stat['customer_id'];
    $year = $stat['year'];
    $Name = $stat['first_name'] .' '. $stat['last_name'] ;
    $annualConsumption = $stat['total_consumption'];
    $validatedConsumption = getTotalValidatedConsumption($pdo, $customerId, $year);
    $difference = abs($annualConsumption - $validatedConsumption);
    $indicator = $difference > 50 ? '<i class="fa-solid fa-xmark"></i>' : '<i class="fa-solid fa-check"></i>';

    echo "<tr>
                <td>{$customerId}</td>
                <td>{$Name}</td>
                <td>{$year}</td>
                <td>{$annualConsumption}</td>
                <td>{$validatedConsumption}</td>
                <td>{$difference}</td>
                <td>{$indicator}</td>
              </tr>";
  }

  echo '</tbody></table>';
}

function importDataFromFile($pdo, $filePath)
{
  // Check if the file exists
  if (!file_exists($filePath)) {
    return "File not found.";
  }

  // Open the file for reading
  $file = fopen($filePath, "r");
  if (!$file) {
    return "Failed to open file.";
  }

  $stmt = $pdo->prepare("INSERT INTO annual_consumption_files (customer_id, year, total_consumption, file_path, insertion_date) VALUES (?, ?, ?, ?, NOW())");

  $file = fopen($filePath, "r");
  if (!$file) {
    return "Failed to open file.";
  }

  while (($line = fgets($file)) !== false
  ) {
    $parts = explode(',', $line);
    if (count($parts) === 3) {
      $parts = array_map(
        'trim',
        $parts
      );
      $stmt->execute([
        $parts[0], $parts[1], $parts[2], $filePath
      ]);
      echo $parts[0] . " " . $parts[1] . " " . $parts[2] . "<br>";
    }
  }

  // Close the file
  fclose($file);

  header('Location: annualConsumption.php');
}


function updateBill($pdo, $data, $file)
{
  list($issueDate, $dueDate) = calculateIssueAndDueDates($data['consumption_date']);

  $rateInfo = getRateInfo($pdo, $data['monthly_consumption']);
  if (!$rateInfo) {
    $rateInfo = ['rate_id' => 3, 'price_per_kwh' => 1];
  }

  $amountHT = $data['monthly_consumption'] * $rateInfo['price_per_kwh'];
  $amountTTC = $amountHT * 1.14; //  VAT is 14%

  $query = "UPDATE bills SET issue_date = ?, due_date = ?, amount_ht = ?, amount_ttc = ?, status = ?, customer_id = ?, declaration_date = NOW(), monthly_consumption = ?, photo_url = ?, rate_id = ?, validation_status = ?, invalid_cause = ? WHERE bill_id = ?";
  $stmt = $pdo->prepare($query);

  $success = $stmt->execute([
    $issueDate,
    $dueDate,
    $amountHT,
    $amountTTC,
    $data['status'],
    $data['customer_id'],
    $data['monthly_consumption'],
    $file,
    $rateInfo['rate_id'],
    $data['validation_status'],
    $data['invalid_cause'],
    $data['bill_id']
  ]);

  return $success ? true : "Failed to update the bill.";
}



// form submition handling section 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $first_name = isset($_POST['first']) ? $_POST['first'] : '';
  $last_name = isset($_POST['last']) ? $_POST['last'] : '';
  $address = isset($_POST['address']) ? $_POST['address'] : '';
  $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
  $email = isset($_POST['email']) ? $_POST['email'] : '';
  $password = isset($_POST['password']) ? $_POST['password'] : '';
  $id = isset($_POST['id']) ? $_POST['id'] : null;
  $action = isset($_POST['action']) ? $_POST['action'] : '';
  $target_dir = "../../public/images";
  $profile_pic = '';

  if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] == 0) {
    $temp_name = $_FILES['profilePic']['tmp_name'];
    $file_name = basename($_FILES['profilePic']['name']);
    $file_name = str_replace(' ', '_', $file_name);
    $target_file = $file_name;
    $target_file = $target_dir . "/" . $file_name;

    if (move_uploaded_file($temp_name, $target_file)) {
      $profile_pic = $target_file; // Use uploaded file
    }
  }

  // Switch between add and update actions
  if ($action == 'addCustomer') {
    if (addCustomer($pdo, $first_name, $last_name, $address, $phone, $email, $password, $profile_pic)) {
      header("Location: customers.php");
    } else {
      echo 'Error inserting customer';
    }
  } elseif ($action == 'updateCustomer' && $id) {
    if (updateCustomer($pdo, $id, $first_name, $last_name, $address, $phone, $email, $password, $profile_pic)) {
      header("Location: customers.php");
    } else {
      echo 'Error updating customer';
    }
  }

  if ($_POST['action'] === 'insertAnnualConsumptions') {
    if (isset($_FILES['consumption_file'])) {
      $file = $_FILES['consumption_file'];

      // Check if the file is "Consommation_annuelle.txt"
      if ($file['error'] === 0 && $file['name'] === 'Consommation_annuelle.txt') {
        $temporaryPath = $file['tmp_name'];
        $destinationPath = '../../public/images/' . $file['name'];

        if (move_uploaded_file($temporaryPath, $destinationPath)) {
          $result = importDataFromFile($pdo, $destinationPath);
          echo $result;
        } else {
          echo 'Failed to move the uploaded file.';
        }
      } else {
        echo 'Please upload "Consommation_annuelle.txt" file.';
      }
    } else {
      echo 'Missing file or year.';
    }
  }
}
