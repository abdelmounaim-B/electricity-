<?php
require "../auth/sessions.php";
checkLoggedInStatus();
require "adminController.php";
require_once __DIR__ . '/../common/database.php';
$pdo = Database::connect();
$id = $_GET['id'];

include_once "../../templates/header.php";
//  including sidbar 
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
  <div class="test">
    <h1 class="p-relative">customeres</h1>
  </div>

  <div class="m-20 d-grid gap-20">


    <!-- Start Settings Box -->
    <div class="p-20 bg-white rad-10 w-full">
      <h2 class="mt-0 mb-10">General Info</h2>
      <p class="mt-0 mb-20 c-grey fs-15">General Information About Your Account</p>

      <?php displayCustomerForm(getCustomerById($pdo, $id)) ?>
    </div>

    <!-- End Settings Box -->


  </div>

  <!-- End Settings Box -->

</div>
</div>
</body>

</html>