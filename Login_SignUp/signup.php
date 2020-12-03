<?php
if (isset($succes) || isset($error)) {
    $succes = $_GET['signup'];
    $error = $_GET['error'];

    if ($succes == "succes") {
        $message = "U heeft uw account aan gemaakt!";
    } elseif ($error == "emptyfields") {
        $message = "Er is een leeg veld!";
    } elseif ($error == "invalidmailuid") {
        $message = "Onjuiste gebruikersnaam/Mail!";
    } elseif ($error == "invalidmail") {
        $message = "Onjuiste Mail!";
    } elseif ($error == "invaliduid") {
        $message = "Onjuiste gebruikersnaam!";
    } elseif ($error == "passwordcheck") {
        $message = "Wachtwoorden komen niet overeen!";
    } elseif ($error == "sqlerror") {
        $message = "Er gaat iets fout (╯°□°）╯︵ ┻━┻ dit komt niet door u!";
    } elseif ($error == "usertaken") {
        $message = "Gebruikersnaam is al in gebruik!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../js/accountcreation.js"></script>
    <link rel="stylesheet" href="../cs/style.css">
    <title>Maak een account aan</title>
</head>
<body>
<form class="form-signup" action="signup.inc.php" method="post">
    <fieldset>
        <legend>Account Aanmaken!</legend>
        <div class="inputmenu">
            <input type="text" name="uid" class="inputtext" placeholder="Username">
            <input type="text" name="mail" class="inputtext" placeholder="E-Mail">
            <input type="password" name="pwd" class="inputtext" placeholder="Password">
            <input type="password" name="pwd-repeat" class="inputtext" placeholder="Repeat Password">
            <button type="submit" name="signup-submit">Maak een Account aan!</button>
        </div>
        <p><?php
            $message
            ?></p>
    </fieldset>
</form>
</body>
</html>