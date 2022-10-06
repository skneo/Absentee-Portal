<?php
$section = $_GET['section'];

session_start();
if (!isset($_SESSION['adminloggedin'])) {
    header('Location: index.php');
    exit;
}

?>
<!doctype html>
<html lang='en'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <!-- Bootstrap CSS -->
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3' crossorigin='anonymous'>
    <title>Delete Statements</title>
</head>

<body>
    <?php
    include 'header.php';
    //delete all ststements
    $lockStatus = file_get_contents("lockStatus.json");
    $lockStatus = json_decode($lockStatus, true);
    $disableBtn = false;
    if (count($lockStatus) != 0) {
        echo "<div class='alert alert-danger alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >Unlock all sections before deleting absentee data. Remember to Select Section as 'all' </strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        $disableBtn = true;
    } else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_all'])) {
        foreach (glob('./*', GLOB_ONLYDIR) as $dir) {
            $section = basename($dir);
            if ($section == '__MACOSX' || $section == 'admin' || $section == 'zip_files')
                continue;
            file_put_contents("$section/absentee.json", "{}");
            if ($handle = opendir("$section/uploads/")) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != ".." && $file != "index.php") {
                        unlink("$section/uploads/$file");
                    }
                }
            }
        }
        file_put_contents("remarks.json", "{}");
        echo "<div class='alert alert-success alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >Absentee data deleted of all sections  </strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
    }
    ?>
    <div class="container my-3">
        <p class="text-danger fs-5">On clicking 'Delete Absentee Data of All Sections', absentee data of all sections including ESS screenshots will be permanently deleted. Export leave statements and download ESS screenshots before deleting. Data of those sections who locked their data can be accessed from Old Data link</p>
        <form method='POST'>
            <div class='mb-3'>
                <!-- <label for='' class='form-label float-start'></label> -->
                <input type='text' hidden class='form-control' id='delete_all' name='delete_all'>
            </div>
            <button type='submit' class='btn btn-danger' <?php if ($disableBtn) echo "disabled = true" ?> onclick="return confirm('Sure to delete absentee data?')">Delete Absentee Data of All Sections</button>
        </form>
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' crossorigin='anonymous'></script>
</body>

</html>