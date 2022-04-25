<?php
  require_once('includes/header.php');
?>
<div class="container">
    <div class="row">
        <div class="col-lg-6 m-auto">
            <div class="card mt-5">
                <div class="card-title">
                    <h2 class="text-center py-4"> Maak Account aan! </h2>
                    <hr>
                </div>
                <div class="card-body">
                    <div class="container">
                        <?php form_validation(); ?>
                        <div id="success_msg"></div>
                        <form method="post">
                            <!-- Voornaam -->
                            <div class="form-group">
                                <label for="voorNaam">Voornaam</label>
                                <input type="text" name="FName" class="form-control" id="voorNaam">
                            </div>
                            <!-- Achternaam -->
                            <div class="form-group">
                                <label for="achterNaam">Achternaam</label>
                                <input type="text" name="LName" class="form-control" id="achterNaam">
                            </div>
                            <div class="form-group">
                                <label for="achterNaam">Gebruikersnaam</label>
                                <input type="text" name="UName" class="form-control" id="Gebruikersnaam">
                            </div>
                            <!-- Email -->
                            <div class="form-group">
                                <label for="email">Email address</label>
                                <input type="email" name="Email" class="form-control" id="email" aria-describedby="emailHelp">
                            </div>
                            <!-- Wachtwoord -->
                            <div class="form-group">
                                <label for="wachtwoord">Wachtwoord</label>
                                <input type="password" class="form-control" name="password" id="wachtwoord">
                            </div>
                            <!-- wachtwoordHerh -->
                            <div class="form-group">
                                <label for="wachtwoordHerh">Wachtwoord herhalen</label>
                                <input type="password" class="form-control" name="cpassword" id="wachtwoordHerh">
                            </div>
                            <div class="captcha_wrapper">
 		                           <div class="g-recaptcha" data-sitekey="6Lf7GeIZAAAAAM_3-kAtz53f_8T-41g6xei8MD_d"></div>
 	                         </div>
                            <button type="submit" name="btn_signup" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

  </body>
</html>
