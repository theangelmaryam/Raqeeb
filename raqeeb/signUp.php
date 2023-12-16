<?php
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

// Insert Data Into MySQL
if (isset($_POST['submit'])) {
    // Required field validation
    $validationErrors = array();

    $Name = $_POST['Name'];
    $Email = $_POST['Email'];
    $Password = $_POST['Password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate the Name
    // if (!ctype_alpha($Name)) {
    if (!preg_match('/^[a-zA-Z ]+$/', $Name)) {
        $validationErrors[] = 'The Name should contain only letters.';
    }

    // Validate the Email format
    if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        $validationErrors[] = 'Invalid Email format.';
    }

    // Check if Email already exists in the database
    $checkQuery = "SELECT * FROM GUARDIAN WHERE Email = '$Email'";
    $result = $conn->query($checkQuery);
    if ($result->num_rows > 0) {
        $validationErrors[] = 'An account with this email already exists. Please log in.';
    }

    // Validate Password strength
    if (strlen($Password) < 8) {
        $validationErrors[] = 'Password should be at least 8 characters long.';
    }

    // if (!preg_match('/[A-Z]/', $Password)) {
    //     $validationErrors[] = 'Password should contain at least one uppercase letter.';
    // }

    // if (!preg_match('/[0-9]/', $Password)) {
    //     $validationErrors[] = 'Password should contain at least one number.';
    // }

    // if (!preg_match('/[^A-Za-z0-9]/', $Password)) {
    //     $validationErrors[] = 'Password should contain at least one special character.';
    // }


    if ($Password !== $confirm_password) {
        $validationErrors[] = 'Password and Confirm Password do not match. Please make sure they are the same.';
    }
    // If there are no validation errors, proceed with user registration
    if (empty($validationErrors)) {
        // SQL query to insert user data into the "GUARDIAN" table
        $sql = "INSERT INTO GUARDIAN (Name, Email, Password) VALUES ('$Name', '$Email', '$Password')";
        // Check if the query execution is successful
        if ($conn->query($sql) === TRUE) {
            // Display a success alert and redirect to the login page
            echo "<script>window.alert('Registration successful. Please log in.'); 
            window.location.href = 'login.php';</script>";
        } else {
            // Display an error message if there is a database error
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        // Display validation errors using a JavaScript alert
        echo "<script>window.alert('" . implode('\n', $validationErrors) . "');</script>";
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
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('button[type="submit"]').prop('disabled', true);

            // Function to validate Name
            function validateName() {
                var name = $('#Name').val();
                // var isNameValid = /^[a-zA-Z]+$/.test(name);
                var isNameValid = /^[a-zA-Z\s]+$/.test(name);
                if (isNameValid || name === '') {
                    $('#nameFeedback').text('');
                } else {
                    $('#nameFeedback').text('Please enter your full name (letters only)').css('color', 'red');
                }

                return isNameValid;
            }

            // Function to validate Email
            function validateEmail() {
                var email = $('#Email').val();
                var isEmailValid = validateEmailFormat(email);

                if (isEmailValid || email === '') {
                    $('#emailFeedback').text('');
                } else {
                    $('#emailFeedback').text('Please enter a valid email address').css('color', 'red');
                }

                return isEmailValid;
            }

            // Function to validate Password
            function validatePassword() {
                var password = $('#Password').val();
                var isPasswordValid = password.length >= 8;

                if (isPasswordValid || password === '') {
                    $('#passwordFeedback').text('');
                } else {
                    $('#passwordFeedback').text('Password should be at least 8 characters long').css('color', 'red');
                }

                return isPasswordValid;
            }

            // Function to validate Confirm Password
            function validateConfirmPassword() {
                var confirm_password = $('#confirm_password').val();
                var password = $('#Password').val();
                var doPasswordsMatch = confirm_password === password;

                if (doPasswordsMatch || confirm_password === '') {
                    $('#confirmPasswordFeedback').text('');
                } else {
                    $('#confirmPasswordFeedback').text('Passwords do not match').css('color', 'red');
                }

                return doPasswordsMatch;
            }

            // Function to show error messages for all fields
            function showAllErrorMessages() {
                validateName();
                validateEmail();
                validatePassword();
                validateConfirmPassword();
            }

            // Function to enable or disable the submit button based on validation
            function enableSubmitButton() {
                showAllErrorMessages();
                var isFormValid = validateName() && validateEmail() && validatePassword() && validateConfirmPassword();
                $('button[type="submit"]').prop('disabled', !isFormValid);
            }

            // Function to validate email format
            function validateEmailFormat(email) {
                var re = /\S+@\S+\.\S+/;
                return re.test(email);
            }

            // Event listeners for input fields
            $('#Name, #Email, #Password, #confirm_password').on('input focus', enableSubmitButton);
        });
    </script>
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
                    echo '<li><a style="color:rgb(114, 79, 79); font-weight: bolder;" href="signUp.php">SIGN UP</a></li>';
                    echo '<li><a href="login.php">LOGIN</a></li>';
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
        <form action="signUp.php" method="post">
            <div><input class="username" type="text" name="Name" id="Name" placeholder="Name"
                    title="Enter your full name (letters only)" required>
                <span class="inputMess" id="nameFeedback"></span>
            </div>
            <div><input class="username" type="email" name="Email" id="Email" placeholder="Email"
                    title="Enter a valid email address" required>
                <span class="inputMess" id="emailFeedback"></span>
            </div>
            <div><input class="password" type="password" name="Password" id="Password" placeholder="Password"
                    title="Password should be at least 8 characters long and contain at least one uppercase letter, one number, and one special character"
                    data-guide="Password should be at least 8 characters long, with an uppercase letter, a number, and a special character"
                    required>
                <span class="inputMess" id="passwordFeedback"></span>
            </div>
            <div><input class="password" type="password" name="confirm_password" id="confirm_password"
                    placeholder="Confirm Password" title="Re-enter your password for confirmation" required>
                <span class="inputMess" id="confirmPasswordFeedback"></span>
            </div>
            <div><button type="submit" name="submit">Sign-Up</button>
                <a class="gray" href="login.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Already have an
                    account?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Login</a>
            </div>
        </form>

    </div>
</body>

</html>