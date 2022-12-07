<?php
session_start();
require_once "pdo.php";
require_once "util.php";
require_once "head.php";

if (! isset($_GET['profile_id']) || strlen($_GET['profile_id'])<1)
{
    $_SESSION['error'] = 'Could not load profile';
    header('Location: index.php');
    return;
}

$stmt=$pdo->prepare("SELECT * FROM Profile WHERE profile_id= :xyz");
$stmt->execute(array(
    ':xyz'=>$_GET['profile_id']));
$row= $stmt->fetch(PDO::FETCH_ASSOC);
if( $row===false){
    $_SESSION['error']='Bad value for profile_id';
    header('Location: index.php');
    return;
} 

$positions = loadPos($pdo,$_REQUEST['profile_id']);
$schools=loadEdu($pdo, $_REQUEST['profile_id']);
?>



<html lang="en">
<head>
    <title>PANAGIOTIS BALIS VIEW PAGE</title>
</head>
<body>
<h1>Profile Information</h1>
    <p>First Name:<?=htmlentities($row['first_name'])?></p><br>
    <p>Last Name:<?=htmlentities($row['last_name'])?></p><br>
    <p>Email:<?=htmlentities($row['email'])?></p><br>
    <p>Headline:<?=htmlentities($row['headline'])?></p><br>
    <p>Summary:<?=htmlentities($row['summary'])?></p><br>

<?php
    echo('<p>Positions: '."\n");
    echo('<div id="position_fields">'."\n");
    echo('<ul>'."\n");

    foreach($positions as $position){
        echo('<li>'.$position['year'].':'.$position['description'].'</li>');
    }
    echo("</ul></div></p>\n");



    echo('<p>Educations: '."\n");
    echo('<div id="edu_fields">'."\n");
    echo('<ul>'."\n");
    foreach($schools as $school){
        echo('<li>'.$school['year'].':'.$school['name'].'</li>');
    }
    echo("</ul></div></p>\n");
  ?>
    <a href="index.php">Done</a>
    
</body>
</html>