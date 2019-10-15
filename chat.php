<?php
session_start();

if (!isset($_SESSION["loggedin"])) {
  header("location: index.php");
}

?>

<link rel="stylesheet" type="text/css" href="styles.css" />
<div class="center" align="center">
<h1>project secretum</h1>
<p>hey. we wont take your info and promise secure messaging, all the time.</p>
<a href="logout.php">logout</a>
<p>copyright (not rlly) 2019 - project secretum inc.</p>
</div>
