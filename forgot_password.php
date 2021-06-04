<?php
// Include config file
include_once "config.php";

// Define variables and initialize with empty values
$username = $password = $confirm_password =  "";
$username_err = $password_err = $confirm_password_err =  "";
// Processing form data when form is submitted

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Validate username
  if (empty(trim($_POST['uname']))) {
    $username_err = "Please enter a username.";
  } else {
    // Prepare a select statement
    $sql = "SELECT username FROM users WHERE username = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "s", $param_username);

      // Set parameters
      $param_username = trim($_POST['uname']);

      // Attempt to execute the prepared statement
      if (mysqli_stmt_execute($stmt)) {
        /* store result */
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
          $username = trim($_POST['uname']);
        } else {
          $username_err = "There is no account with that username";
        }
      } else {
        echo "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      mysqli_stmt_close($stmt);
    }
  }

  // Validate password
  $password = $_POST['psw'];
  $uppercase = preg_match('@[A-Z]@', $password);
  $lowercase = preg_match('@[a-z]@', $password);
  $number    = preg_match('@[0-9]@', $password);
  $specialChars = preg_match('@[^\w]@', $password);
  if (empty($password)) {
    $password_err = "Please enter a password.";
  } elseif (strlen(trim($_POST['psw'])) < 8) {
    $password_err = "Password must have atleast 8 characters.";
  } elseif (!$uppercase) {
    $password_err = "Password should contain 1 upper case.";
  } elseif (!$lowercase) {
    $password_err = "Password should contain 1 lower case.";
  } elseif (!$number) {
    $password_err = "Password should contain 1 number.";
  } elseif (!$specialChars) {
    $password_err = "Password should contain 1 special character.";
  } else {
    $password = trim($_POST['psw']);
  }

  // Validate confirm password
  if (empty(trim($_POST['psw-repeat']))) {
    $confirm_password_err = "Please enter confirm password.";
  } else {
    $confirm_password = trim($_POST['psw-repeat']);
    if (empty($password_err) && ($password != $confirm_password)) {
      $confirm_password_err = "Password did not match.";
    }
  }


  // Check input errors before inserting in database
  if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {

    // Prepare an update statement
    $sql = "UPDATE users SET password = ? WHERE username = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "ss", $param_password, $param_username);

      // Set parameters
      $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
      $param_username = $username;

      // Attempt to execute the prepared statement
      if (mysqli_stmt_execute($stmt)) {
        
        // prepare and bind
         $stmt1 = $link->prepare("INSERT INTO activity_log (activity, username) VALUES (?, ?)");
         $stmt1->bind_param("ss", $activity, $username);

        // // set parameters and execute
         $activity = "Reset a Password";
         $username = $username;
        
         $stmt1->execute();
         $stmt1->close();

        // Redirect to login page
        header("location: login.php");
      } else {
        echo "Something went wrong. Please try again later.";
      }

      // Close statement
      mysqli_stmt_close($stmt);
    }
  }

  // Close connection
  mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style1.css">
   
<head>
    <meta charset="UTF-8">
    <div></div>
    <title>RESET PASSWORD</title>
   
</head>  
<body>
       
    <div class="header">
        <h2>RESET PASSWORD</h2>
    </div>
        
        <form action="" method="post">
            <div class="input-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
              <label for="uname"><b>USERNAME</b></label>
        
              <input type="text" name="uname" id="uname" class="form-control" value="<?php echo $username; ?>">
              <span class="help-block">
                <?php echo $username_err; ?>
              </span>
            </div> 
         
            
            <div class="input-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
        <label for="psw"><b>NEW PASSWORD</b></label>
        
        <input type="password" name="psw" id="psw" class="form-control" value="<?php echo $password; ?>">
        <span class="help-block"><?php echo $password_err; ?></span>
      </div>
      

              <div class="input-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
        <label for="psw-repeat"><b>CONFIRM PASSWORD</b></label>
        
        <input type="password"  name="psw-repeat" id="psw-repeat" class="form-control" value="<?php echo $confirm_password; ?>">
        <span class="help-block"><?php echo $confirm_password_err; ?>

        </span>
      </div>
      
      <center>
        <input type="submit" class="btn" value="Submit">  
        <p>Don't have an account? <a href="register.php">Register</a>.</p>
      </center>
            
        
    </div>
        </form>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>