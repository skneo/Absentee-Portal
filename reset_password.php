<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
    header("Location: login.php?section=admin");
    exit;
}
$section = $_GET['section'];

?>
<!doctype html>
<html lang='en'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <!-- Bootstrap CSS -->
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3' crossorigin='anonymous'>
    <title>Reset Password</title>
</head>

<body>
    <?php
    include 'header.php';
    function validateInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    if (isset($_POST['resetPassword'])) {
        $resetPassword = strtolower(validateInput($_POST['resetPassword']));
        if (file_exists("$resetPassword")) {
            file_put_contents("$resetPassword/password.php", "<?php\n$" . "password='0000';");
            echo "<div class='alert alert-success alert-dismissible fade show py-2 mb-0' role='alert'>
                    <strong >$resetPassword section's password reset to 0000  </strong>
                    <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
        } else {
            echo "<div class='alert alert-danger alert-dismissible fade show py-2 mb-0' role='alert'>
                    <strong >$resetPassword section does not exist </strong>
                    <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
        }
    }
    ?>
    <div class='container my-3'>
        <form method='POST' action=''>
            <div class='mb-3'>
                <label for='resetPassword' class='form-label float-start'>Section Name to Reset Password</label>
                <input type='text' class='form-control' id='resetPassword' name='resetPassword' required>
            </div>
            <button type='submit' class='btn btn-primary' onclick="return confirm('Sure to reset password of entered section?')">Reset Password</button>
        </form>
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' crossorigin='anonymous'></script>
</body>

</html>