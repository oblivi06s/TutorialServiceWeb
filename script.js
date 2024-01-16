function logout() {
    window.location.href = "logout.php";
}

document.addEventListener('DOMContentLoaded', function () {
    var popups = document.querySelectorAll('.popup');

    // Add a click event listener to each popup
    popups.forEach(function (popup) {
        popup.addEventListener('click', function (event) {
            event.stopPropagation();
        });
    });
});


function showPopup(popupId) {
    hideAllPopups();
    console.log("Opening "+ popupId);

    document.getElementById(popupId).style.display = 'block';
    document.getElementById('overlay').style.display = 'block';
    document.body.style.overflow = 'hidden'; 
}

function hideAllPopups() {
    console.log("Entered in hideAllPopups");
    document.querySelectorAll('.popup').forEach(function (popup) {
        popup.style.display = 'none';
    });
    document.getElementById('overlay').style.display = 'none';
    document.body.style.overflow = 'auto'; 
}


function closePopup(popupId) {
    console.log("Closing "+ popupId);
    document.getElementById(popupId).style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
    document.body.style.overflow = 'auto'; // Enable scrolling on the background
    event.stopPropagation();
}

function assignTutor(openPopup, exitPopup) {
   closePopup(exitPopup);
   showPopup(openPopup);
}


function removeStudent(studentID) {
    var confirmation = confirm("Are you sure you want to remove this student with studentID "+studentID+" ?");
    if (confirmation) {
        window.location.href = 'remove_user.php?userID=' + studentID+ "&userType=student";
    }
}

function removeTutor(tutorID) {
    var confirmation = confirm("Are you sure you want to remove this tutor with tutorID "+tutorID+" ?");
    if (confirmation) {
        window.location.href = 'remove_user.php?userID=' + tutorID+ "&userType=tutor";
    }
}

function removeSession(sessionID) {
    var confirmation = confirm("Are you sure you want to remove this session with sessionID "+sessionID+" ?");
    if (confirmation) {
        window.location.href = "remove_user.php?sessionID=" + sessionID;
    }
}

function removeTutorialRequest(requestID) {
    var confirmation = confirm("Are you sure you want to remove this request with requestID "+requestID+" ?");
    if (confirmation) {
        window.location.href = "remove_user.php?requestID=" + requestID;
    }
}
