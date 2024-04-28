<?php
require "../auth/sessions.php";

redirectToDashboardBasedOnRole($_SESSION['role_id']);

require "adminController.php";
require_once __DIR__ . '/../common/database.php';
$pdo = Database::connect();

$system_status = checkSystemHealth($pdo);
$customers = getAllCustomers($pdo);
$customersCount = count($customers);
$invoiceCount = countInvoices($pdo);
$reclamations = selectlastReclamations($pdo);
$totalTTC = calculateTotalTTC($pdo);
$totalTTCunpaid = calculateTotalTTCunpaid($pdo);
$percentagePaid  = calculatePercentageOfPaidBills($pdo);
$UnpaidBills = calculateUnPaidBills($pdo);
$PaidBills = calculatePaidBills($pdo);
$percentageUnpaid = calculatePercentageOfUnpaidBills($pdo);
$percentagePaidMoney = calculatePercentageOfPaidMoney($pdo);
$Totalpaid = calculateTotalTTCpaid($pdo);
$recentInvoices = getRecentInvoices($pdo);

include_once "../../templates/header.php";
include_once "../../templates/sideBar.php";

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
  <h1 class="p-relative">Dashboard</h1>
  <div class="wrapper d-grid gap-20">
    <!-- Start Welcome Widget -->
    <div class="welcome bg-white rad-10 txt-c-mobile block-mobile">
      <div class="intro p-20 d-flex space-between bg-eee">
        <div>
          <h2 class="m-0">Welcome</h2>
          <p class="c-grey mt-5">Admin</p>
        </div>
        <img class="hide-mobile" src="../../public/images/welcome.png" alt="" />
      </div>
      <img src="../../public/images/avatar.png" alt="" class="avatar" />
      <div class="body txt-c d-flex p-20 mt-20 mb-20 block-mobile">
        <div>You are : <span class="d-block c-grey fs-14 mt-10">an Admin</span></div>
        <div>number of costumers : <span class="d-block c-grey fs-14 mt-10"> <?php echo $customersCount ?> customers</span></div>
        <div>number of invoices <span class="d-block c-grey fs-14 mt-10"> <?php echo $invoiceCount ?> invoices</span></div>
      </div>
      <a href="profile.html" class="visit d-block fs-14 bg-blue c-white w-fit btn-shape">Profile</a>
    </div>

    <!-- system health -->
    <?php displaySystemStatus($system_status) ?>
    <!-- End Welcome Widget -->

    <div class="targets p-20 bg-white rad-10">
      <h2 class="mt-0 mb-10">Statistics</h2>
      <p class="mt-0 mb-20 c-grey fs-15">Statistic rates</p>
      <div class="target-row mb-20 blue center-flex">
        <div class="icon center-flex">
          <i class="fa-solid fa-dollar-sign fa-lg c-blue"></i>
        </div>
        <div class="details">
          <span class="fs-14 c-grey">Money Paid</span>
          <span class="d-block mt-5 mb-10 fw-bold"> <?= $Totalpaid ?> DH</span>
          <div class="progress p-relative">
            <span class="bg-blue blue" style="width: <?php echo $percentagePaidMoney ?>80%">
              <span class="bg-blue"><?= number_format($percentagePaidMoney)  ?>%</span>
            </span>
          </div>
        </div>
      </div>
      <div class="target-row mb-20 center-flex orange">
        <div class="icon center-flex">
          <i class="fa-solid fa-code fa-lg c-orange"></i>
        </div>
        <div class="details">
          <span class="fs-14 c-grey">Unpaid Bills</span>
          <span class="d-block mt-5 mb-10 fw-bold"><?= number_format($UnpaidBills)  ?></span>
          <div class="progress p-relative">
            <span class="bg-orange orange" style="width: <?php echo $percentageUnpaid ?>%">
              <span class="bg-orange"><?= number_format($percentageUnpaid)  ?> %</span>
            </span>
          </div>
        </div>
      </div>
      <div class="target-row mb-20 center-flex green">
        <div class="icon center-flex">
          <i class="fa-solid fa-user fa-lg c-green"></i>
        </div>
        <div class="details">
          <span class="fs-14 c-grey">Paid Bills</span>
          <span class="d-block mt-5 mb-10 fw-bold"><?= number_format($PaidBills)  ?></span>
          <div class="progress p-relative">
            <span class="bg-green green" style="width: <?php echo $percentagePaid ?>%">
              <span class="bg-green"><?= number_format($percentagePaid)  ?>%</span>
            </span>
          </div>
        </div>
      </div>
    </div>
    <!-- End Targets Widget -->

    <!-- Start Ticket Widget -->
    <div class="tickets p-20 bg-white rad-10">
      <h2 class="mt-0 mb-10">Income Statistics in Dirhams</h2>
      <p class="mt-0 mb-20 c-grey fs-15">Everything About Support Tickets</p>
      <div class="d-flex txt-c gap-20 f-wrap">
        <div class="box p-20 rad-10 fs-13 c-grey">
          <i class="fa-regular fa-rectangle-list fa-2x mb-10 c-orange"></i>
          <span class="d-block c-black fw-bold fs-25 mb-5"><?php echo $totalTTC ?></span>
          Total
        </div>
        <div class="box p-20 rad-10 fs-13 c-grey">
          <i class="fa-solid fa-spinner fa-2x mb-10 c-blue"></i>
          <span class="d-block c-black fw-bold fs-25 mb-5"> <?php echo number_format($totalTTC - $totalTTC / 1.14) ?> </span>
          Daserved TVA
        </div>
        <div class="box p-20 rad-10 fs-13 c-grey">
          <i class="fa-regular fa-circle-check fa-2x mb-10 c-green"></i>
          <span class="d-block c-black fw-bold fs-25 mb-5"><?php echo $Totalpaid ?></span>
          Total TTC Paid
        </div>
        <div class="box p-20 rad-10 fs-13 c-grey">
          <i class="fa-regular fa-rectangle-xmark fa-2x mb-10 c-red"></i>
          <span class="d-block c-black fw-bold fs-25 mb-5"><?php echo $totalTTCunpaid ?></span>
          Total TTC Unpaid

        </div>
      </div>
    </div>
    <!-- End Ticket Widget -->

    <!-- Start Latest News Widget -->
    <?php printReclamations1($reclamations); ?>
    <!-- End Latest News Widget -->


    <!-- Start Latest Uploads Widget -->
    <?php displayLatestUploads($pdo); ?>
    <!-- End Latest Uploads Widget -->


  </div>

  <!-- Start Projects Table -->

      <?php displayCustomersTable($customers) ?>


  <!-- End Projects Table -->
</div>
</div>
</body>

</html>