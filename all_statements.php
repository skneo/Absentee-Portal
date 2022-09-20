<?php
session_start();
// if (!isset($_SESSION[$section . 'loggedin'])) {
//     header('Location: index.php');
// }
$section = $_GET['section'];
function validateInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
<!doctype html>
<html lang='en'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <!-- Bootstrap CSS -->
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3' crossorigin='anonymous'>
    <title>All Leave Statements - <?php echo strtoupper($section) ?></title>
</head>

<body>
    <?php
    include 'header.php';
    //data verification
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_data'])) {
        $emp_num = $_POST['verify_data'];
        $absentee = file_get_contents("$section/absentee.json");
        $absentee = json_decode($absentee, true);
        $emp_data = $absentee[$emp_num];
        $emp_data[3] = 1;
        $absentee[$emp_num] = $emp_data;
        $emp_name = $emp_data[0];
        file_put_contents("$section/absentee.json", json_encode($absentee));
        echo "<div class='alert alert-success alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >Data verified of $emp_name.</strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
    }
    //delete
    else if (isset($_POST['delete'])) {
        $emp_num = $_POST['delete'];
        $absentee = file_get_contents("$section/absentee.json");
        $absentee = json_decode($absentee, true);
        $emp_data = $absentee[$emp_num];
        $file = $emp_data[2];
        $emp_name = $emp_data[0];
        if (file_exists("$section/uploads/" . $file)) {
            unlink("$section/uploads/" . $file);
            echo "<div class='alert alert-success alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >$file deleted </strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        }
        unset($absentee[$emp_num]);
        file_put_contents("$section/absentee.json", json_encode($absentee));
        echo "<div class='alert alert-success alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >Leave statement of $emp_name deleted </strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
    }
    //edit employees
    else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_employees']) && isset($_SESSION['adminloggedin'])) {
        $edit_employees   =  $_POST['edit_employees'];
        $edit_employees = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', ' ', $edit_employees);
        $edit_employees = preg_split("/\r\n|\n|\r/", $edit_employees);
        $totalEmployees = count($edit_employees);
        $edit_employees = json_encode($edit_employees, true);
        file_put_contents("$section/employees.json", $edit_employees);
        echo "<div class='alert alert-success alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >$totalEmployees Employees added in $section section </strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
    }
    //lock
    else if (isset($_POST['lock'])) {
        //saving section lock status 
        $lock = validateInput($_POST['lock']);
        $lockStatus = file_get_contents("lockStatus.json");
        $lockStatus = json_decode($lockStatus, true);
        $lockStatus[$section] = 1;
        // $_SESSION['sectionLock'] = true;
        file_put_contents("lockStatus.json", json_encode($lockStatus));
        //saving remark 
        $remarks = file_get_contents("remarks.json");
        $remarks = json_decode($remarks, true);
        $inchargeRemark = validateInput($_POST["inchargeRemark"]);
        $remarks[$section] = $inchargeRemark;
        file_put_contents("remarks.json", json_encode($remarks));

        //zipping all screenshots
        date_default_timezone_set('Asia/Kolkata');
        $from = date('16-M-Y', strtotime('-1 month'));
        $to = date("15-M-Y");
        $zip_name = "zip_files/" . strtoupper($section) . "_ESS_screenshots_$from" . "_to_" . "$to" . ".zip";
        $showAlert = false;
        // Get real path for our folder
        $rootPath = realpath("$section/uploads");
        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($zip_name, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }
        // Zip archive will be created only after closing object
        $zip->close();

        //saving copy of absentee data
        $absentee = file_get_contents("$section/absentee.json");
        $absentee = json_decode($absentee, true);
        $absentee_employees = array_keys($absentee);
        sort($absentee_employees);
        // $absentee_employees = array_values($absentee_employees);
        $sn = 1;
        $total_emp = count($absentee_employees);
        $absenteeArrray = array();
        $absenteeRow = array('SN', 'Employee Number', 'Employee Name', 'Leave Type', 'Leave From', 'Leave Upto');
        array_push($absenteeArrray, $absenteeRow);
        for ($i = 0; $i < $total_emp; $i++) {
            $emp_num = $absentee_employees[$i];
            $emp_data = $absentee[$emp_num];
            $emp_name = strtoupper($emp_data[0]);
            $leave_data   = $emp_data[1];
            $total_slots = count($leave_data);
            for ($j = 0; $j < $total_slots; $j++) {
                $absenteeRow = array();
                $row = $leave_data[$j];
                $from = date("d-M-y", strtotime($row[0]));
                $to = date("d-M-y", strtotime($row[1]));
                $leave_type = $row[2];
                array_push($absenteeRow, $sn, $emp_num, $emp_name, $leave_type, $from, $to);
                array_push($absenteeArrray, $absenteeRow);
            }
            if ($total_slots == 0) {
                $absenteeRow = array();
                array_push($absenteeRow, $sn, $emp_num, $emp_name, 'NIL', 'NIL', 'NIL');
                array_push($absenteeArrray, $absenteeRow);
            }
            $sn = $sn + 1;
        }
        // print_r($absenteeArrray);
        date_default_timezone_set('Asia/Kolkata');
        $from = date('16-M-Y', strtotime('-1 month'));
        $to = date("15-M-Y");
        $fp = fopen("zip_files/" . strtoupper($section) . "_Absentee_$from" . "_to_" . "$to" . ".csv", 'w');
        foreach ($absenteeArrray as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);

        echo "<div class='alert alert-success alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >Data locked and submitted, don't forget to Export Table in Excel and Download All Screenshots </strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";

        //web push notification
        $end_point = 'https://api.webpushr.com/v1/notification/send/all';
        $http_header = array(
            "Content-Type: Application/Json",
            "webpushrKey: 05dfa90eda322294084342fdd1b8cafe",
            "webpushrAuthToken: 40358"
        );
        $sectionCap = strtoupper($section);
        $req_data = array(
            'title'         => "Absentee data submitted by $sectionCap section", //required
            'message'         => "View absentee data of $sectionCap by clicking on this notification", //required
            'target_url'    => "https://absentee.techtips.co.in/all_statements.php?section=$section", //required
            //following parameters are optional
            // 'name'		=> 'Test campaign',
            // 'icon'		=> 'https://cdn.webpushr.com/siteassets/wSxoND3TTb.png',
            // 'image'		=> 'https://cdn.webpushr.com/siteassets/aRB18p3VAZ.jpeg',
            //'auto_hide'	=> 1,
            //'expire_push'	=> '5m',
            //'send_at'		=> '2022-01-04 10:47 +5:30',
            //'action_buttons'=> array(	
            //array('title'=> 'Demo', 'url' => 'https://www.webpushr.com/demo'),
            //array('title'=> 'Rates', 'url' => 'https://www.webpushr.com/pricing')
            //)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_URL, $end_point);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        // echo $response;

    }
    ?>
    <div class="container my-3 mb-5">
        <h4>Leave statements of all employees <span><button id='copyBtn' onclick="copyTableLink()" class="btn btn-outline-primary btn-sm">Copy Link</button></span></h4>
        <script>
            function copyTableLink() {
                let tableLink = window.location.href;
                navigator.clipboard.writeText(tableLink);
                document.getElementById('copyBtn').innerText = 'Copied to clip';
                setTimeout(() => {
                    document.getElementById('copyBtn').innerText = 'Copy Link';
                }, 2000);
            }
        </script>
        <div class="my-3 table-responsive">
            <table id="table_id" class="table-bordered w-100 text-center">
                <tr>
                    <td colspan='11' class='text-center fw-bold'>
                        <h5></h5><?php echo strtoupper($section) ?> Section Khyberpass Depot</h5>
                    </td>
                </tr>
                <tr>
                    <td colspan='11' class='text-center fw-bold'>
                        <?php
                        date_default_timezone_set('Asia/Kolkata');
                        $from = date('16-M-Y', strtotime('-1 month'));
                        $to = date("15-M-Y");
                        echo "<h5></h5>Leave statements from $from to $to</h5>";
                        ?>

                    </td>
                </tr>
                <tr>
                    <td colspan='11'> .</td>
                </tr>
                <!--<thead>-->
                <tr>
                    <th>SN</th>
                    <th>Employee Number</th>
                    <th style='min-width:150px'>Employee Name</th>
                    <th>Type</th>
                    <th>Leave Type</th>
                    <th style='min-width:100px'>Leave From</th>
                    <th style='min-width:100px'>Leave Upto</th>
                    <th>Approved</th>
                    <th>No of Days</th>
                    <th>Approving Authority Name</th>
                    <th>View/Verify Data</th>
                </tr>
                <!--</thead>-->
                <tbody>
                    <?php
                    $absentee = file_get_contents("$section/absentee.json");
                    $absentee = json_decode($absentee, true);
                    $sn = 1;
                    //counting from employees.json
                    $employees = file_get_contents("$section/employees.json");
                    $employees = json_decode($employees, true);
                    $total_emp = count($employees);
                    $not_submitted = array();
                    $enableLock = 1;
                    for ($i = 0; $i < $total_emp; $i++) {
                        $emp_num = trim(explode("-", $employees[$i])[0]);
                        if (array_key_exists($emp_num, $absentee)) {
                            $emp_data = $absentee[$emp_num];
                            $emp_name = strtoupper($emp_data[0]);
                            $leave_data   = $emp_data[1];
                            // $file_path   = $emp_data[2];
                            $verification   = $emp_data[3];
                            $verified = '';
                            if ($verification == 1)
                                $verified = 'text-success';
                            else
                                $enableLock = 0;
                            $sub_dir  = $_SERVER['PHP_SELF'];
                            $sub_dir = str_replace("all_statements.php", "", $sub_dir);
                            $current_site = 'http://' . $_SERVER['SERVER_NAME'] . $sub_dir;
                            $file_path = $current_site . "view_screenshot.php?section=$section&view_emp=$emp_num";
                            $total_slots = count($leave_data);
                            //officer name
                            $approvers = file_get_contents("$section/approvers.json");
                            $approvers = json_decode($approvers, true);
                            $officerName = strtoupper($approvers['officerName']);
                            for ($j = 0; $j < $total_slots; $j++) {
                                $row = $leave_data[$j];
                                $days = strtotime($row[1]) - strtotime($row[0]);
                                $days = round($days / 86400) + 1;
                                $from = date("d-M-y", strtotime($row[0]));
                                $to = date("d-M-y", strtotime($row[1]));
                                $leave_type = $row[2];
                                echo "<tr class='$verified'>";
                                if ($j == 0)
                                    echo "<td rowspan='$total_slots'>$sn </td>
                                          <td rowspan='$total_slots'>$emp_num</td>
                                          <td rowspan='$total_slots'>$emp_name </td>
                                          <td rowspan='$total_slots'>REGULAR </td>";
                                echo "<td>$leave_type</td>
                                      <td>$from</td>
                                      <td>$to</td>
                                      <td>YES</td>
                                      <td>$days</td>";

                                if ($j == 0)
                                    echo "<td rowspan='$total_slots'>$officerName</td>
                                          <td rowspan='$total_slots'><a href='$file_path'>View/Verify</a></td>";
                                echo "</tr>";
                            }
                            if ($total_slots == 0) {
                                echo "<tr class='$verified'>
                                      <td>$sn</td>
                                      <td>$emp_num</td>
                                      <td>$emp_name</td>
                                      <td>REGULAR</td>
                                      <td>NIL</td>
                                      <td>NIL</td>
                                      <td>NIL</td>
                                      <td>NIL</td>
                                      <td>NIL</td>
                                      <td>$officerName</td>
                                      <td><a href='$file_path'>View/Verify</a></td>";
                                echo "</tr>";
                            }
                            $sn = $sn + 1;
                        } else {
                            array_push($not_submitted, $employees[$i]);
                        }
                    }
                    ?>
                </tbody>
            </table>
            <a href="export_statements.php?section=<?php echo $section ?>" class="mt-3 btn btn-primary">Export Table in Excel</a><br>
            <a href="zip_screenshots.php?section=<?php echo $section ?>" class="my-3 btn btn-primary">Download All Screenshots</a>
        </div>
        <?php
        $dataSubmitted = 0;
        if ($enableLock == 1 and count($not_submitted) == 0) {
            $lock = 0;
            $lockStatus = file_get_contents("lockStatus.json");
            $lockStatus = json_decode($lockStatus, true);
            if (array_key_exists($section, $lockStatus)) {
                $lock = $lockStatus[$section];
            }
            if ($lock == 1) {
                $inchargeRemark = 'NA';
                $remarks = file_get_contents("remarks.json");
                $remarks = json_decode($remarks, true);
                if (array_key_exists($section, $remarks)) {
                    $inchargeRemark = $remarks[$section];
                }
                $inchargeName = $approvers['inchargeName'];
                $inchargeEmpNo = $approvers['inchargeEmpNo'];
                echo "<p><b>Remark: </b>$inchargeRemark</p>
                        <div class='alert alert-info' role='alert'>
                            <strong >Data locked and submitted by section incharge " . strtoupper($inchargeName) . " ($inchargeEmpNo)  </strong>
                        </div>";
                $dataSubmitted = 1;
            }
        }
        if ($dataSubmitted == 0) echo "<p><b>Note: </b> Data not submitted to HR</p>";
        if ($enableLock == 0) echo "<p><b>Note: </b>Entries shown with black fonts needs verification</p>";
        if ($enableLock == 1 and count($not_submitted) == 0 and isset($_SESSION[$section . 'loggedin']) and $lock == 0) {
            $inchargeName = $approvers['inchargeName'];
            $inchargeEmpNo = $approvers['inchargeEmpNo'];
            echo "<form method='POST' action='all_statements.php?section=$section'>
                    <div class=' form-check'>
                        <input type='checkbox' class='form-check-input' id='checkbtn' onchange='enable_btn()'>
                        <label class='form-check-label' for='exampleCheck1'>
                            <p class='text-danger'>I have carefully matched the leave statements and ESS applied leaves of above employees with attendance register. </p>
                        </label>
                    </div>
                    <label for='inchargeRemark' class='form-label float-start'>Remark</label>
                    <textarea class='form-control mb-3' id='inchargeRemark' name='inchargeRemark' rows='3'>NA</textarea>
                    <button type='submit' id='submitbtn' disabled name='lock' value='1' class='btn btn-danger' onclick=\"return confirm('After locking you will not be able to edit data, are you sure to lock and submit ?')\">Lock & Submit Data</button>
                </form> <br>
                <script>
                    function enable_btn() {
                        var checked = document.getElementById('checkbtn').checked;
                        if (checked == true)
                            document.getElementById('submitbtn').disabled = false;
                        else
                            document.getElementById('submitbtn').disabled = true;
                    }
                </script>
                <b>Section Incharge:</b> $inchargeName <br>
                <b>Employee Number:</b> $inchargeEmpNo 
                ";
        }

        ?>
        <!-- leave statement not submitted  -->
        <div class="my-3" id='notSubmitted'>
            <?php
            if (count($not_submitted) != 0) {
                echo "<h5 class='text-danger'>Leave statement not submitted by below employees <span><button class='btn btn-outline-primary btn-sm' onclick='copyDivToClipboard()'>Copy Message</button></span></h5>";
                for ($i = 1; $i <= count($not_submitted); $i++) {
                    $emp = $not_submitted[$i - 1];
                    echo "<b>$i.</b> $emp <br>";
                }
            }
            ?>
        </div>
        <script>
            function copyDivToClipboard() {
                var range = document.createRange();
                range.selectNode(document.getElementById("notSubmitted"));
                window.getSelection().removeAllRanges(); // clear current selection
                window.getSelection().addRange(range); // to select text
                document.execCommand("copy");
                window.getSelection().removeAllRanges(); // to deselect
                alert('Message copied to clip')
            }
        </script>
    </div>
    <div class="text-center bg-dark text-light py-3 mt-5" style="margin-bottom: -300px;">
        Developer: satishkushwahdigital@gmail.com
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' crossorigin='anonymous'></script>
</body>

</html>