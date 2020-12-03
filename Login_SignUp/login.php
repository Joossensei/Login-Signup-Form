<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../cs/style.css">
    <title>Inloggen</title>
</head>
<body>

    <form method="POST" action="login.inc.php">
	  <fieldset>
                <legend>In Loggen!</legend>
		<div class="inputmenu">
                <input type="text" name="mailuid" class="inputtext" placeholder="E-mail/Username">
                <input type="password" name="pwd" class="inputtext" placeholder="Password">
                <div class="g-recaptcha" data-sitekey="PUBLIC KEY"></div>
                <button type="submit" name="login-submit">Login</button>
			<p style="font-size: 12px">Nieuw Account, <a href="signup.php" value="signup">Klik hier</a></p>
			</div>
		</fieldset>
    </form>
	

</body>
</html>
