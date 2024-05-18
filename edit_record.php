<?php
$section = $_GET['section'];
session_start();
if (!isset($_SESSION[$section . 'loggedin'])) {
    header("Location: login.php?section=$section");
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Edit Record</title>
</head>

<body>
    <?php
    include 'header.php';
    ?>
    <div class='container my-3'>
        <?php
        if (isset($_GET['changeScreenshot'])) {
            $emp_num = $_GET['changeScreenshot'];
            echo "<a href='view_screenshot.php?section=$section&view_emp=$emp_num' class='btn btn-primary btn-sm mb-3'><- Back</a>
                 <h4>Edit Record</h4>";
            echo "<p>Enployee Number: $emp_num</p>
                    <form method='POST' action='view_screenshot.php?section=$section&emp_num=$emp_num' enctype=\"multipart/form-data\">
                        <div class='mb-3'>
                            <label for='newScreenshot' class='form-label float-start'>New ESS Screenshot <div class='form-text text-muted'> अगर आपने कोई छुट्टी नहीं ली फिर भी ESS का स्क्रीनशॉट अपलोड करें </div></label>
                            <input type='file' class='form-control' id='newScreenshot' name='newScreenshot' required>
                            <div class='form-text text-muted'>स्क्रीनशॉट Crop करके अपलोड करें जिसमें सिर्फ आपका नाम और अप्लाई की हुई छुट्टियाँ ही दिखें <a target='_blank' href='sample.jpg'>( सैंपल देखें ) </a></div>
                        </div>
                            <button type='submit' value='1' name='changeScreenshot' class='btn btn-primary ' onclick=\"return confirm('Sure to submit?')\">Submit</button>
                    </form>";
        }
        //edit table
        if (isset($_GET['editTable'])) {
            $emp_num = $_GET['editTable'];
            echo "<a href='view_screenshot.php?section=$section&view_emp=$emp_num' class='btn btn-primary btn-sm mb-3'><- Back</a>
                 <p>Enployee Number: $emp_num</p>";
        }
        ?>
        <div id='tableDiv' style="display:none">
            <b>Existing Table</b>
            <table id="table_id" class="table-bordered w-100 mb-3 text-center">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>Upto</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_GET['editTable'])) {
                        echo "<script>document.getElementById('tableDiv').style.display='block'</script>";
                        $absentee = file_get_contents("$section/absentee.json");
                        $absentee = json_decode($absentee, true);
                        $emp_data = $absentee[$emp_num];
                        $leave_data   = $emp_data[1];
                        $total_slots = count($leave_data);
                        for ($j = ($total_slots - 1); $j >= 0; $j--) {
                            $row = $leave_data[$j];
                            $from = date("d-M-y", strtotime($row[0]));
                            $to = date("d-M-y", strtotime($row[1]));
                            $leave_type = $row[2];
                            echo "<tr>
                                    <td>$from</td>
                                    <td>$to</td>
                                    <td>$leave_type</td>
                                </tr>";
                        }
                        if ($total_slots == 0)
                            echo "<tr>
                                    <td>NIL</td>
                                    <td>NIL</td>
                                    <td>NIL</td>
                                </tr>";
                    }
                    ?>
                </tbody>
            </table>
            <h4>Update Table</h4>
            <form method='POST' action='view_screenshot.php?section=<?php echo $section ?>' enctype="multipart/form-data">
                <div class='mb-3 table-responsive'>
                    <label for='emp_num' class='form-label float-start '>Leave Data <small class="form-text text-muted">( छुट्टी नहीं ली तो खाली छोड़ दें ) </small>
                    </label>
                    <input type='text' name='total_rows' id='total_rows' value='1' hidden>
                    <table class="table-light table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>From</th>
                                <th>To</th>
                                <th style='min-width:150px'>Type of leave</th>
                            </tr>
                        </thead>
                        <tbody id='tbody'>
                            <?php
                            if ($total_slots == 0) {
                                echo "<tr>
                                <td><input type='date' class='form-control' name='from_0'></td>
                                <td><input type='date' class='form-control' name='to_0'></td>
                                <td>
                                    <select class='form-select' name='leave_type_0'>
                                        <option>No Leave</option>
                                        <option>HALF CL</option>
                                        <option>CL</option>
                                        <option>LAP</option>
                                        <option>RH</option>
                                        <option>LHAP (Com.)</option>
                                        <option>LHAP (Half)</option>
                                        <option>PL</option>
                                        <option>ML</option>
                                        <option>CCL</option>
                                        <option>SCL</option>
                                        <option>IOD</option>
                                        <option>QRTL</option>
                                        <option>EOL</option>
                                        <option>LWP</option>
                                        <option>LND</option>
                                        <option>OTHER</option>
                                    </select>
                                </td>
                            </tr>
                            <script>
                                total_rows.value = 1;
                            </script>";
                            } else {
                                for ($j = 0; $j < $total_slots; $j++) {
                                    $row = $leave_data[$j];
                                    $from = $row[0];
                                    $to = $row[1];
                                    $leave_type = $row[2];
                                    echo "<tr>
                                    <td><input type='date' class='form-control' name='from_$j' value='$from'></td>
                                    <td><input type='date' class='form-control' name='to_$j' value='$from'></td>
                                <td>
                                    <select class='form-select' name='leave_type_$j' id='leave_type_$j'>
                                        <option>No Leave</option>
                                        <option>HALF CL</option>
                                        <option>CL</option>
                                        <option>LAP</option>
                                        <option>RH</option>
                                        <option>LHAP (Com.)</option>
                                        <option>LHAP (Half)</option>
                                        <option>PL</option>
                                        <option>ML</option>
                                        <option>CCL</option>
                                        <option>SCL</option>
                                        <option>IOD</option>
                                        <option>QRTL</option>
                                        <option>EOL</option>
                                        <option>LWP</option>
                                        <option>LND</option>
                                        <option>OTHER</option>
                                    </select>
                                </td>
                            </tr>
                            <script>leave_type_$j.value = '$leave_type';</script>
                            ";
                                }
                                echo "<script>
                            total_rows.value = $total_slots;
                            </script>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <buttton class="btn btn-info btn-sm" id='add_row'>Add Row</buttton>
                    <script>
                        var row = parseInt(total_rows.value);
                        $("#add_row").click(function() {
                            $("#tbody").append(`<tr>
                                <td><input type = 'date' class = 'form-control' name = 'from_${row}'></td>
                                <td><input type = 'date' class = 'form-control' name = 'to_${row}'></td>
                                <td>
                                    <select class='form-select' name='leave_type_${row}'>
                                        <option>No Leave</option>
                                        <option>HALF CL</option>
                                        <option>CL</option>
                                        <option>LAP</option>
                                        <option>RH</option>
                                        <option>LHAP (Com.)</option>
                                        <option>LHAP (Half)</option>
                                        <option>PL</option>
                                        <option>ML</option>
                                        <option>CCL</option>
                                        <option>SCL</option>
                                        <option>IOD</option>
                                        <option>QRTL</option>
                                        <option>EOL</option>
                                        <option>LWP</option>
                                        <option>LND</option>
                                        <option>OTHER</option>
                                    </select>
                                </td>
                                </tr>`);
                            row = row + 1;
                            $("#total_rows").val(row);
                        });
                    </script>
                </div>
                <button type='submit' class='btn btn-primary' name='editTable' value='<?php echo $emp_num ?>' onclick="return confirm('Old table will be replaced with new, sure to submit?')">Submit</button>
            </form>
        </div>
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' crossorigin='anonymous'></script>
</body>

</html>