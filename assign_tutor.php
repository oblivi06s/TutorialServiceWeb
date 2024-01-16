<?php
    include("sqlconnect.php");

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Retrieve form data
        $date = $_POST["date"];
        $location = $_POST["location"];
        $duration = $_POST["duration"];
        $time = $_POST["time"];
        $tutorID = $_POST["tutorID"];
        $studentID = $_POST["studentID"];
        $courseID = $_POST["course"];

        $sqlInsertSession = "INSERT INTO sessions (date, location, duration_in_hours, time) VALUES (?, ?, ?, ?)";
        $stmtInsertSession = $conn->prepare($sqlInsertSession);
        $stmtInsertSession->bind_param("ssis", $date, $location, $duration, $time);
        $stmtInsertSession->execute();

        $sessionID = $stmtInsertSession->insert_id;

        $stmtInsertSession->close();

        $sqlInsertConduct = "INSERT INTO conducts (session_ID, student_ID, tutor_ID,  course_ID) VALUES (?, ?, ?, ?)";
        $stmtInsertConduct = $conn->prepare($sqlInsertConduct);
        $stmtInsertConduct->bind_param("iiii", $sessionID, $studentID, $tutorID,  $courseID);
        $stmtInsertConduct->execute();
          
        

        // Close the statement
        $stmtInsertConduct->close();

    }

    // Close the database connection
    $conn->close();

    header("Location: admin_dashboard.php");
?>
