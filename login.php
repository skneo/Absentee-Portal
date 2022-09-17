<?php
$section = $_GET['section'];
session_start();
$showAlert = false;
if (isset($_SESSION[$section . 'loggedin'])) {
    header("Location: all_statements.php?section=$section");
}
include "$section/password.php";

if (isset($_POST['password'])) {
    if ($_POST['password'] == $password) {
        $_SESSION[$section . 'loggedin'] = true;
        if ($section == 'admin') {
            header("Location: fill_leaves.php?section=admin");
        } else header("Location: all_statements.php?section=$section");
    } else {
        $showAlert = true;
        $alertClass = "alert-danger";
        $alertMsg = "Wrong password";
        // header('Location: login.php');
    }
}
?>

<!doctype html>
<html lang='en'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <!-- Bootstrap CSS -->
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0' crossorigin='anonymous'>
    <title>Login </title>
</head>

<body>
    <center>
        <?php
        if ($showAlert) {
            echo "<div class='alert $alertClass alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >$alertMsg</strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
        }
        include 'header.php';

        ?>
        <div class="mt-5 ">
            <form action="login.php?section=<?php echo $section ?>" method="post">
                <input type="password" name="password" class="form-control mb-3 mt-5" style="width: 200px;" placeholder="enter password">
                <button type="submit" class="btn btn-primary " style="width: 200px;">Login </button>
            </form>
        </div>

    </center>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js' integrity='sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8' crossorigin='anonymous'></script>
</body>

</html>