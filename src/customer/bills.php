<?php
require "../auth/sessions.php";
redirectToDashboardBasedOnRole($_SESSION['role_id']);

require_once __DIR__ . '/../common/database.php';
$pdo = Database::connect();

include_once "../../templates/header.php";
include_once "../../templates/sideBar.php";

$id = $_SESSION['customer_id'];


require "customerController.php";
$totalValidatedConsumption = getTotalValidatedConsumption($pdo, $id);
$customer = getCustomerById($pdo, $id);
$lastValidatedBillDate = getLastValidatedBillDate($pdo, $id);
$today = date("Y-m"); // Get today's date in "YYYY-MM" format
$minDate = $lastValidatedBillDate ? date("Y-m", strtotime($lastValidatedBillDate . " +1 month")) : ''; // Set min date to one month after the last validated bill


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
        <button class="c-blue add_button" id="showConsumptionForm">Add a consumption</button>
    </div>
    <div class="m-20 d-grid gap-20">
        <div id="consumptionModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <div class="p-20 bg-white rad-10 w-full">
                    <h2 class="mt-0 mb-10">General Info</h2>
                    <p class="mt-0 mb-20 c-grey fs-15">insert your consumptionbInformation</p>
                    <form id="consumptionForm" class="consumption-form" action="billActions.php" method="post" enctype="multipart/form-data">
                        <div class="p-20 bg-white rad-10">
                            <div class="mb-15">
                                <label class="fs-14 c-grey d-block mb-10" for="monthly_consumption">your consumption (kWh):</label>
                                <input type="number" id="monthly_consumption" min="<?php echo $totalValidatedConsumption ?>" name="monthly_consumption" class="b-none border-ccc p-10 rad-6 d-block w-full" required>
                            </div>

                            <div class="mb-15">
                                <label class="fs-14 c-grey d-block mb-10" for="meter_photo">meter image:</label>
                                <input type="file" id="meter_photo" name="meter_photo" class="b-none border-ccc p-10 rad-6 d-block w-full" accept="image/*" required>
                            </div>

                            <?php echo '<div class="mb-15">
                                 <label class="fs-14 c-grey d-block mb-10" for="consumption_date">Date: (should be ordered)</label>
                                 <input type="month" id="consumption_date" name="consumption_date" class="b-none border-ccc p-10 rad-6 d-block w-full" ' .
                                ($minDate ? 'min="' . $minDate . '" ' : '') .
                                '" required> 
                                </div>'; ?>
                            <div class="buttons">
                                <input type="submit" value="submit" class="b-none border-ccc p-10 rad-6 d-block w-full c-blue bg-white cursor-pointer">
                            </div>
                            <input type="hidden" name="customer_id" value="<?php echo $id ?>">
                        </div>
                    </form>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="projects p-20 bg-white rad-10 m-20">
        <div class="test">
            <h2 class="mt-0 mb-20">Bills Table</h2>
            <select class="filter-select" id="billFilter">
                <option value="all">All</option>
                <option value="paid">Paid</option>
                <option value="unpaid">Unpaid</option>
                <option value="validated">Validated</option>
                <option value="pending">Pending</option>
            </select>
        </div>
        <div class="responsive-table">
            <?php displayBillsTable(getAllBills($pdo, $id)); ?>
        </div>
    </div>
</div>

</body>
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
<!-- filter script -->
<script>
    document.getElementById('billFilter').addEventListener('change', function() {
        var selectedFilter = this.value;
        var rows = document.querySelectorAll('#billsTable tbody tr');

        rows.forEach(function(row) {
            // Assuming you have data-status and data-validation-status attributes on each row
            var status = row.getAttribute('data-status');
            var validationStatus = row.getAttribute('data-validation-status');

            // Determine if the row should be displayed based on the filter
            var shouldDisplay = false;
            if (selectedFilter === 'all') {
                shouldDisplay = true;
            } else if (selectedFilter === 'paid' || selectedFilter === 'unpaid') {
                shouldDisplay = (status === selectedFilter);
            } else if (selectedFilter === 'validated') {
                shouldDisplay = (validationStatus === 'validated');
            } else if (selectedFilter === 'pending') {
                shouldDisplay = (validationStatus === 'pending_validation');
            }

            // Update the display of the row based on the filter
            if (shouldDisplay) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>


</html>