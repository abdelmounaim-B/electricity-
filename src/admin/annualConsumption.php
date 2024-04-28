<?php
require "../auth/sessions.php";
checkLoggedInStatus();
require "adminController.php";
require_once __DIR__ . '/../common/database.php';
$pdo = Database::connect();


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
    <h1 class="p-relative">Anual Consumptions</h1>
    <button class="c-blue add_button" id="showConsumptionForm">Add a file</button>
  </div>

  <div class="m-20 d-grid gap-20">
    <div id="consumptionModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <div class="p-20 bg-white rad-10 w-full">
          <h2 class="mt-0 mb-10">General Info</h2>
          <p class="mt-0 mb-20 c-grey fs-15">General Information About Your Account</p>
          <form id="annualConsumptionForm" class="consumption-form" action="AdminController.php" method="post" enctype="multipart/form-data">
            <div class="p-20 bg-white rad-10">
              <div class="mb-15">
                <label class="fs-14 c-grey d-block mb-10" for="consumption_file">File:</label>
                <input type="file" id="consumption_file" name="consumption_file" class="b-none border-ccc p-10 rad-6 d-block w-full" required>
              </div>
              <div class="buttons">
                <input type="submit" value="Submit" class="b-none border-ccc p-10 rad-6 d-block w-full c-blue bg-white cursor-pointer">
              </div>
              <input type="hidden" name="action" value="insertAnnualConsumptions">
            </div>
          </form>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- start of the table  -->
  <div class="projects p-20 bg-white rad-10 m-20">
    <div class="test">
      <h2 class="mt-0 mb-20">Anual Consumptions</h2>
      <?php printYearLinks(); ?>
    </div>
    <div class="responsive-table">
      <?php displayAnnualConsumptionComparison($pdo); ?>
    </div>
  </div>

  <script>
    var modal = document.getElementById('consumptionModal');

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
  </script>

  <script>
    document.getElementById('yearFilter').addEventListener('change', function() {
      var selectedYear = this.value;
      var url = window.location.pathname;
      // Redirect to the same page with the selected year as a query parameter
      window.location.href = url + '?year=' + selectedYear;
    });
  </script>