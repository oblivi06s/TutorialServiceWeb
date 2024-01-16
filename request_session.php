<?php
session_start();
include("sqlconnect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $personID = $_SESSION["user_id"];
    $courseName = $_POST["course"];

    $sqlPersonID = "SELECT student_ID FROM students WHERE person_ID = ?";
    $studentIDStmt = $conn->prepare($sqlPersonID);
    $studentIDStmt->bind_param("i", $personID);
    $studentIDStmt->execute();
    $studentIDResult = $studentIDStmt->get_result();
    $row = $studentIDResult->fetch_assoc();
    $studentID = $row['student_ID'];

    $sqlCourse = "SELECT course_ID FROM courses WHERE course_name = ?";
    $courseIDStmt = $conn -> prepare($sqlCourse);
    $courseIDStmt ->bind_param("s", $courseName);
    $courseIDStmt ->execute();
    $courseIDResult = $courseIDStmt->get_result();
    $row = $courseIDResult->fetch_assoc();
    $courseID = $row['course_ID'];

    
    $requestedDate = $_POST["date"];  

    
    $sqlInsert = "INSERT INTO tutorialRequests (student_ID, course_ID, requested_date)
                  VALUES (?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("iis", $studentID, $courseID, $requestedDate);

    if ($stmtInsert->execute()) {
        header("Location: student_dashboard.php");
        exit();
    } else {
        
        echo "Error: " . $stmtInsert->error;
    }

    $stmtInsert->close();
    $conn->close();
} else {
    header("Location: index.html");
    exit();
}
?>
