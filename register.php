<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }


    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 8){
        $password_err = "Password must have atleast 8 characters.";
    } elseif ( !preg_match("#[a-z]+#", $_POST["password"]) ) {
        $password_err = "Password should contain lowercase";
    } elseif ( !preg_match("#[0-9]+#", $_POST["password"]) ) {
        $password_err = "Password should contain atleast a number";
    } elseif( !preg_match("#[A-Z]+#", $_POST["password"]) ) {
        $password_err = "Password should contain uppercase";
    } elseif ( !preg_match("/\W/", $_POST["password"])) {
        $password_err = "Password should contain special characters";
    }
    else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    


    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter email"; 
    }elseif(!preg_match('/^[a-zA-Z0-9_.-]+@+[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/', $_POST["email"])){
        $email_err = "invalid email";

    }
    else{
        $email = trim($_POST["email"]);
    }
    

    

    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>REGISTER Form</title>
    <link rel="stylesheet" href="css/style1.css">
    
</head>
<body>
    <div class="header">
        <h2>REGISTER</h2>
    </div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>USERNAME</label>
                <input type="text"  name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>   
            <div class="input-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>EMAIL</label>
                <input type="text"  name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>  
            <div class="input-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>PASSWORD</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="input-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>CONFIRM PASSWORD</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>

            <center>
                <input type="submit" class="btn" value="Submit">
           
            <p>Already have an account? <a href="login.php">Login here</a>.</p>

            </center>
        </form>
    </div>    
</body>
</html>