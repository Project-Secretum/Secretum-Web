<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}
require_once "config.php";
$username = $password = "";
$username_err = $password_err = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST["username"]);
    }
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    if (empty($username_err) && empty($password_err)) {
        $sql = "SELECT username, password, track_ip, role FROM Users WHERE username = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $username, $hashed_password, $track_ip, $role);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["username"] = $username;
                            $_SESSION["track_ip"] = $track_ip;
                            $_SESSION["role"] = $role;
                            header("location: index.php");
                        }
                        else {
                            $password_err = "Invalid password!";
                        }
                    }
                }
                else {
                    $username_err = "Invalid username!";
                }
            }
            else {
                echo "Something went wrong. Please try again later.";
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
}
?>
<link rel="stylesheet" type="text/css" href="styles.css" />
    <div class="center">
        <h2>Login</h2>
        <p>Please enter your credentials.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

            <div <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <strong>Username</strong>
                <input type="text" name="username" value="<?php echo $username; ?>" placeholder="Username">
                <span style="color:red;"><?php echo $username_err; ?></span>
            </div>
            <div <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <strong>Password</strong>
                <input type="password" name="password" placeholder="Password">
                <span style="color:red;"><?php echo $password_err; ?></span>
            </div>

            <div class="form-group">
                <input type="submit" value="Login">
            </div>

        </form>
    </div>
