<?php
session_start();
$section = $_GET['section'];

// if (!isset($_SESSION['loggedin'])) {
//     header('Location: index.php');
// }
?>
<!doctype html>
<html lang='en'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <!-- Bootstrap CSS -->
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3' crossorigin='anonymous'>
    <title>Subscribe to Notifications</title>
</head>

<body>
    <?php
    include 'header.php';

    ?>
    <div class='container my-3'>
        <h4>Enable Notifications</h4>
        <p>Wait for subscribe button to load</p>
        <p>Subscribe and enable notifications to get notification when a section locks and submit data</p>
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' crossorigin='anonymous'></script>
    <!-- start webpushr tracking code -->
    <script>
        (function(w, d, s, id) {
            if (typeof(w.webpushr) !== 'undefined') return;
            w.webpushr = w.webpushr || function() {
                (w.webpushr.q = w.webpushr.q || []).push(arguments)
            };
            var js, fjs = d.getElementsByTagName(s)[0];
            js = d.createElement(s);
            js.id = id;
            js.async = 1;
            js.src = 'https://cdn.webpushr.com/app.min.js';
            fjs.parentNode.appendChild(js);
        }(window, document, 'script', 'webpushr-jssdk'));
        webpushr('setup', {
            'key': 'BO1cmyevzFd0zZtMTX6vPdBWCQsOh0rIp7ppuImPY1-WzfDk6NOpZlq3r_iMizmudV5S0-pswSO6tV7VgtFRfJs'
        });
    </script>
    <!-- end webpushr tracking code -->
</body>

</html>