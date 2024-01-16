<?php

include("sqlconnect.php");

if (isset($_GET['userID']) && isset($_GET['userType'])) {
    $userID = $_GET['userID'];
    $userType = $_GET['userType'];

    
    $table = ($userType === 'student') ? 'students' : 'tutors';

    $sqlPersonID = "SELECT * FROM $table join persons using(person_ID) WHERE {$userType}_ID = ?";
    $personIDStmt = $conn->prepare($sqlPersonID);
    $personIDStmt->bind_param("i", $userID);
    $personIDStmt->execute();
    
    
    // Fetch the result
    $personIDResult = $personIDStmt->get_result();
    $row = $personIDResult->fetch_assoc();
    $personID = $row['person_ID'];
    $contact_number_ID = $row['contact_number_ID'];
    $addressID = $row['address_ID'];
    $emailID = $row['email_ID'];


    
    // Delete the user
    $sqlDelete = "DELETE FROM persons WHERE person_ID = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $personID);
    $stmtDelete->execute();

    $sqlDelete = "DELETE FROM contactnumbers WHERE contact_number_ID = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $contact_number_ID);
    $stmtDelete->execute();

    $sqlDelete = "DELETE FROM addresses WHERE address_ID = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $addressID);
    $stmtDelete->execute();

    $sqlDelete = "DELETE FROM emails WHERE email_ID = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $emailID);
    $stmtDelete->execute();

    
    
    if ($stmtDelete->execute()) {
        header("Location: admin_dashboard.php");
    } else {
        echo "Error removing user: " . $stmtDelete->error;
    }
} elseif (isset($_GET['sessionID'])) {
    $sessionID = $_GET['sessionID'];

    $sql = "DELETE FROM sessions WHERE session_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sessionID);
    
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
    } else {
        echo "Error removing session: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} elseif(isset($_GET['requestID'])){
    $requestID = $_GET['requestID'];

    $sql = "DELETE FROM tutorialRequests WHERE request_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $requestID);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
    } else {
        echo "Error removing session: " . $stmt->error;
    }

}else {
    header("Location: index.php");
    exit();
}
?>{
    
}
?>
