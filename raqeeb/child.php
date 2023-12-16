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
// Connect to database
$host = "127.0.0.1";
$username = "root";
$password = "maryaM@1999";
$database = "raqeeb";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch data from the database
$sql = "SELECT Id, Name FROM CHILD WHERE Guardian_Id = '$Guardian_Id'";
$result = mysqli_query($conn, $sql);
$childRecords = array();
while ($row = mysqli_fetch_assoc($result)) {
    // $childRecords[] = $row['Name'];
    $childRecords[] = array('Id' => $row['Id'], 'Name' => $row['Name']);
}

// Encode the data as JSON to make it accessible in JavaScript
$childRecordsJSON = json_encode($childRecords);

mysqli_close($conn);
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
        <div id="childRecordsContainer"></div>
    </center>
    <center>
        <div>
            <a href="addNewChild.php?Guardian_Id=<?php echo (int) $Guardian_Id; ?>">
                <button id="add-child">
                    Add New Child&nbsp;&nbsp;&nbsp;&nbsp; +
                </button>
            </a>
        </div>
    </center>

    <script>
        var colors = ["#D9D9D9", "white"];
        var currentColor = 1;

        // Fetch the JSON-encoded data from the PHP script
        var childRecords = <?php echo $childRecordsJSON; ?>;

        // Get the container element where you want to insert the child records
        var container = document.getElementById("childRecordsContainer");

        // Loop through the records and generate HTML dynamically
        childRecords.forEach(function (record) {
            var childDiv = document.createElement("div");
            childDiv.className = "child";

            // Change the background color of the child div
            toggleColor();
            childDiv.style.backgroundColor = colors[currentColor];
            // Access ID and Name properties from the record object
            var childId = record.Id;
            var childName = record.Name;
            childDiv.innerHTML = '<img style="margin-top:90px;" width="120px" src="images/child.png" alt=""><div style="margin-top:20px; font-size: 50px; font-weight:bold;">' + childName + '</div><br><div>The Plugin ID of ' + childName + ' is: ' + childId + '</div>';

            container.appendChild(childDiv);
        });

        function toggleColor() {
            currentColor = (currentColor + 1) % colors.length;
        }
    </script>

</body>

</html>