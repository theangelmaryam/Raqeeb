<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // User is not logged in, redirect to the login page or display an error message
    header("Location: login.php");
    exit();
}
$Guardian_Id = $_SESSION['Guardian_Id'];

error_reporting(E_ALL);
ini_set('display_errors', '1');

// Establish a connection to the MySQL database
$host = "127.0.0.1";
$username = "root";
$password = "maryaM@1999";
$database = "raqeeb";

// Create a connection
$connection = new mysqli($host, $username, $password, $database);

// Check if the connection was successful
if ($connection->connect_error) {
    echo ("Connection failed: " . $connection->connect_error);
}

// Get child information from the form
if (isset($_POST['submit'])) {
    // Rest of your code remains the same
    $Name = $_POST["Name"];
    $Birth_date = date("Y-m-d", strtotime($_POST["Birth_date"]));
    // Insert the child information into the CHILD table
    $sql = "INSERT INTO CHILD (Guardian_Id, Name, Birth_date)
            VALUES ('$Guardian_Id', '$Name', '$Birth_date')";
    // Check if the insertion was successful
    if ($connection->query($sql) === TRUE) {
        // Redirect to the child.php page
        header("Location: child.php");
        exit();
    } else {
        // Capture and store an error message
        $result_message = "Error: " . $connection->error;
    }
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
    <!-- Import Date parsing library to validate various date formats -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
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
                    $('#nameFeedback').text('Please enter your child name (letters only)').css('color', 'red');
                }
                return isNameValid;
            }

            function validateBirthDate() {
                var birthDate = $('#Birth_date').val();
                var isBirthDateValid = moment(birthDate, ['D-M-YYYY', 'D-MM-YYYY', 'DD-M-YYYY', 'YYYY-MM-DD', 'D/M/YYYY', 'D/MM/YYYY', 'DD/M/YYYY', 'MM/DD/YYYY', 'DD-MM-YYYY'], true).isValid();

                if (isBirthDateValid || birthDate === '') {
                    $('#birthDateFeedback').text('');
                } else {
                    $('#birthDateFeedback').text('Please enter a valid date').css('color', 'red');
                }
                return isBirthDateValid;
            }


            // Function to show error messages for all fields
            function showAllErrorMessages() {
                validateName();
                validateBirthDate()
            }

            // Function to enable or disable the submit button based on validation
            function enableSubmitButton() {
                showAllErrorMessages();
                var isFormValid = validateName() && validateBirthDate();
                $('button[type="submit"]').prop('disabled', !isFormValid);
            }

            // Function to validate email format
            function validateEmailFormat(email) {
                var re = /\S+@\S+\.\S+/;
                return re.test(email);
            }


            // Event listeners for input fields
            $('#Name, #Birth_date').on('input focus', enableSubmitButton);
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
                    echo '<li><a style="color:rgb(114, 79, 79); font-weight: bolder;" href="child.php">CHILD</a></li>';
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
    <center>
        <div>
            <div class="title">ADD NEW CHILD</div>
            <form action="addNewChild.php?" method="post">
                <input type="hidden" name="Guardian_Id" value="<?php echo $Guardian_Id; ?>">
                <div>
                    <input id="Name" style="color:gray; padding-left: 20px;" type="text" name="Name" placeholder="Name">
                    <span class="inputMess" id="nameFeedback"></span>
                </div>
                <div><input id="Birth_date" style="color:gray; padding-left: 20px;" type="text" name="Birth_date"
                        placeholder="Birth-Date">
                    <span class="inputMess" id="birthDateFeedback"></span>
                </div>
                <div><button style="color: #86644B; font-weight: bolder;" type="submit" name="submit">Add</button></div>
            </form>
        </div>
    </center>
    </div>
</body>

</html>