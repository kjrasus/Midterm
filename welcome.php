<?php 
session_start();
  
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"]== "POST"){
    $sql= 'SELECT id, username, password FROM users WHERE username = ?';
    if ($stmt = mysql_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, 's', $param_username);


        $param_username= $username;
        $stmt1= $link->prepare("INSER INTO actvity_log (activity,username) VALUES (?,?)");
        $stmt1->bind_param("ss", $activity, $username);

        $activity="Logged out";
        $username = ($_SESSION["username"]);

        $stmt1->execute();
        $stmt->close();

        header("location: login.php");

    }
    mysqli_close($conn);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" type="text/css" href="css/style1.css"> 

</head>
<body>
    
    <div class="header">
        <h2><b>Welcome!</h2>
    </div>  
    <center>
            <form>
                <p> Log in Successful! </p>
                <p>
                    <br> 
                    <a href="logout.php" class="btn btn-danger" >Logout</a>
                </p>
            </form>
    </center>
</body>
</html>