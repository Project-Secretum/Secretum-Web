<?php
session_start();

// Check if the user is logged in, if then redirect him
if (isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == true) {
    header("location: index.php");
    exit;
}

   require_once "config.php";
   error_reporting(E_ALL); ini_set('display_errors', 1);
   $username = $email = $password = $confirm_password = "";
   $username_err = $password_err = $confirm_password_err = "";

   if ($_SERVER["REQUEST_METHOD"] == "POST") {

       if (empty(trim($_POST["username"]))) {
           $username_err = "Please enter a username.";
       } else {
           $sql = "SELECT id FROM Users WHERE username = ?";

           if ($stmt = mysqli_prepare($link, $sql)) {
               mysqli_stmt_bind_param($stmt, "s", $param_username);

               $param_username = trim($_POST["username"]);

               if (mysqli_stmt_execute($stmt)) {
                   mysqli_stmt_store_result($stmt);

                   if (mysqli_stmt_num_rows($stmt) == 1) {
                       $username_err = "This username is already taken.";
                   } else {
                       $username = trim($_POST["username"]);
                   }
               } else {
                   echo "Oops! Something went wrong. Please try again later.";
               }
               mysqli_stmt_close($stmt);
           }
       }

       if (empty(trim($_POST["password"]))) {
           $password_err = "Please enter a password.";
       } elseif (strlen(trim($_POST["password"])) < 6) {
           $password_err = "Password must have atleast 6 characters.";
       } else {
           $password = trim($_POST["password"]);
       }

       if (empty(trim($_POST["confirm_password"]))) {
           $confirm_password_err = "Please confirm password.";
       } else {
           $confirm_password = trim($_POST["confirm_password"]);
           if (empty($password_err) && ($password != $confirm_password)) {
               $confirm_password_err = "Password did not match.";
           }
       }

       $track_ip = $_POST["track_ip"];

       if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {

           $sql = "INSERT INTO Users (user_id, username, tag, password, track_ip) VALUES (?, ?, ?, ?, ?)";

           if ($stmt = mysqli_prepare($link, $sql)) {
               mysqli_stmt_bind_param($stmt, "isisi", $param_id, $param_username, $param_tag, $param_password, $param_track_ip);

               $param_id = substr(str_shuffle('0123456789'), 0, 8);
               $param_tag = substr(str_shuffle('0123456789'), 0, 4);
               $param_username = trim($_POST["username"]);
               $param_password = password_hash($password, PASSWORD_DEFAULT);
               $param_track_ip = $track_ip;

               if (mysqli_stmt_execute($stmt)) {
                 echo "<script type='text/javascript'>alert('You can now login.');</script>";
                 header("location: login.php");
               }
               else {
                   echo "somethig went wrong! : " . $param_id, $param_username, $param_tag, $param_password, $param_track_ip;
               }
           }
           mysqli_stmt_close($stmt);
       }
       mysqli_close($link);
   }
   ?>

   <link rel="stylesheet" type="text/css" href="styles.css" />
      <div class="center">
         <h1>Register</h1>
         <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

            <div class="<?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
               <strong>Username</strong>
               <input type="text" name="username" value="<?php echo $username; ?>">
               <span style="color:red;"><?php echo $username_err; ?></span>
            </div>

            <div class="<?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
               <strong>Password</strong>
               <input type="password" name="password" value="<?php echo $password; ?>">
               <span style="color:red;"><?php echo $password_err; ?></span>
            </div>

            <div class="<?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
               <strong>Confirm Password</strong>
               <input type="password" name="confirm_password" value="<?php echo $confirm_password; ?>">
               <span style="color:red;"><?php echo $confirm_password_err; ?></span>
            </div>

            <div>
               <strong>Notify if a new IP logs into my account
                 <br>
                 (requires us to tie Hashed and salted IPs to your account)</strong>
                 <br>
               <label><input type="checkbox" name="track_ip" value="1"> Yes</label>
               <input type="hidden" name="track_ip" value="0">
            </div>

            <div>
               <input type="submit" value="Submit">
            </div>
         </form>
      </div>
