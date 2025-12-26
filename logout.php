<?php
require './session.php';
session_destroy();
?>
<script>
    localStorage.clear();
    window.location.href = "login.php";
</script>
