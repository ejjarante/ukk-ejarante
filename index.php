<?php
require "auth.php";

if (!empty($_SESSION['login'])) {
    header("Location: " . url_dashboard($_SESSION['role'] ?? '', ''));
} else {
    header("Location: login.php");
}
exit;
