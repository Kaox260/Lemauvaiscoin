<?php
require_once 'includes/db.php';

session_destroy();
header('Location: index.php');
exit;