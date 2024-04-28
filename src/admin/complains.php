<?php
require "../auth/sessions.php";
checkLoggedInStatus();
require "adminController.php";
require_once __DIR__ . '/../common/database.php';
$pdo = Database::connect();

$compains = selectReclamations($pdo);


include_once "../../templates/header.php";
include_once "../../templates/sideBar.php";

if (isset($_GET['action']) && $_GET['action'] == 'setReclamationStatusToReviewed' && isset($_GET['reclamation_id'])) {
  $reclamationId = $_GET['reclamation_id'];
  $pdo = Database::connect(); // Ensure you have a method to connect to your database

  setReclamationStatusToReviewed($pdo, $reclamationId);

  // Optionally, add a session message or redirect after action
  $_SESSION['message'] = $resultMessage;
  header('Location: complains.php'); // Redirect back to the complaints page or to another confirmation page
  exit();
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
      <img src="../../public/images/avatar.png" />
    </div>
  </div>
  <!-- End Head -->
  <?php
  printComplaints($compains);
  ?>

</div>
</div>
</div>
</body>
<script>
  document.getElementById('complaintFilter').addEventListener('change', function() {
    var selectedFilter = this.value;
    var complaints = document.querySelectorAll('.project');

    complaints.forEach(function(complaint) {
      if (selectedFilter === 'all' || complaint.getAttribute('data-status') === selectedFilter) {
        complaint.style.display = ''; // Show the complaint
      } else {
        complaint.style.display = 'none'; // Hide the complaint
      }
    });
  });
</script>

</html>