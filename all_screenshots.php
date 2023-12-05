<?php
$section = $_GET['section'];
session_start();
if (!isset($_SESSION['adminloggedin'])) {
    header("Location: login.php?section=admin");
    exit;
}
$showAlert = false;
if (isset($_POST['delete'])) {
    $fileName = $_POST['delete'];
    $file = "zip_files/$fileName";
    if (file_exists($file)) {
        unlink($file);
        $showAlert = true;
        $alertClass = "alert-success";
        $alertMsg = "File deleted";
    }
}
?>
<!doctype html>
<html lang='en'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <!-- Bootstrap CSS -->
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0' crossorigin='anonymous'>
    <title>ESS Screenshots Zip</title>
</head>

<body>
    <?php
    include 'header.php';
    echo "<div class='alert alert-info py-2' role='alert'>
        <strong>Files older than 100 days will be deleted automatically</strong>
        </div>";
    if ($showAlert) {
        echo "<div class='alert $alertClass alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >$alertMsg</strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
    }
    ?>
    <h4 class="text-center"><a href="all_screenshots.php?section=<?php echo $section ?>">All Files</a> </h4>
    <div class="container my-3">
        <?php
        date_default_timezone_set('Asia/Kolkata');
        $sn = 1;
        if ($handle = opendir("zip_files")) {
            echo "<table id='table_id' class='table-light table table-striped table-bordered w-100'>
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>File Name</th>
                            <th>File Size</th>
                            <th style='min-width:150px'>Last Modified Date</th>
                            <th style='min-width:150px'>Actions</th>
                        </tr>
                    </thead>
                    <tbody>";

            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if ($file == "index.php")
                        continue;
                    $ctime = filectime("zip_files/$file");
                    $dateTime = date("Y-m-d H:i:s", $ctime);
                    $filedownload = rawurlencode($file);
                    $size = round(filesize("zip_files/" . $file) / (1024));
                    $emp_num = explode('.', $file)[0];
                    echo "<tr>
                                <td>$sn</td>
                                <td>$file</td>
                                <td>$size kb</td>
                                <td>$dateTime</td>
                                <td><a href=\"zip_files/$filedownload\" download class='btn btn-sm btn-primary'>Download</a> 
                                <div class='float-end'>
                                    <form method='post' class='' action='all_screenshots.php?section=$section'>
                                        <button onclick=\"return confirm('Sure to delete $file ?')\" type='submit' class='btn btn-sm btn-danger' name='delete' value=\"$file\">Delete</button>
                                    </form>
                                </div>    
                                </td>
                            </tr>";
                    $sn = $sn + 1;
                }
            }
            echo "</tbody>
                  </table>";
        }
        ?>
        <!-- for data table -->
        <script src="https://code.jquery.com/jquery-3.5.1.js"> </script>
        <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"> </script>
        <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
        <script>
            $(document).ready(function() {
                $('#table_id').DataTable({
                    "scrollX": true
                });
            });
        </script>
    </div>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js' integrity='sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8' crossorigin='anonymous'></script>
</body>

</html>