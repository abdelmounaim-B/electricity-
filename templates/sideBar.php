<div class="page d-flex">
  <div class="sidebar bg-white p-20 p-relative">
    <h3 class="p-relative txt-c mt-0">Electicity Bills App </h3>
    <ul>
      <?php
      if ($_SESSION['role_id'] == 1) {
        // Menu for role_id == 1 (Admin)
        echo '
              <li><a class="d-flex align-center fs-14 c-black rad-6 p-10" href="index.php"><i class="fa-regular fa-chart-bar fa-fw"></i><span>Dashboard</span></a></li>
              <li><a class="d-flex align-center fs-14 c-black rad-6 p-10" href="customers.php"><i class="fa-regular fa-user fa-fw"></i><span>Customers</span></a></li>
              <li><a class="d-flex align-center fs-14 c-black rad-6 p-10" href="bills.php"><i class="fa-solid fa-diagram-project fa-fw"></i><span>Bills</span></a></li>
              <li><a class="d-flex align-center fs-14 c-black rad-6 p-10" href="complains.php"><i class="fa-regular fa-message"></i><span>Complains</span></a></li>
              <li><a class="d-flex align-center fs-14 c-black rad-6 p-10" href="annualConsumption.php"><i class="fa-regular fa-file"></i><span>Annual Consumption</span></a></li>
          ';
      } else {
        // Menu for all other roles
        echo '
              <li><a class="d-flex align-center fs-14 c-black rad-6 p-10" href="bills.php"><i class="fa-solid fa-graduation-cap fa-fw"></i><span>Bills</span></a></li>
              <li><a class="d-flex align-center fs-14 c-black rad-6 p-10" href="complains.php"><i class="fa-regular fa-circle-user fa-fw"></i><span>Complains</span></a></li>
          ';
      }
      ?>
    </ul>
  </div>
  <script>
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