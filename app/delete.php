<?php
require_once "pdo.php";
session_start();
if (!isset($_GET['profile_id'])){
    $_SESSION['error']='Missing profile_id';
    header('Location: index.php');
    return;
}

if (isset($_POST['delete']) && isset($_POST['profile_id'])){
    $sql="DELETE FROM profile WHERE profile_id= :zip ";
    $stmt=$pdo->prepare($sql);
    $stmt->execute(array(
        ':zip'=>$_POST['profile_id']));
    $_SESSION['success']="Profile deleted";
    header('Location: index.php');
    return;
}





$stmt=$pdo->prepare("SELECT * FROM profile WHERE profile_id= :xyz");
$stmt->execute(array(
    ':xyz'=>$_GET['profile_id']));
$row= $stmt->fetch(PDO::FETCH_ASSOC);
if( $row===false){
    $_SESSION['error']='Bad value for profile_id';
    header('Location: index.php');
    return;
} 

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PANAGIOTIS BALIS Delete Page</title>
    <style>
    </style>
</head>
<body>
    <h1>Deleting Profile</h1>
<p>First Name: <?=htmlentities($row['first_name'])?> </p>
<p>Last Name: <?=htmlentities($row['last_name'])?> </p>
<form method="post">
<input type="hidden" name="profile_id" value="<?php echo $row['profile_id'] ?>">
<input type="submit" name="delete" value="Delete">
<a href="index.php">Cancel</a>
</form>
</body>
</html>