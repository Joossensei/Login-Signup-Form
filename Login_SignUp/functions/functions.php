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
function Error_Display($Error)
{
  return '<div class="alert alert-danger">' . $Error . '</div>';
}

/* ------------------------------- FORM CHECKS ------------------------------ */

//Token Generator
function token_generator()
{
  $token = $_SESSION['token'] = md5(uniqid(mt_rand(), true));
  return $token;
}

//Check of het email adres bestaat
function Email_Exist($Email)
{
  global $mysqli;
  $query = "SELECT * FROM users WHERE Email=?";
  $stmt = mysqli_stmt_init($mysqli);
  if (!mysqli_stmt_prepare($stmt, $query)) {
    return false;
  } else {
    mysqli_stmt_bind_param($stmt, "s", $Email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $resultCheck = mysqli_stmt_num_rows($stmt);
    if ($resultCheck == 0) {
      return true;
    }
  }
}

//Check of de gebruikersnaam bestaat
function User_Exists($User)
{
  global $mysqli;
  $sql = "SELECT UserName FROM users WHERE UserName=?";
  $stmt = mysqli_stmt_init($mysqli);
  if (!mysqli_stmt_prepare($stmt, $sql)){
    return false;
  } else {
    mysqli_stmt_bind_param($stmt, "s", $User);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $resultCheck = mysqli_stmt_num_rows($stmt);
    if ($resultCheck > 0) {
      return false;
    }
  }
}

//Verstuur email functie
function Send_Email($Email,$Subject,$msg,$Header)
{
   return mail($Email,$Subject,$msg,$Header);
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
      $FirstName = $_POST['FName'];
      $LastName = $_POST['LName'];
      $UserName = $_POST['UName'];
      $Email = $_POST['Email'];
      $Password = $_POST['password'];
      $CPassword = $_POST['cpassword'];

      $Errors=[];

      //Check voor lege velden
      if(empty($FirstName) || empty($LastName) || empty($UserName) || empty($Email) || empty($Password) || empty($CPassword))
      {
          $Errors[] = "Vul alle velden in!";
      }

      // Check User Name Characters
      if(!preg_match("/^[A-Za-z,0-9]*$/",$UserName))
      {
            $Errors[] = " Gebruikersnaam kan geen / * @ bezitten ";
      }

      // Check Email Exists
      if(!Email_Exist($Email))
      {
          $Errors[] = " Email al in gebruik ";
      }

      // Check User Exist
      if(!User_Exists($UserName))
      {
          $Errors[] = " Gebruikersnaam al in gebruik ";
      }

      // Password Checking
      if($Password!=$CPassword)
      {
          $Errors[] = " Zorg dat de wachtwoorden gelijk zijn! ";
      }

      if(!empty($Errors))
      {
          foreach($Errors as $display)
          {
              echo Error_Display($display);
          }
      }

      else {
              if(user_registration($FirstName,$LastName,$UserName,$Email,$Password))
              {
                  echo '<div class="alert alert-success"> Je hebt je success aanmgemeld. Check je email voor verificatie </div>';
              }
              else
              {
                  Error_Display(" Probeer het opnieuw er ging iets fout! ");
              }
           }
  }
}
}


/* ------------------------------- Gebruikers aanmaak functie ------------------------------ */

function user_registration($FirstName,$LastName,$UserName,$Email,$Password)
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
    $Validation_Code = md5($UserName+microtime());
    $sql = "INSERT INTO users (UUID, FirstName, LastName, UserName, Email, Password, Validation_Code, Active) VALUES (UUID_TO_BIN(UUID()),?,?,?,?,?,?,'0')";
    $stmt = mysqli_stmt_init($mysqli);
    if (!mysqli_stmt_prepare($stmt, $sql)){
      return false;
      exit();
    } else {

      $hashedPwd = password_hash($Password, PASSWORD_DEFAULT);
      mysqli_stmt_bind_param($stmt, "ssssss", $FirstName, $LastName, $UserName, $Email, $hashedPwd, $Validation_Code);
      mysqli_stmt_execute($stmt);
    }
  }

    $subject = " Verifieer uw account voor /*Bedrijfsnaam*/ ";
    $msg = "<html>";
    $msg .= "<body width:'600px';>";
    $msg .= '<h3> Verifieer uw account van /*Bedrijfsnaam*/ </h3>';
    $msg .= '<h4>Hoi ' . $FirstName . '</h4>';
    $msg .= '<p>Om jouw registratie te voltooien hoef je alleen nog maar op de knop hieronder te klikken om uw email te verifie&#235;ren</p>';
    $msg .= '<a href="/*Activatie link*/' . $Email . '&Code=' . $Validation_Code . '">Verifieer E-mail</a>';
    $msg .= "</body></html>";
    $headers .= "From: /*Verstuurder*/\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    Send_Email($Email,$subject,$msg,$headers);
    return true;
    mysqli_stmt_close($stmt);
    mysqli_close($mysqli);
}


/* ------------------------------- Verifieer account functie ------------------------------ */
function activate()
{
    $Email = $_GET['Email'];
    $Code = $_GET['Code'];
    global $mysqli;
    $query = "SELECT * FROM users WHERE Email=? AND Validation_Code=?";
    $stmt = mysqli_stmt_init($mysqli);
    if (!mysqli_stmt_prepare($stmt, $query)) {
      return false;
      exit();
    } else {
      mysqli_stmt_bind_param($stmt, "ss", $Email, $Code);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      $resultCheck = mysqli_stmt_num_rows($stmt);
      if ($resultCheck > 0) {
          $up_query = "UPDATE users SET Active='1', Validation_Code='0' WHERE Email='$Email' AND Validation_Code='$Code'";
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
    $Email = mysqli_real_escape_string($mysqli, $_POST['Email']);
    $password = mysqli_real_escape_string($mysqli,$_POST['Password']);
    $Remember = isset($_POST['remember']);

    $Errors=[];

    if(empty($Email))
    {
        $Errors[]= " Please Enter Your Email  ";
    }

    if(empty($password))
    {
        $Errors[]= " Please Enter Your Password  ";
    }

    if(!empty($Errors))
    {
        foreach($Errors as $Display)
        {
            echo Error_Display($Display);
        }
    }
    else
    {
        if(login($Email,$password,$Remember))
        {
            header("location:admin.php");
        }
        else
        {
            echo Error_Display(" Your Password or Email is Incorrect ");
        }
    }
  }
}

function login($Email,$password,$Remember)
{
  global $mysqli;
  $sql = "SELECT * FROM users WHERE Email=? AND Active =1";
  $stmt = mysqli_stmt_init($mysqli);
  if (!mysqli_stmt_prepare($stmt, $sql)) {
    return false;
    exit();
  } else {
    mysqli_stmt_bind_param($stmt, "s", $Email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
      $pwdCheck = password_verify($password, $row['Password']);
      if ($pwdCheck == false) {
        return false;
        exit();
      } else if ($pwdCheck == true){
        if ($Remember == true) {
          setcookie('Email',$Email, time() + 86400);
        }
        $_SESSION['Email']=$Email;
        return true;
      } else {
        return false;
      }
    }
  }
}


/* ------------------------------- Wachtwoord vergeten functie ------------------------------ */

 ?>
