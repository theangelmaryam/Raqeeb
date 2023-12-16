<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
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

// Check if the user has submitted the login form
if (isset($_POST['Email']) && isset($_POST['Password'])) {
    $Email = $_POST['Email'];
    $Password = $_POST['Password'];

    // Check if the provided email exists in the database
    $sqlValidE = "SELECT * FROM GUARDIAN WHERE Email='$Email'";
    $resultValidE = $conn->query($sqlValidE);
    // If the email exists, proceed with further validation
    if ($resultValidE->num_rows > 0) {
        // Validate the provided email and password combination
        $sqlValidEandP = "SELECT * FROM GUARDIAN WHERE Email='$Email' AND Password='$Password'";
        $resultValidEandP = $conn->query($sqlValidEandP);
        // If the email and password combination is correct, proceed with login
        if ($resultValidEandP->num_rows > 0) {
            // Retrieve the unique Guardian_Id associated with the email
            $sql = "SELECT Id FROM GUARDIAN WHERE Email='$Email'";
            $result = $conn->query($sql);
            $Guardian_Id = $result->fetch_assoc()['Id'];
            // Set user session as logged in and store Guardian_Id
            $_SESSION['loggedin'] = true;
            $_SESSION['Guardian_Id'] = $Guardian_Id;
            // If the user's credentials are correct, redirect the user to the child page (child.php)
            header("Location: child.php");
            exit();
        } else {
            // If the user's credentials are incorrect, display an error message
            echo "<script>window.alert('Invalid password. Please try again, 
            if you forget your password you can reset it.');
            window.location.assign('login.php');
            </script>";
        }
    } else {
        // If the user's credentials are incorrect, display an error message
        echo "<script>window.alert(\"You don't have an account, please Sign Up\");
        window.location.assign('signUp.php');
        </script>";
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
        <form action="login.php" method="post">
            <div><input class="username" type="email" name="Email" placeholder="Email" required></div>
            <div><input class="password" type="password" name="Password" placeholder="Password" required></div>
            <div><button type="submit">Login</button></div>
            <a class="gray" style="margin-left:10px ;" href="reset_password.php">Forget Password?</a>
        </form>


    </div>
</body>

</html>