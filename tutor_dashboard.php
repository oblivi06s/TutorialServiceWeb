<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    // Redirect to the login page if not logged in
    header("Location: index.php");
    exit();
}

include ("sqlconnect.php");

$userID = $_SESSION["user_id"];

// Retrieve user information
$sqlUserInfo = "SELECT person_ID, first_name, last_name, date_of_birth, contact_number, address, email FROM (
                    SELECT * FROM emails
                    JOIN (
                        SELECT * FROM addresses
                        JOIN (
                            SELECT * FROM persons
                            JOIN contactnumbers USING(contact_number_ID)
                        ) AS f USING(address_ID)
                    ) AS s USING(email_ID)
                ) AS t WHERE person_ID = ?";
$stmtUserInfo = $conn->prepare($sqlUserInfo);
$stmtUserInfo->bind_param("i", $userID);
$stmtUserInfo->execute();
$resultUserInfo = $stmtUserInfo->get_result();
$userInfo = $resultUserInfo->fetch_assoc();

$sqlUsername = "SELECT * from userCredentials where person_ID = ?";
$stmt = $conn -> prepare($sqlUsername);
$stmt -> bind_param("i", $userInfo['person_ID']);
$stmt -> execute();
$resultStmt = $stmt -> get_result();
$userCreds = $resultStmt -> fetch_assoc();

// Retrieve tutoring session schedule
$sqlForTutorID = "SELECT * from tutors join persons using(person_ID) where person_ID = ?";
$FortutorID = $conn->prepare($sqlForTutorID);
$FortutorID->bind_param("i", $userID);
$FortutorID->execute();
$resultTutorID = $FortutorID->get_result();
$tutorID = $resultTutorID->fetch_all(MYSQLI_ASSOC);


$sqlTutoringSessions = "SELECT first_name, last_name, contact_number, course_name, location, date, duration_in_hours, time from (
                            SELECT * from contactNumbers join (
                                SELECT * from persons join (
                                    SELECT * from students join (
                                        SELECT * from courses join (
                                            SELECT * from conducts join sessions using(session_ID)
                                        ) as f using(course_ID)
                                    ) as s using(student_ID)
                                ) as t using(person_ID)
                            )as fr using(contact_number_ID)
                        ) as ff WHERE tutor_ID = ? ORDER BY date";
$stmtTutoringSessions = $conn->prepare($sqlTutoringSessions);
$stmtTutoringSessions->bind_param("i", $tutorID[0]['tutor_ID']);
$stmtTutoringSessions->execute();
$resultTutoringSessions = $stmtTutoringSessions->get_result();
$tutoringSessions = $resultTutoringSessions->fetch_all(MYSQLI_ASSOC);


$conn->close();
?>

<?php include("top.html"); ?>

    <!-- User Information Section -->
    <div class="dashboard-container">

        <div class="dashboard-section" id="section0" onclick="showPopup('userInfo')">
            <div class="circles" style="background-color: #73bdbd;"></div>
            <div class="tiles"><h3>User Information</h3></div>
            <div class="descriptions"><p>View and manage tutor information.</p></div>

            <div id="userInfo" class="popup">
                <div class="popup-content">
                <h2>Edit Profile</h2>
                    <form action="update_user_info.php" method="post">
                        <!-- Input fields with placeholders -->
                        <input type="hidden" name="userType" value="tutor">

                        <label for="editFirstName">First Name:</label>
                        <input type="text" id="editFirstName" name="editFirstName" value="<?php echo $userInfo['first_name']; ?>">
                        <br>
                        <label for="editLastName">Last Name:</label>
                        <input type="text" id="editLastName" name="editLastName" value="<?php echo $userInfo['last_name']; ?>">
                        <br>
                        <label for="editDateOfBirth">Date of Birth:</label>
                        <input type="date" id="editDateOfBirth" name="editDateOfBirth" value="<?php echo $userInfo['date_of_birth']; ?>">
                        <br>
                        <label for="editContact">Contact Number:</label>
                        <input type="text" id="editContact" name="editContact" value="<?php echo $userInfo['contact_number']; ?>">
                        <br>
                        <label for="editAddress">Address:</label>
                        <input type="text" id="editAddress" name="editAddress" value="<?php echo $userInfo['address']; ?>">
                        <br>
                        <label for="editEmail">Email:</label>
                        <input type="text" id="editEmail" name="editEmail" value="<?php echo $userInfo['email']; ?>">
                        <br>
                        <label for="editUsername">Username:</label>
                        <input type="text" id="editUsername" name="editUsername" value="<?php echo isset($userCreds['username']) ? $userCreds['username'] : ''; ?>" required>

                        <br>
                        <label for="editPassword">Password:</label>
                        <input type="password" id="editPassword" name="editPassword" placeholder="********" required>
                        <br>

                        
                        <div class="edit-buttons">
                            <button type="button" onclick="closePopup('userInfo')">Cancel</button>
                            <button type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="dashboard-section" id="section1" onclick="showPopup('tutorialSessionsPopup')">

            <div class="circles" style="background-color: #cec97b;"></div>
            <div class="tiles"><h3>Tutoring Session Schedule</h3></div>
            <div class="descriptions"><p>View and manage tutorial sessions.</p></div>

            <div id="tutorialSessionsPopup" class="popup">
                <div class="popup-content">
                <h2>"Tutorial Sessions</h2>
                    <?php 
                        if (empty($tutoringSessions)) {
                        ?>
                            <p>No tutoring sessions scheduled.</p>
                        <?php
                        } else {
                        ?>
                            <table border="1">
                                <thead>
                                    <tr>
                                        <th>Student's First Name</th>
                                        <th>Student's Last Name</th>
                                        <th>Student's Contact Number</th>
                                        <th>Course</th>
                                        <th>Location</th>
                                        <th>Date</th>
                                        <th>Duration (in hours)</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tutoringSessions as $session): ?>
                                        <tr>
                                            <td><?php echo $session['first_name']; ?></td>
                                            <td><?php echo $session['last_name']; ?></td>
                                            <td><?php echo $session['contact_number']; ?></td>
                                            <td><?php echo $session['course_name']; ?></td>
                                            <td><?php echo $session['location']; ?></td>
                                            <td><?php echo $session['date']; ?></td>
                                            <td><?php echo $session['duration_in_hours']; ?></td>
                                            <td><?php echo $session['time']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php
                        }
                    ?>
                    <span class="close" onclick="closePopup('tutorialSessionsPopup')">&times;</span>
                </div>
            </div>
        </div>

        <!-- Section 2: Notifications -->
        <div class="dashboard-section" id="section2">

            <div class="circles" style="background-color: #37566b;"></div>
            <div class="tiles"><h3>Notifications</h3></div>
            <div class="descriptions"><p>View Notifications.</p></div>

            
        </div>

        <!-- Section 3: Upcoming Events -->
        <div class="dashboard-section" id="section3">
            <div class="circles" style="background-color: #f08f75;"></div>
            <div class="tiles"><h3>Upcoming Events</h3></div>
            <div class="descriptions"><p>View upcoming events</p></div>
        </div>
        
    </div>


        <!-- Overlay element -->
    <div id="overlay" class="overlay"></div>

<?php echo '</body></html>'; ?>
