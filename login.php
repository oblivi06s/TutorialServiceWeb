<?php
include("sqlconnect.php");


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    session_start();

    // User Credentials
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Query to check if the user exists
    $sql = "SELECT UC.person_ID, UC.role, UC.password_hash
            FROM UserCredentials UC
            WHERE UC.username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify the password
    if ($user && password_verify($password, $user["password_hash"])) {
        // Password is correct, user exists

        // Check the user's role
        $role = $user["role"];
        
        $_SESSION["role"] = $role;
        $_SESSION["user_id"] = $user["person_ID"];
        $_SESSION["username"] = $username;

        // Check if the person_ID is in the Students or Tutors table
        $personID = $user["person_ID"];
        $isStudent = checkPersonInTable($conn, $personID, 'Students');
        $isTutor = checkPersonInTable($conn, $personID, 'Tutors');

        // Redirect to the appropriate dashboard based on role and presence in Students or Tutors table
        if ($role === "admin") {
            header("Location: admin_dashboard.php");
        } elseif ($role === "user") {
            if ($isStudent) {
                header("Location: student_dashboard.php");
            } elseif ($isTutor) {
                header("Location: tutor_dashboard.php");
            }
        }
    } else {
        // Invalid credentials, show an error message or redirect to the login page
        
        $_SESSION["failed"] = true;
        header("Location: index.php");
        
    }

    $stmt->close();
}

// Function to check if a person_ID exists in a specific table
function checkPersonInTable($conn, $personID, $table)
{
    $sql = "SELECT 1 FROM $table WHERE person_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $personID);
    $stmt->execute();
    $stmt->store_result();
    $rowCount = $stmt->num_rows;
    $stmt->close();
    
    return $rowCount > 0;
}
?>
