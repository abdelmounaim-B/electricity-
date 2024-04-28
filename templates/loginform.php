<?php
//ig get error = error=login
$message_content = '';
isset($_GET['error']) ?? $message_content = '<div class="error-message">Invalid email or password</div>';

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../public/css/login.css">
  <title>Document</title>
</head>

<body>
  <div class="container">
    <?php echo $message_content; ?>
    <div class="heading">Sign In</div>
    <!-- Point the action to your login PHP script and set method to POST -->
    <form action="../src/auth/login.php" method="post" class="form">
      <input required class="input" type="email" name="email" id="email" placeholder="E-mail">
      <input required class="input" type="password" name="password" id="password" placeholder="Password">
      <span class="forgot-password"><a href="#">Forgot Password ?</a></span>
      <input class="login-button" type="submit" value="Sign In">
    </form>
    <span class="agreement"><a href="#">Learn user licence agreement</a></span>
  </div>

</body>

</html>

