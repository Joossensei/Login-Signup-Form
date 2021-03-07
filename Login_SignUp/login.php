<?php
require_once 'includes/header.php';
session_start();
 ?>
 <!--Main Page Content-->
       <div class="container">
           <div class="row">
               <div class="col-lg-6 m-auto">
                   <div class="card mt-5">
                       <div class="card-title">
                           <h2 class="text-center py-4"> Login Formulier </h2>
                           <hr>
                           <?php
                               login_validation();
                               display_message();
                           ?>
                       </div>
                           <div class="card-body">
                               <form method="POST" class="login_form">
                                   <input type="text" name="Email" placeholder="Email of gebruikersnaam" class="form-control mb-2 py-2">
                                   <input type="password" name="Password" placeholder=" Wachtwoord" class="form-control mb-2 py-2">
                                   <button class="btn btn-success float-right" name="btn-login" > Login </button>

                           </div>
                           <div class="captcha_wrapper">
		                           <div class="g-recaptcha" data-sitekey="6Lf7GeIZAAAAAM_3-kAtz53f_8T-41g6xei8MD_d"></div>
	                         </div>
                           <div class="card-footer">
                               <a href="signup.php">Nog geen Account?</a>
                                   <input type="checkbox" name="remember" class="align-content-right"> <span>Blijf ingelogd </span>
                               </form>
                           </div>
                 </div>
               </div>
           </div>
       </div>

<?php require_once 'includes/footer.php'; ?>
