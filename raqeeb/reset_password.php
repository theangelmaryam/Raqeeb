<?php
$servername = "127.0.0.1";
$username = "root";
$password = "maryaM@1999";
$dbname = "raqeeb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['Email']) && isset($_POST['New_Password'])) {
        $Email = $_POST['Email'];
        $NewPassword = $_POST['New_Password'];

        // Update the password in the database based on the email
        $sql = "UPDATE GUARDIAN SET Password='$NewPassword' WHERE Email='$Email'";

        if ($conn->query($sql) === TRUE) {
            echo "<script>window.alert('Password updated successfully');
            
            window.location.href = 'login.php';
            </script>";

        } else {
            echo "Error updating password: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raqeeb</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <nav class="navbar">
            <a href="#"> <img id="logo" src="images/Raqeeb_Logo.jpg" alt="Logo"></a>
            <ul>
                <li><a href="index.php">HOME</a></li>
                <li><a href="plugin.php">PLUG-IN</a></li>
                <li><a href="gettingStarted.php">GETTING STARTED</a></li>
                <?php
                // Check if the user is logged in
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                    // User is logged in, display child, plugin, history, and logout
                    echo '<li><a href="child.php">CHILD</a></li>';
                    echo '<li><a href="history.php">HISTORY</a></li>';
                    echo '<li> <a href="logout.php"> <img id="logout" src="images/logout.png" alt="logout"></a></li>';
                } else {
                    // User is not logged in, display sign up and login
                    echo '<li><a href="signUp.php">SIGN UP</a></li>';
                    echo '<li><a style="color:rgb(114, 79, 79); font-weight: bolder;" href="login.php">LOGIN</a></li>';
                }
                ?>
            </ul>
        </nav>
        <hr>
    </header>
    <div class="form-left-side">
        <img height="600px" src="images/sign-up-bg.jpg" alt="">
    </div>
    <div class="form-right-side">
        <form action="reset_password.php" method="post">
            <div><input class="username" type="email" name="Email" placeholder="Email"></div>
            <div><input class="password" type="password" name="New_Password" placeholder="New Password"></div>
            <div><button type="submit">Reset Password</button></div>
        </form>
    </div>
</body>

</html>