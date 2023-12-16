import mysql.connector
import yt_dlp
import os
import time

# Database configuration
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': 'maryaM@1999',
    'database': 'raqeeb',
    'port': 3306,
}

# Directory to save downloaded videos
download_dir = "/Applications/XAMPP/xamppfiles/htdocs/RaqeebSystem/weights/movies"

# True, it tells the function not to show an error if the directory already exists
os.makedirs(download_dir, exist_ok=True)

# function is responsible for downloading a video from a specified URL and saving it to a local directory.


def download_video(video_url, video_id):
    try:
        # Python dictionary
        ydl_opts = {
            # download the best quality available for the video.
            'format': 'best',

            # to specify the output template for the downloaded file
            # to join one or more path components
            'outtmpl': os.path.join(download_dir, f"{video_id}.mp4"),
        }

        # This class is designed to handle YouTube video downloads and allow to customize various aspects of the download process
        with yt_dlp.YoutubeDL(ydl_opts) as ydl:
            ydl.download(video_url)

        mark_as_downloaded(video_id)
        print(f"Downloaded video {video_id}")

    except Exception as e:
        print(f"Error downloading video {video_id}: {str(e)}")


def mark_as_downloaded(video_id):
    try:
        connection = mysql.connector.connect(**db_config)
        cursor = connection.cursor()  # to execute SQL queries
        update_query = "UPDATE video SET downloaded = 1 WHERE Id = %s"
        cursor.execute(update_query, (video_id,))
        connection.commit()  # the changes become permanent in the database
        cursor.close()
        connection.close()
        print(f"Marked video {video_id} as downloaded")
    except Exception as e:
        print(f"Error marking video {video_id} as downloaded: {str(e)}")


def listen_to_watched_videos():
    try:
        connection = mysql.connector.connect(**db_config)
        cursor = connection.cursor()
        select_query = "SELECT URL, Id FROM video WHERE downloaded = 0"
        cursor.execute(select_query)
        # retrieves all the rows from the result set of the previously executed query.
        watched_videos = cursor.fetchall()
        cursor.close()
        connection.close()

        # checks if the variable watched_videos is empty
        if not watched_videos:
            print("No new videos found. Waiting before checking again.")
            time.sleep(10)  # Sleep for 10 seconds before checking again
            return  # exit the function and return to the caller

        for video_url, video_id in watched_videos:
            download_video(video_url, video_id)
    except mysql.connector.Error as err:
        if err.errno == mysql.connector.errorcode.CR_SERVER_LOST or err.errno == mysql.connector.errorcode.CR_SERVER_GONE_ERROR:
            print("MySQL server connection lost. Waiting before the next attempt.")
            time.sleep(10)  # Wait for 10 seconds before retrying
        else:
            print(f"Error retrieving videos from the database: {str(err)}")


if __name__ == "__main__":
    while True:
        try:
            listen_to_watched_videos()
        except KeyboardInterrupt:
            break
