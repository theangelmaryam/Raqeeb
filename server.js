//-------------------------------Import required libraries-------------------------------------
const { spawn } = require("child_process");
const express = require("express");
const app = express();
const port = 3000;
const { google } = require("googleapis");
const mysql = require("mysql2");

const http = require("http");
const WebSocket = require("ws");
const server = http.createServer(app);
const wss = new WebSocket.Server({ server });

var storedChildId;
var guardianId;
var Id;
var Video_Id;

//--------------------------Create a Connection to the MySQL Database---------------------------
const db = mysql.createConnection({
  host: "127.0.0.1",
  user: "root",
  password: "maryaM@1999",
  database: "raqeeb",
});

//---------------------------------Error Handling Function---------------------------------------
function handleError(message, error, res) {
  console.error(message, error);
  if (res) {
    res.status(500).json({ error: "Internal server error" });
  }
}

//----------------Connect to the Database and Handle any Connection Errors------------------------
db.connect((err) => {
  if (err) {
    handleError("Error connecting to MySQL", err);
    process.exit(1);
  } else {
    console.log("Connected to MySQL");
  }
});

//---------------------------------For JSON Request Bodies----------------------------------------
app.use(express.json());

//---------------Enable CORS (Cross-Origin Resource Sharing) for All Routes-----------------------
app.use((req, res, next) => {
  res.header("Access-Control-Allow-Origin", "*");
  res.header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS");
  res.header("Access-Control-Allow-Headers", "Content-Type, Authorization");
  next();
});

//-----------------Function to Extract the Video ID from a YouTube Video URL-----------------------
function extractVideoId(videoUrl) {
  try {
    const url = new URL(videoUrl);
    return url.searchParams.get("v");
  } catch (error) {
    return handleError("Error extracting video ID", error);
  }
}

//-----Function to Check if a Video Exists in the MySQL Database Based on Video ID and Status-------
function checkVideoStatus(videoId, status, storedChildId) {
  return new Promise((resolve, reject) => {
    //-------------------------------------Start of Mariam Code-------------------------------------------------------
    const Guardian_Id_Query = "SELECT Guardian_Id FROM CHILD WHERE Id = ?";
    db.query(Guardian_Id_Query, [storedChildId], (err, results) => {
      if (err) {
        handleError("Your Child ID is Not Valid", err);
        reject(err);
      } else {
        guardianId = results[0].Guardian_Id;
        //---------------------------------------End of Mariam Code----------------------------------------------

        const query = "SELECT * FROM video WHERE Id = ? AND status = ?";
        db.query(query, [videoId, status], (err, results) => {
          if (err) {
            handleError("Error checking video status", err);
            reject(err);
          } else {
            resolve(results.length > 0);
          }
        });
      }
    });
  });
}

//-----------------Function to Fetch Video Information from the YouTube Data API--------------------
async function fetchVideoInfoFromAPI(
  videoId,
  videoUrl,
  watchedTime,
  storedChildId
) {
  try {
    const apiKey = "AIzaSyAM5YruDkxkMBcNSNCywv1gt2_4mgxsM2E";
    const youtube = google.youtube({ version: "v3", auth: apiKey });
    const response = await youtube.videos.list({
      part: "snippet",
      id: videoId,
    });
    if (response.data.items.length > 0) {
      const video = response.data.items[0];
      const title = video.snippet.title;
      const description = video.snippet.description;
      const uploadDate = new Date(video.snippet.publishedAt);
      return {
        title,
        description,
        uploadDate,
        url: videoUrl,
        id: videoId,
        time: watchedTime,
        storedChildId: storedChildId,
      };
    }
  } catch (error) {
    return handleError("Error fetching video info", error);
  }
}
//----------------------Function to Save Video Information to the MySQL database----------------------
async function saveVideoInfoToMySQL(status, videoInfo, videoId) {
  try {
    const uploadDate = new Date(videoInfo.uploadDate);
    const watchedTime = new Date(videoInfo.time);

    const videoData = {
      status,
      URL: videoInfo.url,
      title: videoInfo.title,
      description: videoInfo.description,
      Uploaded_date: videoInfo.uploadDate,
      Id: videoInfo.id,
      watched_time: watchedTime,
    };
    const query = "INSERT INTO video SET ?";
    await new Promise((resolve, reject) => {
      db.query(query, videoData, (err) => {
        if (err) {
          handleError("Error saving video info", err);
          reject(err);
        } else {
          console.log("Video URL saved with status:", status);
          resolve(status);
        }
      });

      //-------------------------------------Start of Mariam Code-------------------------------------------------------
      //Video_Id=> real id from YouTube API (primary)
      const Video_Id_Query = "SELECT Id FROM Video WHERE Id = ?";
      db.query(Video_Id_Query, [videoId], (err, results) => {
        if (err) {
          handleError("Error finding Video_Id", err);
          reject(err);
        } else {
          Video_Id = results[0].Id;

          const InsertWatchesRel =
            "INSERT INTO WATCHES (Child_Id, Video_Id, Guardian_Id) VALUES (?, ?, ?)";
          db.query(
            InsertWatchesRel,
            [storedChildId, Video_Id, guardianId],
            (err, results) => {
              if (err) {
                handleError("Error saving watches info", err);
              } else {
                console.log("Watch information saved in watches table");
              }
            }
          );
        }
      });
      //---------------------------------------End of Mariam Code----------------------------------------------
    });
    return status;
  } catch (error) {
    handleError("Error saving video info", error);
    throw error;
  }
}

//--------------Define a POST Endpoint for Receiving Video Information and Then Managing it----------------------
app.post("/video", async (req, res) => {
  const videoUrl = req.body.videoUrl;
  const watchedTime = req.body.watchedTime;
  storedChildId = req.body.storedChildId;

  console.log("Received video URL:", videoUrl);
  const videoId = extractVideoId(videoUrl);

  const status = ["watched", "appropriate", "inappropriate"];
  for (const theStatus of status) {
    try {
      const existsWithStatus = await checkVideoStatus(
        videoId,
        theStatus,
        storedChildId
      );
      if (existsWithStatus) {
        console.log(
          "Video URL found with status: ",
          theStatus,
          ". It will not be stored again."
        );
        return res.status(200).json({ theStatus, videoUrl });
      }
    } catch (error) {
      return handleError("Error in video endpoint", error, res);
    }
  }
  try {
    const videoInfo = await fetchVideoInfoFromAPI(
      videoId,
      videoUrl,
      watchedTime,
      storedChildId
    );
    if (videoInfo) {
      const savedStatus = await saveVideoInfoToMySQL(
        "watched",
        videoInfo,
        videoId
      );
      res.status(200).json({ status: savedStatus });
    } else {
      console.error("Video information not found.");
      res.sendStatus(500);
    }
  } catch (error) {
    handleError("Error in video endpoint", error, res);
  }
});

//----------------------------------------Receive and Handle Model Result----------------------------------------
app.post("/model-result", async (req, res) => {
  const modelResult = await req.body.modelResult;
  const videoURL = await req.body.videoURL;

  console.log("Model result:", modelResult, " | Video URL:", videoURL);
  const dataToSend = { modelResult: modelResult, videoURL: videoURL };

  // Send data to the connected client (content file)
  wss.clients.forEach((client) => {
    client.send(JSON.stringify(dataToSend));
  });

  res.sendStatus(200);
});
//-----------------------------------------------Start the server------------------------------------------------
server.listen(port, () => {
  console.log(`Server running on port ${port}`);
});