<?php
require realpath('includes/config.php');

/* ------------------------------- MESSAGES ------------------------------ */
// Zet het bericht in de sessie
function set_message($msg){
  if(!empty($msg)){
    $_SESSION['Message'] = $msg;
  }
  else {
    $msg = "";
  }
}

// Laat het bericht zien
function display_message(){
  if(isset($_SESSION['Message'])){
    echo $_SESSION['Message'];
    unset($_SESSION['Message']);
  }
}


//Error display
function error_display($error)
{
  return '<div class="alert alert-danger">' . $error . '</div>';
}

/* ------------------------------- FORM CHECKS ------------------------------ */

//Token Generator
function token_generator()
{
  $token = $_SESSION['token'] = md5(uniqid(mt_rand(), true));
  return $token;
}

//Check of het email adres bestaat
function email_exist($email)
{
  global $mysqli;
  $query = "SELECT * FROM users WHERE email=?";
  $stmt = mysqli_stmt_init($mysqli);
  if (!mysqli_stmt_prepare($stmt, $query)) {
    return false;
  } else {
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $resultCheck = mysqli_stmt_num_rows($stmt);
    if ($resultCheck == 0) {
      return true;
    }
  }
}

//Check of de gebruikersnaam bestaat
function user_exists($user)
{
  global $mysqli;
  $sql = "SELECT username FROM users WHERE username=?";
  $stmt = mysqli_stmt_init($mysqli);
  if (!mysqli_stmt_prepare($stmt, $sql)){
    return false;
  } else {
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $resultCheck = mysqli_stmt_num_rows($stmt);
    if ($resultCheck > 0) {
      return false;
    }
  }
}

//Verstuur email functie
function send_email($email, $subject, $msg, $header)
{
   return mail($email, $subject, $msg, $header);
}

//Formulier check
function form_validation()
{
  	$response = $_POST["g-recaptcha-response"];

	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$data = array(
		'secret' => 'YOUR_SECRET_KEY',
		'response' => $_POST["g-recaptcha-response"]
	);
	$options = array(
		'http' => array (
			'method' => 'POST',
			'content' => http_build_query($data)
		)
	);
	$context  = stream_context_create($options);
	$verify = file_get_contents($url, false, $context);
	$captcha_success=json_decode($verify);

	if ($captcha_success->success==false) {
		return false;
	} else if ($captcha_success->success==true) {
  		if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['btn_signup'])) {
      			$firstname = $_POST['FName'];
      			$lastname = $_POST['LName'];
      			$username = $_POST['UName'];
      			$email = $_POST['Email'];
      			$password = $_POST['password'];
      			$CPassword = $_POST['cpassword'];

      			$errors=[];

      			//Check voor lege velden
      			if(empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($password) || empty($CPassword)) {
          			$errors[] = "Vul alle velden in!";
      			}

      			// Check User Name Characters
      			if(!preg_match("/^[A-Za-z,0-9]*$/",$username)) {
            			$errors[] = " Gebruikersnaam kan geen / * @ bezitten ";
      			}

      			// Check Email Exists
      			if (!email_exist($email)) {
          			$errors[] = " Email al in gebruik ";
      			}

      			// Check User Exist
      			if(!user_exists($username)) {
          			$errors[] = " Gebruikersnaam al in gebruik ";
      			}

      			// Password Checking
      			if($password!=$CPassword) {
          			$errors[] = " Zorg dat de wachtwoorden gelijk zijn! ";
      			}

      			if(!empty($errors)) {
          			foreach($errors as $display) {
              				echo error_display($display);
          			}
      			}

      			else {
              			if(user_registration($firstname,$lastname,$username,$email,$password)) {
                  			echo '<div class="alert alert-success"> Je hebt je success aanmgemeld. Check je email voor verificatie </div>';
              			}
              			else {
                  			error_display(" Probeer het opnieuw er ging iets fout! ");
              			}
           		}
  		}
	}
}


/* ------------------------------- Gebruikers aanmaak functie ------------------------------ */

function user_registration($firstname,$lastname,$username,$email,$password)
{
  $response = $_POST["g-recaptcha-response"];

	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$data = array(
		'secret' => 'YOUR_SECRET_KEY',
		'response' => $_POST["g-recaptcha-response"]
	);
	$options = array(
		'http' => array (
			'method' => 'POST',
			'content' => http_build_query($data)
		)
	);
	$context  = stream_context_create($options);
	$verify = file_get_contents($url, false, $context);
	$captcha_success=json_decode($verify);

	if ($captcha_success->success==false) {
		return false;
	} else if ($captcha_success->success==true) {

    global $mysqli;
    $validation_code = md5($username+microtime());
    $sql = "INSERT INTO users (UUID, firstname, lastname, username, email, password, validation_code, active) VALUES (UUID_TO_BIN(UUID()),?,?,?,?,?,?,'0')";
    $stmt = mysqli_stmt_init($mysqli);
    if (!mysqli_stmt_prepare($stmt, $sql)){
      return false;
      exit();
    } else {

      $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
      mysqli_stmt_bind_param($stmt, "ssssss", $firstname, $lastname, $username, $email, $hashedPwd, $validation_code);
      mysqli_stmt_execute($stmt);
    }
  }

    $subject = " Verifieer uw account voor /*Bedrijfsnaam*/ ";
    $msg = "<html>";
    $msg .= "<body width:'600px';>";
    $msg .= '<h3> Verifieer uw account van /*Bedrijfsnaam*/ </h3>';
    $msg .= '<h4>Hoi ' . $firstname . '</h4>';
    $msg .= '<p>Om jouw registratie te voltooien hoef je alleen nog maar op de knop hieronder te klikken om uw email te verifie&#235;ren</p>';
    $msg .= '<a href="verifieer.php' . $email . '&Code=' . $validation_code . '">Verifieer E-mail</a>';
    $msg .= "</body></html>";
    $headers .= "From: /*Verstuurder*/\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    send_email($email,$subject,$msg,$headers);
    return true;
    mysqli_stmt_close($stmt);
    mysqli_close($mysqli);
}


/* ------------------------------- Verifieer account functie ------------------------------ */
function activate()
{
    $email = $_GET['Email'];
    $code = $_GET['Code'];
    global $mysqli;
    $query = "SELECT * FROM users WHERE email=? AND validation_code=?";
    $stmt = mysqli_stmt_init($mysqli);
    if (!mysqli_stmt_prepare($stmt, $query)) {
      return false;
      exit();
    } else {
      mysqli_stmt_bind_param($stmt, "ss", $email, $code);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      $resultCheck = mysqli_stmt_num_rows($stmt);
      if ($resultCheck > 0) {
          $up_query = "UPDATE users SET active='1', validation_code='0' WHERE email='$email' AND validation_code='$code'";
          $up_result = mysqli_query($mysqli,$up_query);
          echo '<div class="alert alert-success"> Uw account is geactiveerd <a href="login.php">Klik hier om in te loggen!</a></div>';
      } else {
          echo '<div class="alert alert-danger"> Account is nog niet geactiveerd probeer het opnieuw!</div>';
      }
    }
}

/* ------------------------------- Formulier check Login ------------------------------ */

function login_validation()
{
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-login'])) {
    global $mysqli;
    $email = mysqli_real_escape_string($mysqli, $_POST['Email']);
    $password = mysqli_real_escape_string($mysqli,$_POST['Password']);
    $remember = isset($_POST['remember']);

    $errors=[];

    if(empty($email))
    {
        $errors[]= " Please Enter Your Email  ";
    }

    if(empty($password))
    {
        $errors[]= " Please Enter Your Password  ";
    }

    if(!empty($errors))
    {
        foreach($errors as $display)
        {
            echo error_display($display);
        }
    }
    else
    {
        if(login($email,$password,$remember))
        {
            header("location:admin.php");
        }
        else
        {
            echo error_display(" Your Password or Email is Incorrect ");
        }
    }
  }
}

function login($email,$password,$remember)
{
  global $mysqli;
  $sql = "SELECT * FROM users WHERE email=? AND active =1";
  $stmt = mysqli_stmt_init($mysqli);
  if (!mysqli_stmt_prepare($stmt, $sql)) {
    return false;
    exit();
  } else {
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
      $pwdCheck = password_verify($password, $row['Password']);
      if ($pwdCheck == false) {
        return false;
        exit();
      } else if ($pwdCheck == true){
        if ($remember == true) {
          setcookie('Email',$email, time() + 86400);
        }
        $_SESSION['Email']=$email;
        return true;
      } else {
        return false;
      }
    }
  }
}
?>
