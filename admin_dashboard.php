<?php
session_start();

// Check if the user is logged in and has the admin role
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    // Redirect to the login page if not logged in or not an admin
    header("Location: index.html");
    exit();
}

// Include your SQL connection file
include("sqlconnect.php");

    $sql = "SELECT student_ID, first_name, last_name, address, email, contact_number, DATE_FORMAT(date_of_birth, '%M %e, %Y') as Birthday
    FROM Addresses JOIN (
        SELECT * FROM Emails JOIN (
            SELECT * FROM ContactNumbers JOIN (
                SELECT * FROM Persons
                JOIN Students USING (person_ID)
            ) AS st USING (contact_number_ID)
        ) AS ct USING (email_ID)
    ) AS em USING (address_ID)";
    $student_result = $conn->query($sql);
    
    $sql = "SELECT tutor_ID, first_name, last_name, address, email, contact_number, DATE_FORMAT(date_of_birth, '%M %e, %Y') as Birthday
    FROM Addresses JOIN (
        SELECT * FROM Emails JOIN (
            SELECT * FROM ContactNumbers JOIN (
                SELECT * FROM Persons
                JOIN Tutors USING (person_ID)
            ) AS st USING (contact_number_ID)
        ) AS ct USING (email_ID)
    ) AS em USING (address_ID)";
    $tutor_result = $conn->query($sql);

    $sqlAssignments = "SELECT session_ID, tutor_ID, first_name as tutor_fname, last_name as tutor_lname,
                            student_ID, student_fname, student_lname, course_name, date, location, duration_in_hours, time
                            from persons join (
                                SELECT * from tutors join (
                                        SELECT session_ID, first_name as student_fname, last_name as student_lname, student_ID, tutor_ID, course_name, date, location, duration_in_hours, time from persons join (
                                                SELECT * from students join (
                                                    SELECT * FROM courses JOIN (
                                                        SELECT * FROM conducts JOIN sessions using(session_ID)
                                                        ) as f using(course_ID)
                                                    ) as s using(student_ID)
                                                ) as t using(person_ID)
                                        ) as fr using(tutor_ID)
                                ) as fif using(person_ID) ORDER BY session_ID";

    $resultAssignments = $conn->query($sqlAssignments);

    $sqlTutorRequests = "SELECT request_ID, student_ID, first_name, last_name, course_name, requested_date
                         from  persons join (
                            select * from courses join (
                                select * from students join tutorialRequests using(student_ID)
                            )as f using(course_ID)
                        ) as s using(person_ID) ORDER BY request_ID";
    $TutorialRequests = $conn->query($sqlTutorRequests);

// Close the database connection
$conn->close();
?>

<?php include("top.html"); ?>
    
    <div class="dashboard-container">
        <!-- Box 1: Students -->
        <div class="dashboard-section" onclick="showPopup('studentsPopup')">
            <div class="circles" style="background-color: #73bdbd;"></div>
            <div class="tiles"><h3>Students</h3></div>
            <div class="descriptions"><p>View and manage student information.</p></div>

            <div id="studentsPopup" class="popup">
                <div class="popup-content" >
                <h2>Students</h2>
                    <?php
                        if ($student_result->num_rows > 0) {
                            // Output HTML structure with the table inside the div
                            echo '<div class="section">
                                    <table border="1">
                                        <thead>
                                            <tr>
                                                <th>Student ID</th>
                                                <th>First name</th>
                                                <th>Last name</th>
                                                <th>Address</th>
                                                <th>Email</th>
                                                <th>Contact Number</th>
                                                <th>Birthday</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                    
                            // Output data of each row
                            while ($row = $student_result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['student_ID']}</td>
                                        <td>{$row['first_name']}</td>
                                        <td>{$row['last_name']}</td>
                                        <td>{$row['address']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['contact_number']}</td>
                                        <td>{$row['Birthday']}</td>
                                        <td>
                                            <button onclick=\"removeStudent({$row['student_ID']})\">Remove</button>
                                        </td>
                                    </tr>";
                            }
                    
                            // Close table and div
                            echo "</tbody></table></div>";
                        } else {
                            echo "No students found.";
                        }
                    ?>
                        <span class="close" onclick="closePopup('studentsPopup')">&times;</span>
                </div>
            </div>
        </div>

        <!-- Box 2: Tutors -->
        <div class="dashboard-section" onclick="showPopup('tutorsPopup')">
            <div class="circles" style="background-color: #cec97b;"></div>
            <div class="tiles"><h3>Tutors</h3></div>
            <div class="descriptions"><p>View and manage tutor information.</p></div>

            <div id="tutorsPopup" class="popup">
                <div class="popup-content">
                <h2>Tutors</h2>
                    <?php
                        if ($tutor_result->num_rows > 0) {
                            // Output HTML structure with the table inside the div
                            echo '<div class="section">
                                    <table border="1">
                                        <thead>
                                            <tr>
                                                <th>Tutor ID</th>
                                                <th>First name</th>
                                                <th>Last name</th>
                                                <th>Address</th>
                                                <th>Email</th>
                                                <th>Contact Number</th>
                                                <th>Birthday</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                    
                            // Output data of each row
                            while ($row = $tutor_result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['tutor_ID']}</td>
                                        <td>{$row['first_name']}</td>
                                        <td>{$row['last_name']}</td>
                                        <td>{$row['address']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['contact_number']}</td>
                                        <td>{$row['Birthday']}</td>
                                        <td>
                                            <button onclick=\"removeTutor({$row['tutor_ID']})\">Remove</button>
                                        </td>
                                    </tr>";
                            }
                    
                            // Close table and div
                            echo "</tbody></table></div>";
                        } else {
                            echo "No tutors found.";
                        }
                    ?>
                        <span class="close" onclick="closePopup('tutorsPopup')">&times;</span>
                </div>
            </div>
        </div>

        <!-- Box 2: Tutor Assignment -->
        <div class="dashboard-section" onclick="showPopup('assignmentPopup')">
            <div class="circles" style="background-color: #37566b;"></div>
            <div class="tiles"><h3>Tutorial Sessions</h3></div>
            <div class="descriptions"><p>View and manage tutor assignment information.</p></div>

            <div id="assignmentPopup" class="popup">
                <div class="popup-content" >
                    <h2>Tutorial Sessions</h2>
                    <?php
                        if ($resultAssignments->num_rows > 0) {
                            // Output HTML structure with the table inside the div
                             echo '<div class="section">
                                <table border="1" >
                                    <thead>
                                        <tr>
                                            <th>Session ID</th>
                                            <th>Tutor ID</th>
                                            <th>Tutor First Name</th>
                                            <th>Tutor Last Name</th>
                                            <th>Student ID</th>
                                            <th>Student First Name</th>
                                            <th>Student Last Name</th>
                                            <th>Course</th>
                                            <th>Date</th>
                                            <th>Location</th>
                                            <th>Duration (in hours)</th>
                                            <th>Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                <tbody>';
                        
                                // Output data of each row
                        while ($row = $resultAssignments->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['session_ID']}</td>
                                    <td>{$row['tutor_ID']}</td>
                                    <td>{$row['tutor_fname']}</td>
                                    <td>{$row['tutor_lname']}</td>
                                    <td>{$row['student_ID']}</td>
                                    <td>{$row['student_fname']}</td>
                                    <td>{$row['student_lname']}</td>
                                    <td>{$row['course_name']}</td>
                                    <td>{$row['date']}</td>
                                    <td>{$row['location']}</td>
                                    <td>{$row['duration_in_hours']}</td>
                                    <td>{$row['time']}</td>
                                    <td>
                                        <button id=\"removeButton\" onclick=\"removeSession({$row['session_ID']})\">Remove</button>
                                    </td>
                                </tr>";
                        }      
                            echo "</tbody></table></div>";
                        } else {
                            echo "No students found.";
                            }
                    ?>

                    <span class="close" onclick="closePopup('assignmentPopup')">&times;</span>

                    <button id="assignButton" onclick="assignTutor('AssignTutorForm', 'assignmentPopup')">Open Tutor Assignment</button>

                </div>
            </div>

            <div id="AssignTutorForm" class="popup">
                <div class="popup-content">
                    <form id="assignTutors" action="assign_tutor.php" method="post">
                        <h2>Assign Tutor</h2>
                        <label for="tutorID">Tutor ID:</label>
                        <input type="number" id="tutorID" name="tutorID" placeholder="Enter tutor ID" required>
                        <br>
                        <label for="studentID">Student ID:</label>
                        <input type="number" id="studentID" name="studentID" placeholder="Enter student ID" required>
                        <br>
                        <label for="location">Location:</label>
                        <input type="text" id="location" name="location" placeholder="Enter location" required>
                        <br>
                        <label for="time">Time:</label>
                        <input type="time" id="time" name="time" required>
                        <br>
                        <label for="date">Date:</label>
                        <input type="date" id="date" name="date" required>
                        <br>
                        <label for="duration">Duration (hours):</label>
                        <input type="number" id="duration" name="duration" placeholder="Enter duration" required>
                        <br>
                        <h4>Choose from the following courses:</h4>

                        <div class="Courses">
                            <label>
                                Mathematics
                            </label>
                            <input type="radio" id="mathematics" name="course" value="1" checked required>
                            <br>
                            <label>
                                English
                            </label>
                            <input type="radio" id="english" name="course" value="2" checked required>
                            <br>
                            <label>
                                Science
                            </label>
                            <input type="radio" id="science" name="course" value="3" checked required>
                        </div>
                        <br>
                        <button type="submit">Assign Tutor</button>
                    </form>

                    <!-- Close button -->
                    <span class="close" onclick="closePopup('AssignTutorForm')">&times;</span>
                </div>
            </div>
        </div>

        <!-- Box 2: Tutorial Session Request -->
        <div class="dashboard-section" onclick="showPopup('tutorialRequests')">
            <div class="circles" style="background-color: #f08f75;"></div>
            <div class="tiles"><h3>Tutorial Session Requests</h3></div>
            <div class="descriptions"><p>View and manage tutorial session request.</p></div>

            <div id="tutorialRequests" class="popup">
                <div class="popup-content">
                <div class="tiles"><h2>Tutorial Session Requests</h2></div>
                    <?php
                        if ($TutorialRequests->num_rows > 0) {
                            // Output HTML structure with the table inside the div
                            echo '<div class="section">
                                    <table border="1">
                                        <thead>
                                            <tr>
                                                <th>Request ID</th>
                                                <th>Student ID</th>
                                                <th>Student First Name</th>
                                                <th>Student Last Name</th>
                                                <th>Course Name</th>
                                                <th>Requested Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                    
                            // Output data of each row
                            while ($row = $TutorialRequests->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['request_ID']}</td>
                                        <td>{$row['student_ID']}</td>
                                        <td>{$row['first_name']}</td>
                                        <td>{$row['last_name']}</td>
                                        <td>{$row['course_name']}</td>
                                        <td>{$row['requested_date']}</td>
                                        <td>
                                            <button onclick=\"removeTutorialRequest('{$row['request_ID']}')\">Delete</button>
                                        </td>
                                    </tr>";
                            }
                    
                            // Close table and div
                            echo "</tbody></table></div>";
                        } else {
                            echo "No tutorial request found.";
                        }
                    ?>
                        <span class="close" onclick="closePopup('tutorialRequests')">&times;</span>
                </div>
            </div>
            
            
        </div>
    </div>  

    <!-- Overlay element -->
    <div id="overlay" class="overlay"></div>

<?php echo '</body></html>'; ?>