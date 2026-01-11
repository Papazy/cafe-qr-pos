<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

if(!isset($_SESSION['user'])){
    header('Location: ' . BASE_URL . '/pages/admin/login.php');
    exit;
}