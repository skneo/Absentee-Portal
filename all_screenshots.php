<?php
$section = $_GET['section'];
session_start();
if (!isset($_SESSION['adminloggedin'])) {
    header("Location: index.php");
}
$showAlert = false;
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
//     $target_dir = "$section/uploads/";
//     $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
//     $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
//     //check file type
//     if ($file_type == 'htm' or $file_type == 'html' or $file_type == 'php' or $file_type == 'asp' or $file_type == 'aspx' or $file_type == 'jsp' or $file_type == 'htaccess') {
//         $showAlert = true;
//         $alertMsg =  "Sorry, this file type not allowed, upload it by making zip file.";
//         $alertClass = "alert-danger";
//     }
//     // Check if file already exists
//     else if (file_exists($target_file)) {
//         $showAlert = true;
//         $alertMsg =  "file of this name already exists, please change file name then try to upload";
//         $alertClass = "alert-danger";
//     } else {
//         if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
//             $filename = htmlspecialchars(basename($_FILES["fileToUpload"]["name"]));
//             $showAlert = true;
//             $alertMsg =  "The file $filename has been uploaded";
//             $alertClass = "alert-success";
//         } else {
//             $showAlert = true;
//             $alertMsg =  "Sorry, there was an error uploading your file.";
//             $alertClass = "alert-danger";
//         }
//     }
// }
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
    if ($showAlert) {
        echo "<div class='alert $alertClass alert-dismissible fade show py-2 mb-0' role='alert'>
                <strong >$alertMsg</strong>
                <button type='button' class='btn-close pb-2' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
    }
    ?>
    <!--<center>-->
    <!--    <div class="container my-3 ">-->
    <!--        <form action="all_screenshots.php?section=<?php echo $section ?>" method="post" enctype="multipart/form-data">-->
    <!--            <h4>Select file to upload</h4>-->
    <!--            <input class="form-control my-3" style="width: 300px;" type="file" name="fileToUpload" id="fileToUpload">-->
    <!--            <input class="btn btn-primary" onclick="loader()" type="submit" style="width: 300px;" value="Upload File" name="submit">-->
    <!--        </form>-->
    <!--        <div class="d-flex justify-content-center my-3 d-none" id="pageLoader">-->
    <!--            <div class="spinner-border" role="status">-->
    <!--                <span class="sr-only"></span>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</center>-->
    <!--<hr>-->
    <!--<script>-->
    <!--    var loader = function() {-->
    <!--        document.getElementById('pageLoader').classList.remove('d-none');-->
    <!--    }-->
    <!--</script>-->
    <h4 class="text-center"><a href="all_screenshots.php?section=<?php echo $section ?>">All Files</a> </h4>
    <div class="container my-3">
        <?php
        date_default_timezone_set('Asia/Kolkata');
        $sn = 1;
        // if (!is_dir("$section/uploads")) {
        //     mkdir("$section/uploads");
        //     file_put_contents("$section/uploads/index.php", "");
        // }
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
                    // $current_site = $_SERVER['SERVER_NAME'];
                    // $sub_dir  = $_SERVER['PHP_SELF'];
                    // $sub_dir = str_replace("all_screenshots.php", "", $sub_dir);
                    // $fileurl = "http://$current_site" . $sub_dir . "uploads/$filedownload";

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
                    // echo "<div class='my-3'> <b>$sn.</b> $file </div>    
                    //       <div class='my-3'> <b>File Size:</b> $size kb <b>Uploaded On:</b> $dateTime </div>
                    //       <div class='my-3 d-flex'>  
                    //           <div >

                    //             <a href=\"zip_files/$filedownload\" download class='btn btn-primary'>Download</a>
                    //             </div>
                    //             <div class='float-start'>
                    //                 <form method='post' class='mx-3' action='all_screenshots.php?section=$section'>
                    //                     <button onclick=\"return confirm('Sure to delete $file ?')\" type='submit' class='btn btn-danger' name='delete' value=\"$file\">Delete</button>
                    //                 </form> 
                    //             </div>
                    //       </div><hr>";
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