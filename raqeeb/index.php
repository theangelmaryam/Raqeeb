<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $Guardian_Id = $_SESSION['Guardian_Id'];
}
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
                <li><a style="color:rgb(114, 79, 79); font-weight: bolder;" href="index.php">HOME</a></li>
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
                    echo '<li><a href="login.php">LOGIN</a></li>';
                }
                ?>
            </ul>
        </nav>
        <hr>
    </header>
    <div id="homeDiv">
        <div id="leftDiv">
            <center>
                <h1>A safe online environment for your child</h1>
            </center>
            <center>
                <h2>AI-powered Content Safety blocks inappropriate content, <br>
                    keeping kids safe online with Raqeeb's innovative <br> security.</h2>
            </center>
        </div>
        <div id="rightDiv">
            <center> <img id="home_image" src="images/home_image.jpeg" alt=""></center>
        </div>
    </div>
</body>

</html>