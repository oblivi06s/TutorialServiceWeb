<?php

include("sqlconnect.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get user ID from session or wherever it's stored
    $userID = $_SESSION["user_id"];
    $userType = $_POST["userType"];

    // Get user input from the form
    $firstName = $_POST["editFirstName"];
    $lastName = $_POST["editLastName"];
    $dateOfBirth = $_POST["editDateOfBirth"];
    $contactNumber = $_POST["editContact"];
    $address = $_POST["editAddress"];
    $email = $_POST["editEmail"];
    $username = $_POST["editUsername"];
    $hashedPassword = password_hash($_POST["editPassword"], PASSWORD_DEFAULT);

    $sqlUpdate = "UPDATE persons
                  SET first_name = ?, last_name = ?, date_of_birth = ?
                  WHERE person_ID = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param(
        "sssi",
        $firstName,
        $lastName,
        $dateOfBirth,
        $userID
    );
    $stmtUpdate->execute();

    $sqlUpdate = "UPDATE contactnumbers
                  SET contact_number = ?
                  WHERE contact_number_ID = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param(
        "si",
        $contactNumber,
        $userID
    );
    $stmtUpdate->execute();

    $sqlUpdate = "UPDATE addresses
                  SET address = ?
                  WHERE address_ID = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param(
        "si",
        $address,
        $userID
    );
    $stmtUpdate->execute();

    $sqlUpdate = "UPDATE emails
                  SET email = ?
                  WHERE email_ID = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param(
        "si",
        $email,
        $userID
    );
    $stmtUpdate->execute();

    $sqlUpdate = "UPDATE userCredentials
    SET username = ?, password_hash = ?
    WHERE person_ID = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param(
        "ssi",
        $username,
        $hashedPassword,
        $userID
    );
    $stmtUpdate->execute();

    if ($stmtUpdate->execute()) {
        // User information updated successfully
        header("Location: {$userType}_dashboard.php"); 
    } else {
        // Error updating user information
        echo "Error: " . $stmtUpdate->error;
    }

    $stmtUpdate->close();
    $conn->close();
} else {
    // Redirect or show an error message if the form wasn't submitted via POST
    header("Location: index.php");
    exit();
}

?>
