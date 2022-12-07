<?php
session_start();
require_once "pdo.php";
require_once "util.php";
require_once "head.php";
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PANAGIOTIS BALIS Welcome Page</title>
    <style>
        h1{color:black;}
        a#link{color:blue;
            text-decoration:none;
        }
        a#link:hover {color:blue;text-decoration:underline;
        }
    </style>
</head>
<body>
<h1>Panagiotis Balis's Resume Registry</h1>
<?php
flashMessages();

    if( !isset($_SESSION['user_id'])){
        echo('<p><a href="login.php" id="link">Please log in</a></p>');
        $stmt=$pdo->query("SELECT * FROM profile");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(count($rows) !==0){
                    //Echo table of profiles
                    echo('<table border="1"><thead><tr>');
                    echo('<th>Name</th><th>Headline</th>');
                    echo('</tr></thead>');
        foreach ($rows as $row) {
            $first_name = htmlentities($row['first_name']);
            $last_name = htmlentities($row['last_name']);
            $headline = htmlentities($row['headline']);
            $user_id = htmlentities($row['user_id']);
            $profile_id = htmlentities($row['profile_id']);

            echo('<tr><td><a href="view.php?profile_id=' . $row['profile_id'] . '">' . $first_name . ' ' . $last_name . '</td><td>' . $headline . '</a></td></tr>');
        }
        echo("</table>");}else{echo('No rows found');}
    }
    if( isset($_SESSION['user_id'])){
        echo('<p> <a href="logout.php" id="link">Logout</a></p>');
        $stmt=$pdo->query("SELECT * FROM profile");
        echo '<table border="1">'."\n";
        echo"<tr><th>Name</th><th>Headline</th><th>Action</th><tr>";
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            echo "<tr><td>";
            echo ('<a href="view.php?profile_id='.$row['profile_id'].'">'.$row['first_name'].''." ".''.$row['last_name'].'</a>');
            echo "</td><td>";
            echo (htmlentities($row['headline']));
            echo "</td><td>";
            echo ('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
            echo ('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
            echo("</td></tr>\n");
        }
        echo("</table>\n");
        echo('<a href="add.php" id="link">Add New Entry</a>');
    }?>


</body>
</html>