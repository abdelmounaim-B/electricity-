<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <link rel="stylesheet" href="../../public/css/all.min.css" />
  <link rel="stylesheet" href="../../public/css/framework.css" />
  <link rel="stylesheet" href="../../public/css/master.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;500&display=swap" rel="stylesheet" />
  <script src="../../public/js/script.js"></script>
  <script>
    let docTttle = document.title;
    window.addEventListener("blur", () => {
      document.title = "Come Back 😧";
    });
    window.addEventListener("focus", () => {
      document.title = docTttle;
    });
  </script>
</head>

<body>