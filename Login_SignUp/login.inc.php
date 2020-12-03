<?php
$sender_name = stripslashes($_POST["sender_name"]);
$sender_email = stripslashes($_POST["sender_email"]);
$sender_message = stripslashes($_POST["sender_message"]);
$response = $_POST["g-recaptcha-response"];

$url = 'https://www.google.com/recaptcha/api/siteverify';
$data = array(
    'secret' => 'SECRET KEY',
    'response' => $_POST["g-recaptcha-response"]
);
$options = array(
    'http' => array(
        'method' => 'POST',
        'content' => http_build_query($data)
    )
);
$context = stream_context_create($options);
$verify = file_get_contents($url, false, $context);
$captcha_success = json_decode($verify);

if ($captcha_success->success==false) {
    echo "<p>You failed the captcha</p>";
} else if ($captcha_success->success==true) {

    if (isset($_POST['login-submit'])) {
        //Add the database connection
        require 'config.php';
        //Fetch the email or username and the password
        $mailuid = $_POST['mailuid'];
        $password = $_POST['pwd'];

        //Start the security checks
        if (empty($mailuid) || empty($password)) {
            header("Location: loginpage?error=emptyfields");
            exit();
        } else {
            //Check if the user exists
            $sql = "SELECT * FROM /* Database */ WHERE /* Username table */=? OR /* Mail table */=?;";
            $stmt = mysqli_stmt_init($mysqli);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: loginpage?error=sqlerror");
                exit();
            } else {
                //Make the statements and check the password
                mysqli_stmt_bind_param($stmt, "ss", $mailuid, $mailuid);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if ($row = mysqli_fetch_assoc($result)) {
                    $pwdCheck = password_verify($password, $row['Wachtwoord']);
                    if ($pwdCheck == false) {
                        header("Location: loginpage?error=wrongpwd");
                        exit();
                    } else if ($pwdCheck == true) {
                        //start the session and add the username to the session variable
                        session_start();
                        $_SESSION['userId'] = $row['ID_User'];
                        $_SESSION['userUid'] = $row['Username'];
                        //If logged in get send to the home page
                        header("Location: loginpage?login=success");
                        exit();
                    } else {
                        header("Location: loginpage?error=wrongpwd");
                        exit();
                    }
                } else {
                    header("Location: loginpage?error=nouser");
                    exit();
                }
            }
        }
    } else {
        header("Location: loginpage");
        exit();
    }
}
