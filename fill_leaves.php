<?php
session_start();
// if (!isset($_SESSION[$section . 'loggedin'])) {
//     header('Location: index.php');
// }
$section = $_GET['section'];
?>
<!doctype html>
<html lang='en'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <!-- Bootstrap CSS -->
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3' crossorigin='anonymous'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Fill Leave Statements - <?php echo ucfirst($section) ?></title>
</head>

<body>
    <?php
    include 'header.php';
    ?>
    <div class='alert alert-info alert-dismissible fade show py-2 mb-2' role='alert'>
        <strong>स्क्रीनशॉट Crop करके ही अपलोड करें <a target='_blank' href="sample.jpg">( सैंपल देखें )</a></strong>
        <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>
    <div class="container mb-3">
        <h4>Fill Leave Statement</h4>
        <form method='POST' action='view_screenshot.php?section=<?php echo $section ?>' enctype="multipart/form-data">
            <div class='mb-3 mt-3'>
                <label for='emp_name' class='form-label float-start'>Employee Name</label>
                <select class="form-select" name='emp_name' id='emp_num' required>
                    <option disabled selected value> -- Select your name -- </option>
                    <?php
                    $employees = file_get_contents("$section/employees.json");
                    $employees = json_decode($employees, true);
                    $absentee = file_get_contents("$section/absentee.json");
                    $absentee = json_decode($absentee, true);
                    for ($i = 0; $i < count($employees); $i++) {
                        $emp = $employees[$i];
                        $emp_num = trim(explode("-", $emp)[0]);
                        if (array_key_exists($emp_num, $absentee))
                            continue;
                        echo "<option>$emp</option>";
                    }
                    ?>
                </select>
            </div>
            <div class='mb-3 table-responsive'>
                <label for='emp_num' class='form-label float-start '>Leave Data <small class="form-text text-muted">( छुट्टी नहीं ली तो खाली छोड़ दें ) </small>
                </label>
                <table class="table-light table table-striped table-bordered w-100">
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>To</th>
                            <th style='min-width:150px'>Type of leave</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type='date' class='form-control' name='from_0'></td>
                            <td><input type='date' class='form-control' name='to_0'></td>
                            <td>
                                <select class='form-select' name='leave_type_0'>
                                    <option>NA</option>
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
                    </tbody>
                </table>
                <input type='text' name='total_rows' id='total_rows' value='1' hidden>
                <buttton class="btn btn-info btn-sm" id='add_row'>Add Row</buttton>
                <script>
                    var row = 1;
                    $("#add_row").click(function() {
                        $("tbody").append(`<tr>
                                <td><input type = 'date' class = 'form-control' name = 'from_${row}'></td>
                                <td><input type = 'date' class = 'form-control' name = 'to_${row}'></td>
                                <td>
                                    <select class='form-select' name='leave_type_${row}'>
                                        <option>NA</option>
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
            <div class='mb-3'>
                <label for='fileToUpload' class='form-label float-start'>ESS Screenshot <div class="form-text text-muted"> अगर आपने कोई छुट्टी नहीं ली फिर भी ESS का स्क्रीनशॉट अपलोड करें </div></label>
                <input type='file' class='form-control' id='fileToUpload' name='fileToUpload' required>
                <div class='form-text text-muted'>स्क्रीनशॉट Crop करके अपलोड करें जिसमें सिर्फ आपका नाम और अप्लाई की हुई छुट्टियाँ ही दिखें <a target='_blank' href='sample.jpg'>( सैंपल देखें ) </a></div>
            </div>
            <button type='submit' class='btn btn-primary ' onclick="return confirm('Sure to submit?')">Submit</button>
        </form>
    </div>
    <div class="text-center bg-dark text-light py-3 mt-5" style="margin-bottom: -300px;">
        Developer: satishkushwahdigital@gmail.com
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' crossorigin='anonymous'></script>
</body>

</html>