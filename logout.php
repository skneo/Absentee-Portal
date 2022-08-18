<?php
$section = $_GET['section'];
session_start();
session_destroy();
header("Location: fill_leaves.php?section=$section");
