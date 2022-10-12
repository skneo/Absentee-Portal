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
    <title>Export Statements</title>
</head>

<body>
    <?php
    include 'header.php';
    ?>
    <div class="container my-3">
        <h4 class='fw-bold'>Leave statements of all employees</h4>
        <div class="my-3 container-fluid table-responsive">
            <table id="table_id" class="table-bordered w-100 text-center">
                <tr>
                    <td colspan='11' class='text-center fw-bold'>
                        <h5><?php echo strtoupper($section) ?> Section Khyberpass Depot</h5>
                    </td>
                </tr>
                <tr>
                    <td colspan='11' class='text-center fw-bold'>
                        <?php
                        date_default_timezone_set('Asia/Kolkata');
                        $from = date('16-M-Y', strtotime('-1 month'));
                        $to = date("15-M-Y");
                        echo "<h5>Leave statements from $from to $to</h5>";
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
                    <th>ESS Screenshot</th>
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
                            $sub_dir  = $_SERVER['PHP_SELF'];
                            $sub_dir = str_replace("export_statements.php", "", $sub_dir);
                            $current_site = 'http://' . $_SERVER['SERVER_NAME'] . $sub_dir;
                            $file_path = $current_site . "view_screenshot.php?section=$section&view_emp=" . $emp_num;
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

                                echo "<tr class='$verified'>
                                        <td>$sn </td>
                                        <td>$emp_num</td>
                                        <td>$emp_name </td>
                                        <td>REGULAR</td>
                                        <td>$leave_type</td>
                                        <td>$from</td>
                                        <td>$to</td>
                                        <td>YES</td>
                                        <td>$days</td>
                                        <td>$officerName</td>
                                        <td><a href='$file_path'>View</a></td>
                                    </tr>";
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
                                      <td><a href='$file_path'>View</a></td>
                                      </tr>";
                            }
                            $sn = $sn + 1;
                        }
                    }
                    ?>
                </tbody>
            </table>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="jquery.table2excel.min.js"></script>
            <button type="button" class="btn btn-primary my-3" id='tableexport'>Export Table in Excel</button>
            <script>
                $('#tableexport').click(function() {
                    $("#table_id").table2excel({
                        // filename: "absentee.xls"
                        <?php
                        date_default_timezone_set('Asia/Kolkata');
                        $from = date('16-M-Y', strtotime('-1 month'));
                        $to = date("15-M-Y");
                        echo "filename: \"" . strtoupper($section) . "_Absentee_" . $from . "_to_" . $to . ".xls\"";
                        ?>
                    });
                });
            </script>
        </div>

    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' crossorigin='anonymous'></script>
</body>

</html>