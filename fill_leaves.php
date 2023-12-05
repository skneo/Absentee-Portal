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
    <title>Fill Leave Statements - <?php echo strtoupper($section) ?></title>
</head>

<body>
    <?php
    include 'header.php';
    if (isset($_SESSION['adminloggedin'])) {
        $deletedFiles = 0;
        date_default_timezone_set('Asia/Kolkata');
        if ($handle = opendir("zip_files")) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if ($file == "index.php")
                        continue;
                    $ctime = filectime("zip_files/$file");
                    $fileDate = date("Y-m-d", $ctime);
                    $currentDate = date("Y-m-d");
                    $fileDeleteDate = date('Y-m-d', strtotime($fileDate . ' + 100 days'));
                    if ($fileDeleteDate < $currentDate) {
                        $filePath = "zip_files/$file";
                        unlink($filePath);
                        $deletedFiles = $deletedFiles + 1;
                    }
                }
            }
        }
        if ($deletedFiles)
            echo "<div class='alert alert-success alert-dismissible fade show py-2 mb-2' role='alert'>
                <strong >$deletedFiles files older than 100 days deleted from Old Data</strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
    }
    ?>
    <div id='jsalert' class='alert alert-success py-2' style="display: none;" role='alert'>
        <strong>Leave statement submitted by all employees</strong>
    </div>
    <div class='alert alert-info py-2' role='alert'>
        <strong>स्क्रीनशॉट Crop करके ही अपलोड करें <a target='_blank' href="sample.jpg">( सैंपल देखें )</a></strong>
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
                    $not_submitted = array();
                    for ($i = 0; $i < count($employees); $i++) {
                        $emp = $employees[$i];
                        $emp_num = trim(explode("-", $emp)[0]);
                        if (array_key_exists($emp_num, $absentee)) {
                            continue;
                        }
                        array_push($not_submitted, $employees[$i]);
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
                <label for='fileToUpload' class='form-label float-start'>ESS Screenshot (size less than 1 Mb) <div class="form-text text-muted"> अगर आपने कोई छुट्टी नहीं ली फिर भी ESS का स्क्रीनशॉट अपलोड करें </div></label>
                <input type='file' class='form-control' id='fileToUpload' name='fileToUpload' required onchange="validateFile()">
                <div class='text-danger' id='fileErrror' style="display:none ;">File size is greater than 1 Mb</div>
                <div class='form-text text-muted'>स्क्रीनशॉट Crop करके अपलोड करें जिसमें सिर्फ आपका नाम और अप्लाई की हुई छुट्टियाँ ही दिखें <a target='_blank' href='sample.jpg'>( सैंपल देखें ) </a></div>
            </div>
            <center>
                <button type='submit' class='btn btn-primary px-5' id='submitBtn' onclick="showLoader(); return confirm('Sure to submit?') " disabled=true>Submit</button>
            </center>
        </form>
        <div class="d-flex justify-content-center my-3 d-none" id="pageLoader">
            <div class="spinner-border" role="status">
                <span class="sr-only"></span>
            </div>
        </div>

        <!-- leave statement not submitted  -->
        <div class="my-5" id='notSubmitted'>
            <?php
            if (count($not_submitted) != 0) {
                echo " <hr>";
                echo "<h5 class='text-danger'>Leave statement not submitted by below employees <span><button class='btn btn-outline-primary btn-sm' onclick='copyDivToClipboard()'>Copy Message</button></span></h5>";
                for ($i = 1; $i <= count($not_submitted); $i++) {
                    $emp = $not_submitted[$i - 1];
                    echo "<b>$i.</b> $emp <br>";
                }
            } else {
                echo "<script>
                        document.getElementById('jsalert').style.display = 'block';
                    </script>";
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
        <script>
            function validateFile() {
                let fileSize = document.getElementById('fileToUpload').files[0].size / (1024);
                if (fileSize < 1024) {
                    document.getElementById("submitBtn").disabled = false;
                    document.getElementById("fileErrror").style.display = "none";
                } else {
                    document.getElementById("submitBtn").disabled = true;
                    document.getElementById("fileErrror").style.display = "block";
                }
            }

            function showLoader() {
                document.getElementById('pageLoader').classList.remove('d-none');
            }
        </script>
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' crossorigin='anonymous'></script>
    <?php
    if (isset($_SESSION['adminloggedin'])) {
        echo "<script>(function(w,d, s, id) {if(typeof(w.webpushr)!=='undefined') return;w.webpushr=w.webpushr||function(){(w.webpushr.q=w.webpushr.q||[]).push(arguments)};var js, fjs = d.getElementsByTagName(s)[0];js = d.createElement(s); js.id = id;js.async=1;js.src = \"https://cdn.webpushr.com/app.min.\";fjs.parentNode.appendChild(js);}(window,document, 'script', 'webpushr-jssdk'));webpushr('setup',{'key':'BO1cmyevzFd0zZtMTX6vPdBWCQsOh0rIp7ppuImPY1-WzfDk6NOpZlq3r_iMizmudV5S0-pswSO6tV7VgtFRfJs' });</script>";
    }
    ?>
</body>

</html>