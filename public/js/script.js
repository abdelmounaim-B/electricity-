var modal = document.getElementById('modal');
var addCustomerBtn = document.getElementById('addCustomerBtn');
var closeBtn = document.getElementsByClassName('close')[0];

addCustomerBtn.addEventListener('click', function () {
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
    event.preventDefault(); // Prevent form submission
    var firstName = document.getElementById('first').value;
    // Get other form field values
    console.log('Adding customer with first name:', firstName);
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