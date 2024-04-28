<?php
require "../auth/sessions.php";
checkLoggedInStatus();
require "adminController.php";
require_once __DIR__ . '/../common/database.php';
$pdo = Database::connect();

$customers = getAllCustomers($pdo);

include_once "../../templates/header.php";
//  including sidbar 
include_once "../../templates/sideBar.php";

$customerId = isset($_GET['customer_id']) ? $_GET['customer_id'] : null;
$bills = getBills($pdo, $customerId);

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

  <!-- start of pop up -->


  <div id="modal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <div class="p-20 bg-white rad-10 w-full">
        <h2 class="mt-0 mb-10">General Info</h2>
        <p class="mt-0 mb-20 c-grey fs-15">General Information About Your Account</p>
        <form id="customerForm" action="AdminController.php" method="POST" enctype="multipart/form-data">
          <div class="mb-15">
            <label class="fs-14 c-grey d-block mb-10" for="first">First Name</label>
            <input class="b-none border-ccc p-10 rad-6 d-block w-full" name="first" type="text" id="first" placeholder="First Name" required />
          </div>
          <div class="mb-15">
            <label class="fs-14 c-grey d-block mb-5" for="last">Last Name</label>
            <input class="b-none border-ccc p-10 rad-6 d-block w-full" name="last" id="last" type="text" placeholder="Last Name" required />
          </div>
          <div class="mb-15">
            <label class="fs-14 c-grey d-block mb-5" for="address">Address</label>
            <input class="b-none border-ccc p-10 rad-6 d-block w-full" name="address" id="address" type="text" placeholder="Address" required />
          </div>
          <div class="mb-15">
            <label class="fs-14 c-grey d-block mb-5" for="phone">Phone Number</label>
            <input class="b-none border-ccc p-10 rad-6 d-block w-full" name="phone" id="phone" type="tel" placeholder="Phone Number" required />
          </div>
          <div class="mb-15">
            <label class="fs-14 c-grey d-block mb-5" for="password">Email</label>
            <input class="b-none border-ccc p-10 rad-6 d-block w-full" name="email" id="password" type="email" placeholder="Phone Number" required />
          </div>
          <div class="mb-15">
            <label class="fs-14 c-grey d-block mb-5" for="password">Password</label>
            <input class="b-none border-ccc p-10 rad-6 d-block w-full" name="password" id="password" type="password" placeholder="Phone Number" required />
          </div>
          <div class="mb-15">
            <label class="fs-14 c-grey d-block mb-5" for="profilePic">Profile Picture</label>
            <input class="b-none border-ccc p-10 rad-6 d-block w-full" name="profilePic" id="profilePic" type="file" />
          </div>
          <input type="hidden" name="action" value="addCustomer">
          <button class="add_button" type="submit">Submit</button>
        </form>
      </div>
    </div>
  </div>

  <!-- end of pop up -->

  <!-- Start Projects Table -->
  <div class="projects p-20 bg-white rad-10 m-20">
    <h2 class="mt-0 mb-20">Bills</h2>
    <div class="responsive-table">
      <?php displayBillsTable($bills); ?>
    </div>
  </div>
  <!-- End Projects Table -->
  <div class="settings-page m-20 d-grid gap-20">


  </div>

  <!-- End Settings Box -->

</div>
</div>
</body>
<script>
  var modal = document.getElementById('modal');
  var addCustomerBtn = document.getElementById('addCustomerBtn');
  var closeBtn = document.getElementsByClassName('close')[0];

  addCustomerBtn.addEventListener('click', function() {
    console.log('Add customer button clicked');
    modal.style.display = 'block';
  });

  closeBtn.onclick = function() {
    modal.style.display = 'none';
  };

  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = 'none';
    }
  };

  document.getElementById('customerForm').addEventListener('submit', function(event) {

    modal.style.display = 'none'; // Close the modal after submission
  });


  window.onload = function() {
    var currentUrl = window.location.href;
    var links = document.querySelectorAll('.sidebar a');
    links.forEach(function(link) {
      if (link.href === currentUrl) {
        link.classList.add('active');
      }
    });
  };
</script>

</html>