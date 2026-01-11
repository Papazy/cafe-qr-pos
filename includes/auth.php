<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user'])){
    header('Location: ' . BASE_URL . 'pages/admin/login.php');
    exit;
}