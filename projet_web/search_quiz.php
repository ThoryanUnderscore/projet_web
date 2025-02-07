<?php
include 'server.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

displayQuizzes($search);
?>
