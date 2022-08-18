<?php
$section = $_GET['section'];
session_start();
?>
<!doctype html>
<html lang='en'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <!-- Bootstrap CSS -->
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3' crossorigin='anonymous'>
    <title>Help</title>
</head>

<body>
    <?php
    include 'header.php';
    ?>
    <div class="container my-3">
        <h3 class="text-center"> Help</h3>
        <h4>Export Leave Statements table into excel</h4>
        <p>Export table into excel by clicking on "Export Table" button. </p>

        <h4>Forgot password</h4>
        <p>Default password is 0000, you can change it after login.</p>
        <p>Contact HR to reset your password to 0000</p>

        <h4>Features available after login</h4>
        <p>1. Verify and hightlight the absentee data submitted by employee </p>
        <p>2. Edit the data submited by employee.</p>
        <p>3. Delete the leave statement of individual employee.</p>
        <p>4. Change login password.</p>

        <i class="text-danger fs-5">Developer: satishkushwahdigital@gmail.com</i>

    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' crossorigin='anonymous'></script>
</body>

</html>