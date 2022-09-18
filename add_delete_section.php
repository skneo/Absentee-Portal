<?php
session_start();
$section = $_GET['section'];
if (!isset($_SESSION['adminloggedin'])) {
    header('Location: index.php');
}

?>
<!doctype html>
<html lang='en'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <!-- Bootstrap CSS -->
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3' crossorigin='anonymous'>
    <title>Add section</title>
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

    if (isset($_POST['sectionName']) && $_POST['sectionName'] != 'admin') {
        $sectionName = strtolower(validateInput($_POST['sectionName']));
        if (!file_exists("$sectionName")) {
            mkdir("$sectionName");
            mkdir("$sectionName/uploads");
            file_put_contents("$sectionName/absentee.json", '{}');
            file_put_contents("$sectionName/approvers.json", '{"officerName":"Bharat Singh","inchargeName":"Ram Kumar","inchargeEmpNo":"12345","inchargeDesig":"SE"}');
            file_put_contents("$sectionName/employees.json", '["21121 - Ram Kumar","32322 - Laxman Das"]');
            file_put_contents("$sectionName/password.php", "<?php\n$" . "password='0000';");
            file_put_contents("$sectionName/index.php", "<?php\nheader('Location: ../index.php');");
            echo "<div class='alert alert-success alert-dismissible fade show py-2 mb-0' role='alert'>
                    <strong >$sectionName section added </strong>
                    <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
        } else {
            echo "<div class='alert alert-danger alert-dismissible fade show py-2 mb-0' role='alert'>
                    <strong >$sectionName section already exists </strong>
                    <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
        }
    } else if (isset($_POST['deleteSection']) && $_POST['deleteSection'] != 'admin' && file_exists($_POST['deleteSection'])) {
        $deleteSection = strtolower(validateInput($_POST['deleteSection']));
        if ($handle = opendir("$deleteSection/uploads/")) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    unlink("$deleteSection/uploads/$file");
                }
            }
        }
        rmdir("$deleteSection/uploads");
        if ($handle = opendir("$deleteSection")) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    unlink("$deleteSection/$file");
                }
            }
        }
        rmdir("$deleteSection");
        echo "<div class='alert alert-success alert-dismissible fade show py-2 mb-0' role='alert'>
                    <strong >$deleteSection section deleted </strong>
                    <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
    }
    ?>
    <div class='container my-3'>
        <h4>Add Section</h4>
        <form method='POST' action=''>
            <div class='mb-3'>
                <label for='sectionName' class='form-label float-start'>Section Name (in lower case)</label>
                <input type='text' class='form-control' id='sectionName' name='sectionName' required>
            </div>
            <button type='submit' onclick="return confirm('Sure to add new section?')" class='btn btn-primary'>Submit</button>
        </form>
        <hr>
        <h4>Delete Section</h4>
        <form method='POST' action=''>
            <div class='mb-3'>
                <label for='deleteSection' class='form-label float-start'>Section Name (in lower case)</label>
                <input type='text' class='form-control' id='deleteSection' name='deleteSection' required>
            </div>
            <button type='submit' onclick="return confirm('All data of the section will be deleted, Sure to delete section?')" class='btn btn-primary'>Submit</button>
        </form>
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' crossorigin='anonymous'></script>
</body>

</html>