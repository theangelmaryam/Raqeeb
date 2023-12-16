<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $Guardian_Id = $_SESSION['Guardian_Id'];
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
</head>

<body>
    <header>
        <nav class="navbar">
            <a href="#"> <img id="logo" src="images/Raqeeb_Logo.jpg" alt="Logo"></a>
            <ul>
                <li><a href="index.php">HOME</a></li>
                <li><a href="plugin.php">PLUG-IN</a></li>
                <li><a style="color:rgb(114, 79, 79); font-weight: bolder;" href="gettingStarted.php">GETTING
                        STARTED</a></li>
                <?php
                // Check if the user is logged in
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                    // User is logged in, display child, plugin, history, and logout
                    echo '<li><a href="child.php">CHILD</a></li>';
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

    <div>
        <h1 style="margin-top: 160px;"><strong>&nbsp;&nbsp;Raqeeb System - Guardian Guide</strong></h1>
        <h2><strong>&nbsp;&nbsp;&nbsp;Welcome to Raqeeb!</strong></h2>
        <h3>&nbsp;&nbsp;&nbsp;&nbsp;Thank you for choosing Raqeeb to protect your children from inappropriate YouTube
            content. <br>&nbsp;&nbsp;&nbsp;&nbsp;This guide will walk you through the steps to set up and use the Raqeeb
            system effectively.</h3>
        <div>
            <div id="slider-container">
                <div class="slide">
                    <center>
                        <h2><b>Step 1: Sign Up</b></h2>
                    </center> <br>
                    <p>1. Navigate to the "SIGN UP" page.</p>
                    <p>2. Fill in the required information.</p>
                    <p>3. Click on Sign-Up button to create your guardian account.</p>

                </div>
                <div class="slide">
                    <center>
                        <h2><b>Step 2: Login</b></h2>
                    </center> <br>
                    <p>1. Once signed up, You will be redirected to the "LOGIN" page.</p>
                    <p>2. Enter your login credentials.</p>
                    <p>3. Click on the Login button to access your Raqeeb account.</p>
                </div>
                <div class="slide">
                    <center>
                        <h2><b>Step 3: Add Child</b></h2>
                    </center> <br>
                    <p>1. Once logged in, you will be redirect to the "CHILD" page.</p>
                    <p>2. Click on the "Add New Child" button.</p>
                    <p>3. Enter your child's information and click on tha "Add" button.</p>
                    <p>4. You will be redirected to the "CHILD" page; you will show your children's Names and IDs.</p>

                </div>
                <div class="slide">
                    <center>
                        <h2><b>Step 4: Download Plugin</b></h2>
                    </center> <br>
                    <p>1. Navigate to the "PLUG-IN" page.</p>
                    <p>2. Click on the link or scan the barcode to download the Raqeeb Plugin.</p>
                    <p>3. Install the plugin on your child's device.</p>
                    <p>4. When installing the plugin for the first time, please enter your child's ID when prompted</p>
                </div>
                <div class="slide">
                    <center>
                        <h2><b>Step 5: Receive Notification Emails</b></h2>
                    </center> <br>
                    <p>1. If an inappropriate video is detected, the screen will blur.</p>
                    <p>2. You will receive notification emails about the incident.</p>
                    <p>3. If you find a video that you believe is inappropriate, you can report it to the Raqeeb System
                        team.</p>
                    <p>4. Raqeeb team will review the video and remove it if it is deemed to be inappropriate</p>
                </div>
                <div class="slide">
                    <center>
                        <h2><b>Step 6: View History</b></h2>
                    </center> <br>
                    <p>1. Navigate to the "History" page.</p>
                    <p>2. Review a list of all inappropriate videos watched by your children.</p>
                </div>

                <div class="slider-dots"></div>
            </div>

            <div id="prev"><img class="slider-images" src="images/previous.png" alt=""></div>
            <div id="next"><img class="slider-images" src="images/next.png" alt=""></div>

        </div>
    </div>


    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        const totalSlides = slides.length;

        function showSlide(index) {
            if (index < 0) {
                currentSlide = totalSlides - 1;
            } else if (index >= totalSlides) {
                currentSlide = 0;
            } else {
                currentSlide = index;
            }

            for (let i = 0; i < totalSlides; i++) {
                slides[i].style.display = 'none';
            }

            slides[currentSlide].style.display = 'block';
            updateActiveDot();
        }

        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        function prevSlide() {
            showSlide(currentSlide - 1);
        }

        document.getElementById('next').addEventListener('click', nextSlide);
        document.getElementById('prev').addEventListener('click', prevSlide);

        // Show the first slide initially
        showSlide(currentSlide);
        // ----------------------------------------

        const dotContainer = document.querySelector('.slider-dots');

        function createDots() {
            for (let i = 0; i < totalSlides; i++) {
                const dot = document.createElement('span');
                dot.classList.add('dot');
                dotContainer.appendChild(dot);

                dot.addEventListener('click', () => {
                    showSlide(i);
                });
            }
        }

        function updateActiveDot() {
            const dots = document.querySelectorAll('.dot');
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
        }

        createDots();
        updateActiveDot();
    </script>
</body>

</html>