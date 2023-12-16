import os
import cv2
import time
import shutil

def mark_folder_as_in_progress(folder_path):

    #Marks a folder as in progress by appending ".part" to its name.
    in_progress_folder_path = folder_path + ".part"
        
    if not os.path.exists(in_progress_folder_path):
        os.rename(folder_path, in_progress_folder_path)
        return in_progress_folder_path
    else:
        print(f"Folder already in progress: {in_progress_folder_path}")
        shutil.rmtree(in_progress_folder_path)
        print(f"Folder: {in_progress_folder_path} , deleted and will reprocessing..")
        return mark_folder_as_in_progress(folder_path)
    
# Marks an in-progress folder as complete by removing the ".part" suffix.
def mark_folder_as_complete(in_progress_folder_path):
    folder_path = in_progress_folder_path.rstrip(".part")
    try:
        os.rename(in_progress_folder_path, folder_path)
    except PermissionError as e:
        print(f"Permission error: {e}. Skipping folder: {folder_path}")


movies_folder = '/Applications/XAMPP/xamppfiles/htdocs/RaqeebSystem/weights/movies'

try:
    while True:
        # Scan the contents of the movies_folder.
        # Use os.scandir to filter for .mp4 files
        for entry in os.scandir(movies_folder):
            if entry.is_file() and entry.name.endswith('.mp4'):
                video_path = entry.path
                # Extract the name of the video file without the ".mp4" extension.
                # Remove the last four characters of the entry.name
                output_folder = os.path.join(movies_folder, entry.name[:-4])
                
                # Create the output folder if it doesn't exist
                os.makedirs(output_folder, exist_ok=True)

                # Mark the folder as in-progress
                in_progress_folder_path = mark_folder_as_in_progress(output_folder)
                
                print("Processing Video:", entry.name)
                count = 0
                success = True
                # Capture the video
                cap = cv2.VideoCapture(video_path)# to capture video
                while success:
                    success, image = cap.read()
                    # Selects frames at regular intervals (every 30 frames) for saving as images.
                    if success and count % 30 == 0: 
                        # Generates the path for saving the current frame as an image.
                        frame_path = os.path.join(in_progress_folder_path, f'frame{count}.jpg')
                        # Saves the selected frame as an image file at the frame_path location.
                        cv2.imwrite(frame_path, image)
                    count += 1
                # Free up system resources and close the video file.
                cap.release()  

                print(f"Processed {count} frames for {entry.name}")

                # Mark the folder as complete
                mark_folder_as_complete(in_progress_folder_path)

                # Remove the video file after processing
                os.remove(video_path)
                print(f"Removed video file: {entry.name}")

        # Add a sleep to control the loop's iteration rate
        print("No new videos found. Waiting for new videos...")
        # Sleep before checking again
        time.sleep(10) 

except KeyboardInterrupt:
    print("Ragad developer stop running the code.")