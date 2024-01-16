<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&family=Rubik+Mono+One&display=swap" rel="stylesheet">
    <script src="jquery-3.7.1.min.js"></script>
    <script src="script.js" type="text/javascript"></script>
    <title>Login</title>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h1>Twotdo-e</h1>
            <p>One-on-One Tutorial Service</p>
            <br>
            
            <form id="loginForm" action="login.php" method="post">
                <input type="text" id="username" name="username" placeholder="username"  required>

                <input type="password" id="password" name="password" placeholder="password"  required>
            <br>
            <br>
                <button type="submit">login</button>
            </form>
            <a href="registration.html"><button type="button" onclick="toggleForm()">register</button></a>
            
            <?php
            session_start();
            if (isset($_SESSION["failed"])) {
            ?>
                <div id="error">
                    <p style="font-family: 'Roboto', sans-serif; font-size: 8pt;">Incorrect user name / password. Please try again.</p>
                </div>
            <?php
                    session_destroy();
                } 
            ?>
            
        </div>
        <div class="image-container">
            <img src="images/Loginbg.png" alt="Image">
        </div>
    </div>

</body>
</html>
