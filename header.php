<nav class='navbar navbar-expand-lg navbar-dark bg-dark fixed-top'>
    <div class='container-fluid text-center'>
        <a class='navbar-brand active ' href='./index.php'>Absentee</a>
        <!-- <img src='images/logo.png' alt='BrandName' width='30' height='30'> -->
        <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarSupportedContent' aria-controls='navbarSupportedContent' aria-expanded='false' aria-label='Toggle navigation'>
            <span class='navbar-toggler-icon'></span>
        </button>
        <div class='collapse navbar-collapse' id='navbarSupportedContent'>
            <ul class='navbar-nav me-auto mb-2 mb-lg-0'>
                <li class='nav-item'>
                    <a class='nav-link active ' aria-current='page' href='index.php'>Home</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link active ' aria-current='page' href='fill_leaves.php?section=<?php echo $section ?>'>New Entry</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link active ' aria-current='page' href='all_statements.php?section=<?php echo $section ?>'>View All</a>
                </li>
                <?php
                if (isset($_SESSION['adminloggedin'])) {
                    echo "<li class='nav-item'>
                    <a class='nav-link active ' aria-current='page' href='edit_employees.php?section=$section'>Edit Employees</a>
                    </li>
                    <li class='nav-item'>
                    <a class='nav-link active ' aria-current='page' href='export_all_sections.php?section=$section'>Export All Sections</a>
                </li>";
                }
//                 else if (isset($_SESSION[$section . 'loggedin'])) {
//                     echo "<li class='nav-item'>
//                     <a class='nav-link active ' aria-current='page' href='edit_employees.php?section=$section'>Edit Employees</a>
//                     </li>";
//                 }
                ?>
                <li class='nav-item'>
                    <a class='nav-link active ' aria-current='page' href='help.php?section=<?php echo $section ?>'>Help</a>
                </li>
            </ul>
            <?php
            if (isset($_SESSION['adminloggedin'])) {
                // echo "<a href='unlock.php?section=$section' class='btn btn-primary me-3' >Lock/Unlock Data</a>";
                echo "<div class='btn-group '>
                        <button id='userMenu' type='button' class='btn btn-success dropdown-toggle mx-3' data-bs-toggle='dropdown' aria-expanded='false' value=''>
                        Admin Menu
                        </button>
                        <ul class='dropdown-menu dropdown-menu-lg-end'>
                        <li><a class='dropdown-item ' href='delete_statements.php?section=$section'>Delete Absentee Data</a></li>
                        <li><a class='dropdown-item ' href='change_approvers.php?section=$section'>Change Approvers</a></li>
                        <li><a href='unlock.php?section=$section' class='dropdown-item' >Lock/Unlock Data</a></li>
                        <li><a href='reset_password.php?section=$section' class='dropdown-item' >Reset others's Password</a></li>
                        <li><a href='all_screenshots.php?section=$section' class='dropdown-item'>Old Data</a></li>
                        <li><a class='dropdown-item ' href='add_delete_section.php?section=$section'>Add/Delete Section</a></li>
                        <li><a class='dropdown-item ' href='notifications.php?section=$section'>Enable Notifications</a></li>
                        <li><a class='dropdown-item ' href='logout.php?section=$section'>Logout Admin</a></li>
                        </ul>
                        </div>";
            }
            if (!isset($_SESSION[$section . 'loggedin'])) {
                echo "<a href='login.php?section=$section' class='btn btn-primary ' >Login</a>";
            } else {
                echo "<div class='btn-group '>
                        <button id='userMenu' type='button' class='btn btn-success dropdown-toggle ' data-bs-toggle='dropdown' aria-expanded='false' value=''>
                        Menu
                        </button>
                        <ul class='dropdown-menu dropdown-menu-lg-end'>
                        <li><a class='dropdown-item ' href='change_password.php?section=$section'>Change Password</a></li>
                        <li><a class='dropdown-item ' href='logout.php?section=$section'>Logout</a></li>
                        </ul>
                        </div>";
            }
            ?>
        </div>
    </div>
</nav>
<div class="text-center text-primary h4">
    Section: <?php echo strtoupper($section); ?>
</div>
<style>
    body {
        background-color: rgb(218, 225, 233);
        padding-top: 60px;
        margin-bottom: 150px;
    }

    @media only screen and (min-width: 960px) {
        .navbar .navbar-nav .nav-item .nav-link {
            padding: 0 0.5em;
        }

        .navbar .navbar-nav .nav-item:not(:last-child) .nav-link {
            border-right: 1px solid #f8efef;
        }
    }
</style>
