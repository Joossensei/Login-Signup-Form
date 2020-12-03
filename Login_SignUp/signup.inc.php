<?php
if(isset($_POST['signup-submit'])){

    require 'config.php';

    $username = $_POST['uid'];
    $email = $_POST['mail'];
    $password = $_POST['pwd'];
    $passwordRepeat = $_POST['pwd-repeat'];
//Check of het formulier goed is ingevuld
    if (empty($username) || empty($email) || empty($password) || empty($passwordRepeat)) {
      header("Location: signup.php?error=emptyfields&uid=".$username."&mail=".$email);
      exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Z0-9*$/]",($username))) {
      header("Location: signup.php?error=invalidmailuid");
      exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      header("Location: signup.php?error=invalidmail&uid=".$username);
      exit();
    } else if (!preg_match("/^[a-zA-Z0-9]*$/",($username))) {
      header("Location: signup.php?error=invaliduid&mail=".$email);
      exit();
    } else if ($password !== $passwordRepeat) {
      header("Location: signup.php?error=passwordcheck&uid=".$username."&mail=".$email);
      exit();
    }

// check voor dezelfde username

    else {

      $sql = "SELECT Username FROM BEROEPS_users WHERE Username=?";
      $stmt = mysqli_stmt_init($mysqli);
      if (!mysqli_stmt_prepare($stmt, $sql)){
        header("Location: signup.php?error=sqlerror");
        exit();
      } else {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $resultCheck = mysqli_stmt_num_rows($stmt);
        if ($resultCheck > 0) {
          header("Location: signup.php?error=usertaken&mail=".$email);
          exit();
        }
//Voeg de gegevens toe
    else {

          $sql = "INSERT INTO BEROEPS_users (Username, Mail, Wachtwoord) VALUES (?, ?, ?)";
          $stmt = mysqli_stmt_init($mysqli);
          if (!mysqli_stmt_prepare($stmt, $sql)){
            header("Location: signup.php?error=sqlerror");
            exit();
          } else {
            $hashedPwd = password_hash($password, PASSWORD_DEFAULT);

            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedPwd);
            mysqli_stmt_execute($stmt);
            header("Location: ../index.php?signup=success");
            exit();
          }
        }
      }

    }
    mysqli_stmt_close($stmt);
    mysqli_close($mysqli);
}
else {
  header("Location: signup.php");
  exit();
}
