<?php
$section = $_GET['section'];
session_start();
// if (!isset($_SESSION[$section . 'loggedin'])) {
// header('Location: index.php');
// }
?>
<!doctype html>
<html lang='en'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <!-- Bootstrap CSS -->
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3' crossorigin='anonymous'>
    <title>View Screenshots</title>
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
    //checking duplicate entry
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['emp_name'])) {
        $duplicate_entry = 0;
        $absentee = file_get_contents("$section/absentee.json");
        $absentee = json_decode($absentee, true);
        $emp_num = validateInput(explode("-", $_POST['emp_name'])[0]);
        if (array_key_exists($emp_num, $absentee)) {
            $duplicate_entry = 1;
            echo "<div class='alert alert-danger alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >Error! Your leave statement already submitted, duplicate entry not allowed</strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
            echo "<a href='fill_leaves.php?section=$section' class='ms-5 my-3 btn btn-primary me-5'>
            &larr; Back</a>";
            exit;
        }
    }
    //new entry
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['emp_name']) && $duplicate_entry == 0) {
        date_default_timezone_set('Asia/Kolkata');
        $timeStamp = date('Ymd-His');
        $emp_name = validateInput(explode("-", $_POST['emp_name'])[1]);
        $emp_num = validateInput(explode("-", $_POST['emp_name'])[0]);
        $temp = explode(".", $_FILES["fileToUpload"]["name"]);
        $newfilename = $emp_num . "-$timeStamp." . end($temp);
        $target_file = "$section/uploads/" . $newfilename;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        // check file type
        if (!($file_type == 'jpg' or $file_type == 'jpeg' or $file_type == 'png')) {
            echo "<div class='alert alert-danger alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >Error, only .jpg , .jpeg and .png files are allowed to upload ! </strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
            echo "<a href='fill_leaves.php?section=$section' class='btn btn-primary mt-3 ms-3'>&larr; Back</a>";
            exit();
        }
        // Check if file already exists
        else if (file_exists($target_file)) {
            echo "<div class='alert alert-danger alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >Error! $target_file already exists </strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
            echo "<a href='fill_leaves.php?section=$section' class='btn btn-primary mt-3 ms-3'>&larr; Back</a>";
            exit();
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $filename = htmlspecialchars(basename($_FILES["fileToUpload"]["name"]));
                $absentee = file_get_contents("$section/absentee.json");
                $absentee = json_decode($absentee, true);
                if ($absentee == NULL)
                    $absentee = array();
                $employee_record = array();
                array_push($employee_record, $emp_name);
                $leave_data = array();
                $total_rows = validateInput($_POST['total_rows']);
                for ($i = 0; $i < $total_rows; $i++) {
                    $leave_type = "leave_type_" . $i;
                    $from = "from_" . $i;
                    $to = "to_" . $i;
                    if ($_POST[$leave_type] != 'NA' and $_POST[$from] != '' and $_POST[$to] != '') {
                        $row = array();
                        array_push($row, validateInput($_POST[$from]), validateInput($_POST[$to]), validateInput($_POST[$leave_type]));
                        array_push($leave_data, $row);
                    }
                }
                array_push($employee_record, $leave_data, $newfilename, 0); //0 for not verified
                $absentee[$emp_num] = $employee_record;
                file_put_contents("$section/absentee.json", json_encode($absentee));
                $emp_data = $employee_record;
                $emp_name = $emp_data[0];
                $leave_data   = $emp_data[1];
                $file_path   = $emp_data[2];
                echo "<div class='alert alert-success alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >Leave statement saved of $emp_name, kindly check your submitted data below </strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
            } else {
                echo "<div class='alert alert-danger alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >Error! your record not saved.</strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
            }
        }
    }
    //view data
    else if (isset($_GET['view_emp'])) {
        $emp_num = $_GET['view_emp'];
        $absentee = file_get_contents("$section/absentee.json");
        $absentee = json_decode($absentee, true);
        $emp_data = $absentee[$emp_num];
        $emp_name = $emp_data[0];
        $leave_data   = $emp_data[1];
        $file_path   = $emp_data[2];
    }
    //change screenshot
    else if (isset($_POST['changeScreenshot']) && isset($_SESSION[$section . 'loggedin'])) {
        date_default_timezone_set('Asia/Kolkata');
        $timeStamp = date('Ymd-His');
        $emp_num = $_GET['emp_num'];
        $temp = explode(".", $_FILES["newScreenshot"]["name"]);
        $newfilename = $emp_num . "-$timeStamp." . end($temp);
        $target_file = "$section/uploads/" . $newfilename;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        // check file type
        if (!($file_type == 'jpg' or $file_type == 'jpeg' or $file_type == 'png')) {
            echo "<div class='alert alert-danger alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >Error, only .jpg , .jpeg and .png files are allowed to upload ! </strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
            echo "<a href='all_statements.php?section=$section' class='btn btn-primary mt-3 ms-3'>&larr; Back</a>";
            exit();
        }
        if (move_uploaded_file($_FILES["newScreenshot"]["tmp_name"], $target_file)) {
            // $filename = htmlspecialchars(basename($_FILES["newScreenshot"]["name"]));
            $absentee = file_get_contents("$section/absentee.json");
            $absentee = json_decode($absentee, true);
            $emp_data = $absentee[$emp_num];
            $emp_name = $emp_data[0];
            $emp_data[2] = $newfilename;
            $emp_data[3] = 0;
            $absentee[$emp_num] = $emp_data;
            file_put_contents("$section/absentee.json", json_encode($absentee));
            $leave_data   = $emp_data[1];
            $file_path   = $emp_data[2];
            echo "<div class='alert alert-success alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >Screenshot changed of $emp_name ($emp_num)</strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        }
    }
    //edit table
    else if (isset($_POST['editTable']) && isset($_SESSION[$section . 'loggedin'])) {
        $absentee = file_get_contents("$section/absentee.json");
        $absentee = json_decode($absentee, true);
        $emp_num = $_POST['editTable'];
        $emp_data = $absentee[$emp_num];
        $leave_data = array();
        $total_rows = $_POST['total_rows'];
        for ($i = 0; $i < $total_rows; $i++) {
            $leave_type = "leave_type_" . $i;
            $from = "from_" . $i;
            $to = "to_" . $i;
            if ($_POST[$leave_type] != 'NA' and $_POST[$from] != '' and $_POST[$to] != '') {
                $row = array();
                // $fromdate = date("d.m.Y", strtotime($_POST[$from]));
                // $todate = date("d.m.Y", strtotime($_POST[$to]));
                array_push($row, validateInput($_POST[$from]), validateInput($_POST[$to]), validateInput($_POST[$leave_type]));
                array_push($leave_data, $row);
            }
        }
        $emp_data[1] = $leave_data;
        $emp_data[3] = 0;
        $absentee[$emp_num] = $emp_data;
        file_put_contents("$section/absentee.json", json_encode($absentee));
        $emp_name = $emp_data[0];
        $leave_data   = $emp_data[1];
        $file_path   = $emp_data[2];
        echo "<div class='alert alert-success alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >Leave statement updated of $emp_name ($emp_num)</strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
    }
    ?>
    <div class='container mb-5'>
        <a href="all_statements.php?section=<?php echo $section ?>" class="btn btn-primary btn-sm mt-2">&larr; Back</a>
        <?php
        echo "<p class='mt-2'><b>Employee Name:</b> $emp_name <br> <b> Employee Number:</b> $emp_num</p>";
        ?>
        <b>ESS Screenshot</b>
        <?php
        echo "<a href='$section/uploads/$file_path' class='btn btn-info btn-sm' download><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-download' viewBox='0 0 16 16'>
            <path d='M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z'></path>
            <path d='M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z'></path>
            </svg></a>";
        //checking lock status
        $lock = 0;
        $lockStatus = file_get_contents("lockStatus.json");
        $lockStatus = json_decode($lockStatus, true);
        if (array_key_exists($section, $lockStatus)) {
            $lock = $lockStatus[$section];
        }

        if (isset($_SESSION[$section . 'loggedin']) and $lock == 0) {
            echo "<a href='edit_record.php?section=$section&changeScreenshot=$emp_num' class='btn btn-info btn-sm ms-2'>Change </a>";
        }
        echo "<div style='max-height:800px' class='overflow-auto'> <img src='$section/uploads/$file_path' style='width: 1080px' class='mt-2' alt='ESS Screenshot'></div>";
        ?>

        <div class="my-3">
            <table id="table_id" class="table-bordered w-100 mb-3 text-center">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>From</th>
                        <th>Upto</th>
                        <th>Days</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_slots = count($leave_data);
                    for ($j = ($total_slots - 1); $j >= 0; $j--) {
                        $row = $leave_data[$j];
                        $from = date("d-M-y", strtotime($row[0]));
                        $to = date("d-M-y", strtotime($row[1]));
                        $days = strtotime($row[1]) - strtotime($row[0]);
                        $days = round($days / 86400) + 1;
                        $leave_type = $row[2];
                        echo "<tr>
                                <td>$leave_type</td>
                                <td>$from</td>
                                <td>$to</td>
                                <td>$days</td>
                                </tr>";
                    }
                    if ($total_slots == 0)
                        echo "<tr>
                            <td>NIL</td>
                            <td>NIL</td>
                            <td>NIL</td>
                            <td>NIL</td>
                        </tr>";
                    ?>
                </tbody>
            </table>
            <?php

            if (isset($_SESSION[$section . 'loggedin']) and $lock == 0) {
                echo "<a href='edit_record.php?section=$section&editTable=$emp_num' class='btn btn-info btn-sm mb-3'>Edit Table</a>";

                echo "<div class='row mt-3'>
                        <div class='col'>
                            <form method='POST' action='all_statements.php?section=$section'>
                            <button type='submit' onclick=\"return confirm('Sure to delete data of \'$emp_name\'?')\" class='float-start btn btn-danger' name='delete' value='$emp_num'>Delete </button>
                            </form>
                        </div>
                        <div class='col'>
                            <form method='POST' action='all_statements.php?section=$section'>
                            <button type='submit' onclick=\"return confirm('Sure to verify data of \'$emp_name\'?')\" class='btn btn-success float-end' name='verify_data' value='$emp_num'>Verify </button>
                            </form>
                        </div>
                    </div>";
            } else if ($lock == 1) {
                echo "<p class='text-danger'>Data is locked</p>";
            }
            ?>
        </div>
    </div>
    <div class="text-center bg-dark text-light py-3 mt-5" style="margin-bottom: -300px;">
        Developer: satishkushwahdigital@gmail.com
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' crossorigin='anonymous'></script>
</body>

</html>