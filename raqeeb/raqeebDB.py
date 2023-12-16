import mysql.connector

# Establish a connection to MySQL
connection = mysql.connector.connect(
    host="127.0.0.1",
    user="root",
    password="maryaM@1999",
    database="raqeeb"
)

# Check if the connection was successful
if connection.is_connected():
    print("Connected to MySQL")

# Create a cursor
cursor = connection.cursor()

# ----------------------------------------------------
# Database name to create
db_name = "raqeeb"
try:
    # Create the database only if it doesn't already exist
    cursor.execute(f"CREATE DATABASE IF NOT EXISTS {db_name};")
    print(f"Database '{db_name}' created or already exists.")
except mysql.connector.Error as err:
    print("Error:", err)
# -----------------------------------------------------
# Switch to the Raqeeb database
cursor.execute("USE raqeeb")
try:
    # Create GUARDIAN table
    cursor.execute("""
        CREATE TABLE IF NOT EXISTS GUARDIAN (
            Id INT AUTO_INCREMENT PRIMARY KEY,
            Name VARCHAR(255) NOT NULL,
            Email VARCHAR(255) NOT NULL,
            Password VARCHAR(255) NOT NULL
        )
    """)

    # Create CHILD table with a foreign key to GUARDIAN
    cursor.execute("""
        CREATE TABLE IF NOT EXISTS CHILD (
            Id INT AUTO_INCREMENT PRIMARY KEY,
            Guardian_Id INT,
            Name VARCHAR(255) NOT NULL,
            Birth_date DATE,
            FOREIGN KEY (Guardian_Id) REFERENCES GUARDIAN(Id)
        )
    """)

    # Create VIDEO table
    cursor.execute("""
        CREATE TABLE IF NOT EXISTS VIDEO (
            Id VARCHAR(255) NOT NULL PRIMARY KEY,
            Uploaded_date DATE,
            Status VARCHAR(255),
            Title VARCHAR(255) NOT NULL,
            Description TEXT,
            URL VARCHAR(255) NOT NULL,
            downloaded TINYINT(1) DEFAULT '0',
            watched_time DATETIME NOT NULL

        )
    """)

    # Create WATCHES table to establish many-to-many relationship between CHILD and VIDEO
    cursor.execute("""
        CREATE TABLE IF NOT EXISTS WATCHES (
            Child_Id INT,
            Video_Id VARCHAR(255),
            Guardian_Id INT,
            email_sent TINYINT(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (Guardian_Id, Child_Id, Video_Id),
            FOREIGN KEY (Child_Id) REFERENCES CHILD(Id),
            FOREIGN KEY (Video_Id) REFERENCES VIDEO(Id),
            FOREIGN KEY (Guardian_Id) REFERENCES GUARDIAN(Id)
        )
    """)

    connection.commit()
    print("Tables created successfully or already exists.")
except mysql.connector.Error as err:
    print(f"Error creating tables: {err}")
# -----------------------------------------------------

# Close the cursor and connection
cursor.close()
connection.close()
