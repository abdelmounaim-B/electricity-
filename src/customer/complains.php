<?php
require "../auth/sessions.php";
checkLoggedInStatus();
require "customerController.php";
require_once __DIR__ . '/../common/database.php';
$pdo = Database::connect();

$id = $_SESSION['customer_id'];
$complains = selectReclamations($pdo, $id);
$customer = getCustomerById($pdo, $id);
include_once "../../templates/header.php";
include_once "../../templates/sideBar.php";

if (isset($_POST['complaint_title'], $_POST['complaint_description'])) {
  $data = [];
  if (isset($_POST['other_title']) && $_POST['complaint_title'] === 'other') {
    $data['complaint_title'] = $_POST['other_title'];
  } else {
    $data['complaint_title'] = $_POST['complaint_title'];
  }
  $data['complaint_description'] = $_POST['complaint_description'];
  $result = insertComplaint($pdo, $data, $id);
  header('Location: complains.php');
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
      <img src="<?php echo $customer['profile'] ?>" alt="" />
    </div>
  </div>
  <!-- End Head -->

  <div class="test">
    <h1 class="p-relative">Bills</h1>
    <button class="c-blue add_button" id="showConsumptionForm">Add a Complaints</button>
  </div>
  <!-- the pop up model -->
  <div id="complaintModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <div class="p-20 bg-white rad-10 w-full">
        <h2 class="mt-0 mb-10">Submit a Complaint</h2>
        <form id="complaintForm" class="complaint-form" action="complains.php" method="post">
          <div class="mb-15">
            <label class="fs-14 c-grey d-block mb-10" for="complaint_title">Complaint Title:</label>
            <select id="complaint_title" name="complaint_title" class="b-none border-ccc p-10 rad-6 d-block w-full" onchange="titleChange(this)" required>
              <option value="internal">Internal</option>
              <option value="external">External</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div class="mb-15" id="otherTitleContainer" style="display: none;">
            <label class="fs-14 c-grey d-block mb-10" for="other_title">Other Title:</label>
            <input type="text" id="other_title" name="other_title" class="b-none border-ccc p-10 rad-6 d-block w-full">
          </div>

          <div class="mb-15">
            <label class="fs-14 c-grey d-block mb-10" for="complaint_description">Complaint Description:</label>
            <textarea id="complaint_description" name="complaint_description" class="b-none border-ccc p-10 rad-6 d-block w-full" required></textarea>
          </div>

          <div class="buttons">
            <input type="submit" value="Submit" class="b-none border-ccc p-10 rad-6 d-block w-full c-blue bg-white cursor-pointer">
          </div>
        </form>
      </div>
    </div>
  </div>


  <?php
  printComplaints($complains);
  ?>

</div>
</div>
</div>
</body>

<script>
  var modal = document.getElementById('complaintModal');

  var btn = document.getElementById('showConsumptionForm');

  var span = document.getElementsByClassName('close')[0];

  btn.onclick = function() {
    modal.style.display = 'block';
  }

  span.onclick = function() {
    modal.style.display = 'none';
  }

  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = 'none';
    }
  }

  function titleChange(select) {
    var otherTitleContainer = document.getElementById('otherTitleContainer'); // Correct ID
    if (select.value === 'other') {
      otherTitleContainer.style.display = 'block'; // Show the "Other Title" container  
    } else {
      otherTitleContainer.style.display = 'none'; // Hide the "Other Title" container
    }
  }
</script>

</html>