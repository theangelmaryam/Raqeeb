import mysql.connector
import os
import smtplib
import time
from email.message import EmailMessage
from smtplib import SMTPAuthenticationError, SMTPException
from socket import timeout
import ssl

# SMTP configuration
email_sender = 'fcitproject2023@gmail.com'
email_password = 'qghbsqakpidkdvsn'

# Create an SSL context for secure email communication
context = ssl.create_default_context()
context.check_hostname = False
context.verify_mode = ssl.CERT_NONE

# Function to send an email to a guardian with relevant information.


def send_email(guardian_name, guardian_email, child_name, url):
    try:
        email = EmailMessage()
        email['From'] = email_sender
        email['To'] = guardian_email
        email['Subject'] = f'RAQEEB - New Blocked Video'
        email_content = f'''Dear {guardian_name},
        This video has been blocked by RAQEEB for child {child_name}.
        Video link: {url}
        If you see any issues, please contact us.'''
        email.set_content(email_content)

        with smtplib.SMTP_SSL('smtp.gmail.com', 465, context=context) as smtp:
            smtp.login(email_sender, email_password)
            smtp.send_message(email)
            print(f"Email sent to {guardian_email}")
    except (SMTPAuthenticationError, SMTPException, ConnectionError, timeout) as e:
        print(e)

# Function to monitors the database for pending notifications and sends emails to guardians.


def monitor_column():
    while True:
        try:
            # Establish a new connection to the MySQL database inside the loop
            conn = mysql.connector.connect(
                host='localhost',
                user='root',
                password='maryaM@1999',
                database="raqeeb"
            )
            cursor = conn.cursor(buffered=True)

            # Retrieve all pending notifications with status 'neg' from the watches table
            cursor.execute("""
                SELECT watches.child_id, watches.video_id, child.name, video.url, guardian.email, guardian.name AS guardian_name
                FROM watches
                JOIN video ON watches.video_id = video.id
                JOIN child ON watches.child_id = child.id
                JOIN guardian ON child.Guardian_ID = guardian.id
                WHERE watches.email_sent = 0 AND video.status = 'inappropriate'
            """)

            notifications = cursor.fetchall()

            # Iterate over the notifications and send an email to each guardian
            for notification in notifications:
                try:
                    child_id, video_id, child_name, video_url, guardian_email, guardian_name = notification
                    send_email(guardian_name, guardian_email,
                               child_name, video_url)
                    # Update the email_sent flag
                    cursor.execute("UPDATE watches SET email_sent = 1 WHERE child_id = %s AND video_id = %s",
                                   (child_id, video_id))
                    conn.commit()

                except Exception as e:
                    print(f"Error handling notification: {e}")

            cursor.close()
            conn.close()

        except mysql.connector.Error as e:
            print(f"Error connecting to database: {e}")
        except Exception as e:
            print(f"An error occurred: {e}")

        time.sleep(1)


if __name__ == '__main__':
    while True:
        monitor_column()
