<?php
require_once "pdo.php";
require_once "util.php";
require_once "head.php";
session_start();
if(! isset($_SESSION["user_id"])){
    die("ACCESS DENIED");
}
if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

if( !isset($_REQUEST['profile_id'])){
    $_SESSION['error']="Missing profile_id";
    header('Location: index.php');
    return;
}

//Load up the profile in question
$stmt=$pdo->prepare("SELECT * FROM profile WHERE profile_id= :xyz AND user_id= :uid");
$stmt->execute(array(
    ':xyz'=>$_REQUEST['profile_id'],
    ':uid'=>$_SESSION['user_id']));
$row= $stmt->fetch(PDO::FETCH_ASSOC);
if( $row===false){
    $_SESSION['error']='Could not load profile';
    header('Location: index.php');
    return;
} 
//handle incoming data
if (isset($_POST['first_name'])  && isset($_POST['last_name']) && isset($_POST['email']) &&
    isset($_POST['headline']) && isset($_POST['summary'])){
    $msg=ValidateProfile();
    if ( is_string($msg)){
        $_SESSION["error"]=$msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    }
    //validate position entries
    $msg=ValidatePos();
    if( is_string($msg)){
        $_SESSION['error']=$msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    }

    //validate education
    $msg=ValidateEdu();
    if( is_string($msg)){
        $_SESSION['error']=$msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    }


    $sql= "UPDATE profile SET  user_id= :user_id, first_name= :first_name,
     last_name= :last_name, email= :email, headline= :headline, summary= :summary
      WHERE profile_id= :profile_id AND user_id= :user_id";
    $stmt=$pdo->prepare($sql);
    $stmt->execute(array(
        ':user_id'=>$_SESSION['user_id'],
        ':first_name'=>$_POST['first_name'],
        ':last_name'=>$_POST['last_name'],
        ':email'=>$_POST['email'],
        ':headline'=>$_POST['headline'],
        ':summary'=>$_POST['summary'],
        ':profile_id'=>$_REQUEST['profile_id']));


//clear out old position entries
    $stmt=$pdo->prepare('DELETE FROM Position WHERE profile_id= :pid');
    $stmt->execute(array( ':pid'=>$_REQUEST['profile_id']));
    
    //Insert position entries
    insertPositions($pdo,$_REQUEST['profile_id']);

    //clear out old education entries
    $stmt=$pdo->prepare('DELETE FROM Education WHERE profile_id= :pid');
    $stmt->execute(array( ':pid'=>$_REQUEST['profile_id']));

    //Insert education entries
    insertEducations($pdo,$_REQUEST['profile_id']);

     $success="Profile updated";
     $_SESSION["success"]=$success;
     header('Location: index.php');
     return;

$stmt=$pdo->prepare("SELECT * FROM profile WHERE profile_id= :xyz");
$stmt->execute(array(
    ':xyz'=>$_GET['profile_id']));
$row= $stmt->fetch(PDO::FETCH_ASSOC);
if( $row===false){
    $_SESSION['error']='Bad value for user_id';
    header('Location: index.php');
    return;
} 

    }
$positions=loadPos($pdo, $_REQUEST['profile_id']);
$schools=loadEdu($pdo, $_REQUEST['profile_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PANAGIOTIS BALIS Edit Page</title>
    <script type="text/javascript" ></script>
    <style>
    </style>
</head>
<body>
    <h1>Editing Profile for <?= htmlentities($_REQUEST['profile_id']); ?> </h1>
    <?php flashMessages(); ?>
    <div>
    <form method="post" action="edit.php">
    <input type="hidden" name="profile_id" value="<?=htmlentities($_REQUEST['profile_id']); ?>"/>
    <p>First Name:<input type="text" name="first_name" size="60" value="<?=htmlentities($row['first_name'])?>"/></p>
    <p>Last Name:<input type="text" name="last_name" size="60" value="<?=htmlentities($row['last_name'])?>"/></p>
    <p>Email:<input type="text" name="email" size="20" value="<?=htmlentities($row['email'])?>"/></p>
    <p>Headline:<br><input type="text" name="headline" size="60" value="<?=htmlentities($row['headline'])?>"/></p>
    <p>Summary:<br><textarea name="summary" rows="10" cols="60"><?=htmlentities($row['summary'])?></textarea></p>


    <?php
    $countEdu=0;

    echo('<p>Education: <input type="submit" id="addEdu" value="+">'."\n");
    echo('<div id="edu_fields">'."\n");
    if (count($schools)>0){
        foreach($schools as $school){
            $countEdu++;
            echo('<div id="edu'.$countEdu.'">');
            echo '<p>Year: <input type="text" name="edu_year'.$countEdu.'" value="'.$school['year'].'" />
            <input type="button" value="-" onclick="$(\'#edu'.$countEdu.'\').remove();return false;"></p>
            <p>School: <input type="text" size="80" name="edu_school'.$countEdu.'" class="school"
            value="'.htmlentities($school['name']).'" />';
            echo "\n</div>\n";
        }
       
    }
    echo("</div></p>\n");

    $pos=0;
    echo('<p>Position: <input type="submit" value="+" id="addPos">'."\n");
    echo('<div id="position_fields">'."\n");
    foreach($positions as $position){
        $pos++;
        echo('<div id="position'.$pos.'">'."\n");
        echo('<p>Year:<input type="text" name="year'.$pos.'"');
        echo('value="'.htmlentities($position['year']).'"/>'."\n");
        echo('<input type="button" value="-" ');
        echo('onclick="$(\'#position'.$pos.'\').remove();return false;">'."\n");
        echo("</p>\n");
        echo('<textarea name="desc'.$pos.'" rows="8" cols="80">'."\n");
        echo(htmlentities($position['description'])."\n");
        echo("\n</textarea>\n</div>\n");
    }
    echo("</div></p>\n");
    ?>
    <input type="submit" name="save" value="Save"> <input type="submit" name="cancel" value="Cancel">
    </form>
    </div>
<script>
countPos= <?=$pos ?>;
countEdu= <?=$countEdu ?>;

$(document).ready(function(){
    console.log('Document ready called');

    $('#addPos').click(function(event){
        event.preventDefault();
        if( countPos>=9){
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        console.log("Adding position" +countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'">\
            <p>Year: <input type="text" name="year'+countPos+'" value=""/>\
            <input type="button" value="-"\
            onclick="$(\'#position' +countPos+'\').remove();return false;"></p>\
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });

        $('#addEdu').click(function(event){
        event.preventDefault();
        if( countEdu>=9){
            alert("Maximum of nine education entries exceeded");
            return;
        }
        countEdu++;
        console.log("Adding education" +countEdu);
        $('#edu_fields').append(
            '<div id="edu'+countEdu+'">\
            <p>Year: <input type="text" name="edu_year'+countEdu+'" value=""/>\
            <input type="button" value="-"\
            onclick="$(\'#edu' +countEdu+'\').remove();return false;"></p>\
            <p>School: <input type="text" size="80" name="edu_school'+countEdu+ '"\
            class="school" value="" /></p></div>');
        });
        //Add event handler to new ones
        $('.school').autocomplete({
            source: "school.php"
        });
});
    </script>

 


</body>
</html>