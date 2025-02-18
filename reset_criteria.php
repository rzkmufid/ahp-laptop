<?php
session_start();
unset($_SESSION['user_criteria']);
echo json_encode(["success" => true]);
?>
