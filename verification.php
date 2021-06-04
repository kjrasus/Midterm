<?php 

session_start(); 

if(!isset($_SESSION["verify"]) || $_SESSION["verify"] !== true){
    header("location: login.php");
    exit;
}
 
require_once "config.php";


$code_err = "";
$_SESSION["code_access"] = true;



if(isset($_POST['login']))
{ 
    if(empty(trim($_POST["code"]))){
        echo "<script>alert('PLEASE ENTER CODE');</script>";
    } else{ 

        date_default_timezone_set('Asia/Manila');
        $currentDate = date('Y-m-d H:i:s');
        $currentDate_timestamp = strtotime($currentDate);
        $code = $_POST['code'];
        

        $id_code = mysqli_query($link,"SELECT * FROM code WHERE code='$code' AND id_code=id_code") or die('Error connecting to MySQL server');
        $count = mysqli_num_rows($id_code);


        $servername = 'localhost';
        $username = 'root';
        $password = '';
        $dbname = 'useraccount';

        
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT expiration FROM code where code='$code'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            
            while($row = $result->fetch_assoc()) {
                echo "<div style='display: none;'>"."Expiration: " . $row["expiration"]. "<br>";
                echo $currentDate."<br></div>";
                if(($row["expiration"]) >($currentDate)){

                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $id;
                    $_SESSION["username"] = $username;                            
                    header("location: welcome.php");

                }
                else{
                    echo "<script>alert('EXPIRED CODE ERROR');</script>";
                }
            }
          } else {
            echo "<script>alert('WRONG CODE ERROR');</script>";
          }

          $conn->close();
    }
    
    mysqli_close($link);
}
?>
  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VERIFICATION</title>
    <link rel="stylesheet" href="css/style1.css">
    
   
</head>  
<body>
    
    <div class="header">
        <h2>VERIFY</h2>
    </div>
        
        <form role="form" method="post">
            <div>
                To continue, a verification is required.<br>
                Please enter the aunthentication code sent to your email.<br>
                
                <center>
                    <div class="input-group">
                        AUTHENTICATION CODE <input type="text" name="code" >  
                    </div>
                
                
            
                
                    <button type="submit" name="login" class="btn btn-primary">Enter</button><BR>
                    <br>
                    <a class=""  href="random.php" target="_blank"><u>GET YOUR CODE HERE!</u></a>
                </center>               
        </form>  
</body>
</html>


