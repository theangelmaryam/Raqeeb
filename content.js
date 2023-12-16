var storedChildId;

//-------------------------------Browsing Monitoring Process-------------------------------------

function handleYouTubeVideoEvents() {
  //"yt-navigate-finish" event is specific to YouTube and is triggered when
  //navigation between YouTube video pages is complete.

  document.addEventListener("yt-navigate-finish", function () {
    unBlurVideo();
    const videoId = new URLSearchParams(window.location.search).get("v");
    if (videoId) {
      const videoURL = `https://www.youtube.com/watch?v=${videoId}`;
      var watchedTime = getWatchedTime();
      sendVideoInfoToServer(videoURL, watchedTime, storedChildId); //send Info to the server file
      console.log("Video URL: ", videoURL, "\nChild ID: ", storedChildId); //print for check
    }
  });
}

//-------------------------------------Time & Date Process-------------------------------------

function getWatchedTime() {
  // Get current date and time
  var currentDate = new Date();

  // options for formatting (date and time)
  var options = {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
    hour12: false,
  };

  // Get the user's locale or use a default locale
  var userLocale = navigator.language || "ar-SA";

  // Format the date and time based on the user's locale
  var formattedDateTime = currentDate.toLocaleString(userLocale, options);

  console.log(formattedDateTime); //print for check
  return formattedDateTime;
}

//------------------------------Current Page Checking Process--------------------------------
// Check if the current page is on YouTube
if (window.location.hostname === "www.youtube.com") {
  handleYouTubeVideoEvents();
}

//----------------------------------Video Info Process----------------------------------------

// Function to send the video info to serve.js file
function sendVideoInfoToServer(videoUrl, watchedTime, storedChildId) {
  // Check if storedChildId is not null before sending to the server
  if (storedChildId !== null) {
    // URL of the server where the video info should be sent.
    const serverUrl = "http://localhost:3000/video";

    // Make an HTTP request to the serverUrl
    fetch(serverUrl, {
      method: "POST", // Sending data to the server.
      headers: {
        "Content-Type": "application/json", // Specifying that the content type of the request body is JSON.
      },
      body: JSON.stringify({ videoUrl, watchedTime, storedChildId }), // Converts the video info into a JSON string
    })
      .then((response) => {
        if (response.ok) {
          return response.json(); // Parse the response JSON
        } else {
          console.error("Failed to send video URL to server.");
        }
      })
      .then((data) => {
        console.log("Video URL sent to server successfully.");
        console.log("Status: ", data.theStatus, "\nURL: ", data.videoUrl); // Access video status from the response

        const videoId = new URLSearchParams(window.location.search).get("v");
        const videoURL = `https://www.youtube.com/watch?v=${videoId}`;

        if (data.theStatus === "inappropriate" && data.videoUrl === videoURL) {
          blurFunction(); //blur the page
          console.log("Bad Video");
        }
      })
      .catch((error) => {
        console.error("Error sending video URL to server:", error);
      });
  } else {
    console.log("Child ID is null. Skipping server request.");
  }
}

//-----------------------------------Child ID Process-----------------------------------------
var isFirstTime = true;
function askForChildID() {
  if (isFirstTime) {
    var childIdInput = prompt("Please Enter Child ID:");
    if (childIdInput !== null) {
      var childId = parseInt(childIdInput);
      if (!isNaN(childId)) {
        console.log("Child ID Entered: " + childId); //print for check
        isFirstTime = false;
        localStorage.setItem("childId", childId); // Store the child ID in local storage
        return childId; // Return the entered child ID
      } else {
        alert("Invalid input. Please enter a numeric Child ID.");
      }
    } else {
      alert("Operation canceled by user.");
    }
  }
  return null; // Return null if the operation is canceled or input is invalid
}

// Check if the child ID is already stored in local storage
storedChildId = localStorage.getItem("childId");
if (!storedChildId) {
  storedChildId = askForChildID(); // If child ID is not present in local storage, ask for it
}

//-----------------------------Receive Model Result--------------------------------------
const socket = new WebSocket("ws://localhost:3000");
socket.onopen = () => {
  console.log("WebSocket connection established.");

  // Handle messages received from the server
  socket.onmessage = (event) => {
    const message = JSON.parse(event.data);
    console.log(
      "Model Result:",
      message.modelResult,
      "\nURL:",
      message.videoURL
    );

    const videoId = new URLSearchParams(window.location.search).get("v");
    const videoURL = `https://www.youtube.com/watch?v=${videoId}`;

    if (
      message.modelResult === "inappropriate" &&
      message.videoURL === videoURL
    ) {
      blurFunction(); //blur the page
      console.log("Bad Video");
    }
  };
};

//------------------------------------Blur Process-------------------------------------
socket.onclose = () => {
  console.log("WebSocket connection closed.");
};
// check the video
function blurFunction() {
  document.documentElement.style.filter = "blur(50px)";
}

function unBlurVideo() {
  document.documentElement.style.filter = "blur(0px)";
}