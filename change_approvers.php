<?php
$section = $_GET['section'];
$lockStatus = file_get_contents("lockStatus.json");
$lockStatus = json_decode($lockStatus, true);
if (array_key_exists($section, $lockStatus)) {
    if ($lockStatus[$section] == 1)
        header("Location: all_statements.php?section=$section");
}
session_start();
if (!( (isset($_SESSION['adminloggedin']) or isset($_SESSION[$section . 'loggedin']) ))) {
    header("Location: all_statements.php?section=$section");
}
?>
<!doctype html>
<html lang='en'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <!-- Bootstrap CSS -->
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3' crossorigin='anonymous'>
    <title>Change Authority</title>
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

    if (isset($_POST['officerName'])) {
        $inchargeEmpNo = validateInput($_POST['inchargeEmpNo']);
        // $temp = explode(".", $_FILES["inchargeSign"]["name"]);
        // $newfilename = $inchargeEmpNo . '.' . end($temp);
        // $target_file = "$section/" . $newfilename;
        // if (move_uploaded_file($_FILES["inchargeSign"]["tmp_name"], $target_file)) {
        // $filename = htmlspecialchars(basename($_FILES["inchargeSign"]["name"]));
        // echo "<div class='alert alert-success alert-dismissible fade show py-2 mb-0' role='alert'>
        //     <strong >Signature has been uploaded (Try ctrl+shift+R if not changed) </strong>
        //     <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
        // </div>";

        $officerName = validateInput($_POST['officerName']);
        $inchargeName = validateInput($_POST['inchargeName']);
        $inchargeDesig = validateInput($_POST['inchargeDesig']);
        $approversData = array();
        $approversData['officerName'] = $officerName;
        $approversData['inchargeName'] = $inchargeName;
        $approversData['inchargeEmpNo'] = $inchargeEmpNo;
        $approversData['inchargeDesig'] = $inchargeDesig;
        // $approversData['inchargeSign'] = $newfilename;
        file_put_contents("$section/approvers.json", json_encode($approversData));
        echo "<div class='alert alert-success alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >Approvers data updated</strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
    }

    $approvers = file_get_contents("$section/approvers.json");
    $approvers = json_decode($approvers, true);
    $officerName = $approvers['officerName'];
    $inchargeName = $approvers['inchargeName'];
    $inchargeEmpNo = $approvers['inchargeEmpNo'];
    $inchargeDesig = $approvers['inchargeDesig'];
    // $inchargeSign = $approvers['inchargeSign'];
    ?>
    <div class='container my-3'>
        <div id="existApprovers">
            <h4>Current Approvers</h4>
            <b>Approving Authority Name</b>
            <p><?php echo $officerName ?></p>
            <b>Incharge Name</b>
            <p><?php echo $inchargeName ?></p>
            <b>Incharge Employee Number</b>
            <p><?php echo $inchargeEmpNo ?></p>
            <b>Incharge Designation</b>
            <p><?php echo $inchargeDesig ?></p>
            <!--<b>Incharge Signature</b> <br>-->
            <!--<img src="" width="150px" height="50px" alt="Incharge Signature"><br>-->
            <button onclick="showOrHideDiv()" class="btn btn-primary my-3">Change Approvers Name</button>

        </div>
        <script>
            function showOrHideDiv() {
                var v = document.getElementById("existApprovers");
                if (v.style.display === "none") {
                    v.style.display = "block";
                } else {
                    v.style.display = "none";
                }
                var v = document.getElementById("changeApprovers");
                v.style.display = "block";
            }
        </script>
        <div id="changeApprovers" style="display:None ;">
            <a href='change_approvers.php?section=<?php echo $section ?>' class="btn btn-info mb-3">
                <- Back</a>
                    <h4>Change Approvers</h4>
                    <form method='POST' action='change_approvers.php?section=<?php echo $section ?>'>
                        <!--enctype="multipart/form-data"-->
                        <div class='mb-3'>
                            <label for='officerName' class='form-label float-start'>Approving Authority Name</label>
                            <input type='text' class='form-control' id='officerName' name='officerName' required>
                        </div>
                        <div class='mb-3'>
                            <label for='inchargeName' class='form-label float-start'>Incharge Name</label>
                            <input type='text' class='form-control' id='inchargeName' name='inchargeName' required>
                        </div>
                        <div class='mb-3'>
                            <label for='inchargeEmpNo' class='form-label float-start'>Incharge Employee Number</label>
                            <input type='text' class='form-control' id='inchargeEmpNo' name='inchargeEmpNo' required>
                        </div>
                        <div class='mb-3'>
                            <label for='inchargeDesig' class='form-label float-start'>Incharge Designation</label>
                            <input type='text' class='form-control' id='inchargeDesig' name='inchargeDesig' required>
                        </div>
                        <!--<div class='mb-3'>-->
                        <!--    <label for='inchargeSign' class='form-label float-start'>Incharge Signature</label>-->
                        <!--    <input type='file' class='form-control' id='inchargeSign' name='inchargeSign' required>-->
                        <!--</div>-->
                        <button type='submit' class='btn btn-primary'>Submit</button>
                    </form>
        </div>
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' crossorigin='anonymous'></script>
</body>

</html>