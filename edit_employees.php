<?php
$section = $_GET['section'];
session_start();
//if (!((isset($_SESSION['adminloggedin']) or isset($_SESSION[$section . 'loggedin'] )))) {
if (!((isset($_SESSION['adminloggedin'])))) {
    header("Location: all_statements.php?section=$section");
    exit;
}
$lockStatus = file_get_contents("lockStatus.json");
$lockStatus = json_decode($lockStatus, true);
if (array_key_exists($section, $lockStatus)) {
    if ($lockStatus[$section] == 1) {
        header("Location: all_statements.php?section=$section");
        exit;
    }
}

?>
<!doctype html>
<html lang='en'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <!-- Bootstrap CSS -->
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3' crossorigin='anonymous'>
    <title>Edit employees</title>
</head>

<body>
    <?php
    include 'header.php';
    if (strtolower($section) == 'admin') {
        echo "<div class='alert alert-danger alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >Error! Can not edit admin employees </strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        echo "<a href='fill_leaves.php?section=$section' class='btn btn-primary mt-3 ms-3'>&larr; Back</a>";
        exit();
    }
    ?>
    <div class="container my-3">
        <form method='POST' action='all_statements.php?section=<?php echo $section ?>'>
            <div class='mb-3'>
                <label for='employees' class='form-label float-start text-danger me-2'>Edit Employees Names (employees data to be written in proper format with - between employee number and employee name, make sure no blank lines inbetween and at the end) <a href="sample.txt" target='_blank'>View sample file</a></label>
                <?php
                $employees = file_get_contents("$section/employees.json");
                $employees = json_decode($employees);
                $employees = implode("\n", $employees);
                echo "<textarea class='form-control mt-3' name='edit_employees' id='edit_employees' cols='30' rows='15'>$employees</textarea>";
                ?>
            </div>
            <button type='submit' class='btn btn-primary' onclick="return confirm('Make sure employees data is in proper format')">Submit</button>
        </form>
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' crossorigin='anonymous'></script>
</body>

</html>