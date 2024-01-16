<?php
// Replace these values with your actual database credentials
include ("sqlconnect.php");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Personal Information
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $date_of_birth = $_POST["date_of_birth"];

    // Contact Information
    $contact_number = $_POST["contact_number"];
    $address = $_POST["address"];
    $email = $_POST["email"];

    // User Credentials
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Insert data into Persons table
    $sql = "INSERT INTO Persons (first_name, last_name, date_of_birth) VALUES ('$first_name', '$last_name', '$date_of_birth')";
    $conn->query($sql);

    $entry_id = $conn->insert_id;

    // Insert data into ContactNumbers table
    $sql = "INSERT INTO ContactNumbers (contact_number) VALUES ('$contact_number')";
    $conn->query($sql);

    // Insert data into Addresses table
    $sql = "INSERT INTO Addresses (address) VALUES ('$address')";
    $conn->query($sql);

    // Insert data into Emails table
    $sql = "INSERT INTO Emails (email) VALUES ('$email')";
    $conn->query($sql);


    // Update Persons with contact_number_ID, address_ID, and email_ID
    $sql = "UPDATE Persons SET contact_number_ID = LAST_INSERT_ID(), address_ID = LAST_INSERT_ID(), email_ID = LAST_INSERT_ID() WHERE person_ID = '$entry_id'";
    $conn->query($sql);

    $hashedPassword = password_hash($_POST["password"], PASSWORD_DEFAULT);
    // Insert data into UserCredentials table
    $sql = "INSERT INTO UserCredentials (person_ID, username, password_hash, role) VALUES ('$entry_id', '$username', '$hashedPassword', 'user')";
    $conn->query($sql);

    $role = $_POST['role'];

    if ($role == 'student') {
        $sql = "INSERT INTO Students (person_ID) VALUES ('$entry_id')";
        $conn->query($sql);
    } else {
        $sql = "INSERT INTO Tutors (person_ID) VALUES ('$entry_id')";
        $conn->query($sql);
     }

    // Close the database connection
    $conn->close();

    // Redirect to a success page or perform other actions as needed
    header("Location: index.php");
    exit();
}
?>
