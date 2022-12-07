<?php
session_start();
require_once "pdo.php";
require_once "util.php";
require_once "head.php";

    $msg=false;
    $salt = 'XyZzy12*_';
    if(isset($_POST['email']) && isset($_POST['pass'])){
        unset($_SESSION['account']);                    //log out existing user
        if (strlen($_POST['email'])<1 || strlen($_POST['pass'])<1){
            $msg="User name and password are required";
            $_SESSION["error"]=$msg;
            header('Location: login.php');
            return;
        }
        else {
            $check = hash('md5', $salt.$_POST['pass']);
            $stmt = $pdo->prepare('SELECT user_id, name FROM users
            WHERE email = :em AND password = :pw');
            $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ( $row !== false ) {

                $_SESSION['account'] = $row['name'];
       
                $_SESSION['user_id'] = $row['user_id'];
       
                // Redirect the browser to index.php
       
                header("Location: index.php");
       
                return;}
            else {
                $msg="Incorrect password";
                $_SESSION["error"]=$msg;
                header('Location: login.php');
                return;  
            }   
    }
}
    


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PANAGIOTIS BALIS - Login Page</title>
    <style>
        p{color:red;}
        a#link{color:blue;
            text-decoration:none;
        }
        a#link:hover {color:blue;text-decoration:underline;
        }
    </style>
</head>
<body>
    <h1>Please Log In </h1>
    <?php flashMessages(); ?>
    <div>
        <form method="POST" action="login.php">
        <label for="user">Email</label>
        <input type="text" name="email" id="user"  size="20" value=""><br>
        <label for="pw">Password</label>
        <input type="text" name="pass" id="pw"  size="20" value=""><br>
        <input type="submit" onclick="return doValidate();" value="Log In" id="button">
        <a href="index.php" id="link">Cancel</a></form>
    </div>
    <script>
function doValidate() {
    console.log('Validating...');
    try {
        addr = document.getElementById('user').value;
        pw = document.getElementById('pw').value;
        console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</script>
</body>
</html>