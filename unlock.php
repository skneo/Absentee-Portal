<?php
$section = $_GET['section'];
session_start();
if (!isset($_SESSION['adminloggedin'])) {
    header('Location: index.php');
    exit;
}
function validateInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
if (isset($_POST['sectionName'])) {
    $lockMessage = 'unlocked';
    $lockVar = 0;
    $dataLock = validateInput($_POST['dataLock']);
    if ($dataLock == 'lock') {
        $lockMessage = 'locked';
        $lockVar = 1;
    }
    $sectionName = validateInput($_POST['sectionName']);
    if ($sectionName == 'all' and $dataLock == 'unlock') {
        file_put_contents("lockStatus.json", '{}');
        echo "<div class='alert alert-success alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >All sections unlocked successfully </strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
    } else if ($sectionName != 'all') {
        $lockStatus = file_get_contents("lockStatus.json");
        $lockStatus = json_decode($lockStatus, true);
        $lockStatus[$sectionName] = $lockVar;
        file_put_contents("lockStatus.json", json_encode($lockStatus));
        // include 'sendMail.php';
        echo "<div class='alert alert-success alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >$sectionName section $lockMessage successfully </strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
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
    <title>Unlock Data</title>
</head>

<body>
    <?php
    include 'header.php';
    ?>
    <div class='container my-3'>
        <form method='POST' action=''>
            <div class='mb-3'>
                <label for='' class='form-label float-start'>Data Lock Status</label>
                <select class='form-select' name='dataLock'>
                    <option>unlock</option>
                    <option>lock</option>
                </select>
            </div>
            <div class='mb-3'>
                <label for='' class='form-label float-start'>Select Section (select 'all' to unlock all)</label>

                <select class='form-select' name='sectionName'>
                    <option>Select</option>
                    <option>all</option>
                    <?php
                    foreach (glob('./*', GLOB_ONLYDIR) as $dir) {
                        $dirname = basename($dir);
                        if ($dirname == '__MACOSX' || $dirname == 'zip_files')
                            continue;
                        echo "<option>$dirname</option>";
                    }
                    ?>
                </select>
            </div>
            <button type='submit' class='btn btn-primary' onclick="return confirm('Sure to unlock?')">Submit</button>
        </form>
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' crossorigin='anonymous'></script>

</body>

</html>