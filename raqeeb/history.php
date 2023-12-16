<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header("Location: login.php");
  exit();
}
$Guardian_Id = $_SESSION['Guardian_Id'];
error_reporting(E_ALL);
ini_set('display_errors', '1');
// Connect to  database 
$host = "127.0.0.1";
$username = "root";
$password = "maryaM@1999";
$database = "raqeeb";

$connection = mysqli_connect($host, $username, $password, $database);

if (!$connection) {
  die("Connection failed: " . mysqli_connect_error());
}
// Retrieve watched videos for a specific guardian
$sql = "SELECT * FROM VIDEO INNER JOIN WATCHES 
        ON VIDEO.Id = WATCHES.Video_Id 
        WHERE WATCHES.Guardian_Id = $Guardian_Id 
        ORDER BY VIDEO.watched_time DESC";
// Execute the SQL query
$result = $connection->query($sql);
// Initialize an array to store the fetched videos
$videos = array();
// Check if there are any results
if ($result->num_rows > 0) {
  // Fetch each row and add it to the $videos array
  while ($row = $result->fetch_assoc()) {
    $videos[] = $row;
  }
}

//This array will be used to group watched videos by their respective dates
$videosByDate = array();

foreach ($videos as $video) {
  /*Extract Watched Date:Check if the 'watched_time' key exists in the current video. 
  If yes, convert the watched time to the "Y-m-d" date format.
  If not, set $watchedDate to an empty string*/
  $watchedDate = isset($video['watched_time']) ? date("Y-m-d", strtotime($video['watched_time'])) : '';

  /*Group Videos by Date: Add the current video to the $videosByDate array, using the $watchedDate as the key. 
  This step organizes videos into groups based on their watched dates.*/
  $videosByDate[$watchedDate][] = $video;
}
/*Sort Videos by Date in Descending Order:
  based on the keys (dates). T
  his ensures that the most recent dates come first */
arsort($videosByDate);

//Sort Videos Within Each Date based on their Watched Time 
foreach ($videosByDate as $date => $videos) {
  usort($videos, function ($a, $b) {
    $timeA = isset($a['watched_time']) ? strtotime($a['watched_time']) : 0;
    $timeB = isset($b['watched_time']) ? strtotime($b['watched_time']) : 0;
    return $timeB - $timeA;
  });
}


$connection->close();
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
          echo '<li><a style="color:rgb(114, 79, 79); font-weight: bolder;" href="history.php">HISTORY</a></li>';
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

  <div>
    <?php if (!empty($videosByDate)): ?>
      <?php foreach ($videosByDate as $date => $videos): ?>
        <?php $inappropriateVideos = array_filter($videos, function ($video) {
          return isset($video['Status']) && $video['Status'] === 'inappropriate';
        }); ?>

        <?php if (!empty($inappropriateVideos)): ?>
          <div class="history-day">
            <?php $formattedDate = date("l, F j, Y", strtotime($date)); ?>
            <div class="history-title">
              <?= ($date == date("Y-m-d")) ? 'Today - ' . $formattedDate : $formattedDate ?>
            </div>
            <table id="video-table">
              <tbody>
                <!-- Iterate through each inappropriate video and generate table rows -->
                <?php foreach ($inappropriateVideos as $video): ?>
                  <tr>
                    <td>
                      <?php
                      // Format and display the watched time
                      $watchedTime = isset($video['watched_time']) ? date("g:i A", strtotime($video['watched_time'])) : '';
                      echo $watchedTime;
                      ?>
                    </td>
                    <!-- Display YouTube Logo -->
                    <td style="width:10px;"><img width="20px" src="images/youtube.png" alt=""></td>
                    <td>
                      <!-- Display video title as a hyperlink -->
                      <?php if (isset($video['URL'])): ?>
                        <a href="<?= $video['URL'] ?>">
                          <?= $video['Title'] ?>
                        </a>
                      <?php else: ?>
                        <?= $video['Title'] ?>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No videos available.</p>
    <?php endif; ?>
  </div>

  <script>
    // Get the current date and format it
    var currentDate = new Date();
    var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    var dateStr = currentDate.toLocaleDateString(undefined, options);

    // Update the content of the "current-date" span
    document.getElementById("current-date").textContent = dateStr;
  </script>
</body>

</html>