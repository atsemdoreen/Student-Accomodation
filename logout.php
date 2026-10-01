<?php
require 'config.php';
$_SESSION = [];
session_destroy();
session_start();
flash('You are logged out.');
redirect('index.php');
