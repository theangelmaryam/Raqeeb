from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing import image
import numpy as np
from tensorflow.keras.applications.resnet50 import preprocess_input
import os
import shutil
import time
import requests
import mysql.connector
from keras.models import load_model


# Create a connection to the MySQL database
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="maryaM@1999",
    database="raqeeb"
)

# Create a cursor object to execute SQL queries
cursor = db.cursor()

modelResult = ""

# Open the HDF5 file
file_path = '/Applications/XAMPP/xamppfiles/htdocs/RaqeebSystem/weights/nasnet/frames2_epoch_396.hdf5'
modelnasnetframes = load_model(file_path)

# folder path
folder_path = '/Applications/XAMPP/xamppfiles/htdocs/RaqeebSystem/weights/movies'


def send_folder(data, vidURL):
    # server URL
    server_url = 'http://localhost:3000/model-result'

    # Send the modelResult to the server
    response = requests.post(
        server_url, json={'modelResult': data, 'videoURL': vidURL})

    # Check the response status
    if response.status_code == 200:
        print('Model result sent successfully to the server.')

        # Update the video status in the database
        video_id = vidURL.split('=')[1]

        if data == "appropriate":
            status = "appropriate"
        else:
            status = "inappropriate"
        try:
            # Execute the SQL query to update the video status
            cursor.execute(
                "UPDATE video SET status = %s WHERE Id = %s", (status, video_id))
            db.commit()
            print("Video status updated in the database.")
        except Exception as e:
            print(f"Failed to update video status in the database: {e}")
    else:
        print('Failed to send model result to the server.')

# Processes a subfolder containing frames of a video.


def process_folder(folder):
    subfolder_path = os.path.join(folder_path, folder)
    images_data = []
    very_bad_frames_count = 0
    bad_frames_count = 0
    good_frames_count = 0
    total_frames = 0

    print("Processing folder:", subfolder_path)

    for frame in os.listdir(subfolder_path):
        frame_path = os.path.join(subfolder_path, frame)

        try:
            # Load and preprocess the image
            img = image.load_img(frame_path, target_size=(224, 224))
            x = image.img_to_array(img)
            x = np.expand_dims(x, axis=0)
            x = preprocess_input(x)

            # Make predictions using the model
            pred_result = modelnasnetframes.predict(x, verbose=0)

            images_data.append(pred_result)
            total_frames += 1

            # Classify frames based on the prediction result
            if pred_result[0][0] > 0.9:
                print("Very bad", frame)
                very_bad_frames_count += 1
            elif pred_result[0][0] > 0.3:
                print("Bad", frame)
                bad_frames_count += 1
            else:
                good_frames_count += 1
        except Exception as e:
            print(f"Error found in image {frame_path}: {e}. Please delete it.")

        if total_frames > 5000:
            break

    acc_bef = (bad_frames_count + very_bad_frames_count) / total_frames

    vidURL = f"https://www.youtube.com/watch?v={folder}"

    if (bad_frames_count > 0 or very_bad_frames_count > 0):
        print("It is inappropriate video")
        modelResult = "inappropriate"
    else:
        print("It is appropriate video")
        modelResult = "appropriate"

    send_folder(modelResult, vidURL)

    print("Total frames:", total_frames)
    print("Good frames count:", good_frames_count)
    print("Bad frames count:", bad_frames_count)
    print("Very bad frames count:", very_bad_frames_count)
    print("Accuracy:", acc_bef)

    # remove the subfolder and its contents
    shutil.rmtree(subfolder_path, ignore_errors=True)
    print(f"Folder deleted successfully: {subfolder_path}")


try:
    while True:

        subfolders_exist = False
        for subfolder in os.scandir(folder_path):
            if subfolder.is_dir() and not subfolder.name.endswith(".part"):
                subfolders_exist = True
                process_folder(subfolder.name)

        # true , false
        if subfolders_exist == False:
            print("No subfolders found in the specified directory.")
            time.sleep(10)

except KeyboardInterrupt:
    pass
except Exception as e:
    print(f"Error: {e}")
