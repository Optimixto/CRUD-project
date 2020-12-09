<?php
    session_start();
    require_once "pdo.php";
    include_once "util.php";

    // Grabbing data from the database
    $stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id FROM profile");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Alejandro Garcia Muñoz (8c7a1f68) - users DB</title>
    <?php include "headImports.php" ?>
</head>
<body>
    <div class="container">
        <h1>Alex GM's Resume Registry</h1>
        <?php
            flashMessages();
            
            if(!isset($_SESSION['name']))
                {
                echo("<p>");
                    echo("<a href='login.php'>Please log in</a>");
                echo("</p>");
            }
            else{
                echo("<p><a href='logout.php'>Logout</a>");
            }
            
            //table
            if(count($rows)>0)
            {
                echo("<h2>Current profiles:</h2>");
                echo('<table border="1">');
                echo "<tr><td>";
                    echo('Name');
                    echo("</td><td>");
                    echo('Headline');
                    echo("</td>");
                    
                    if(isset($_SESSION['name']))
                    {
                        echo("<td>");
                        echo("Actions");
                        echo("</td></tr>");
                    }
                    else
                        echo("</tr>");

                foreach ( $rows as $row ) {
                    echo ('<tr><td><a href="view.php?profile_id='.$row['profile_id'].'" >');
                    echo(htmlentities($row['first_name']).' '.htmlentities($row['last_name']));
                    echo("</a></td><td>");
                    echo(htmlentities($row['headline']));
                    echo("</td>");
                    if(isset($_SESSION['name']))
                    {                    
                        echo("<td>");
                        echo('<form method="post"><input type="hidden" ');
                        echo('name="profile_id" value="'.$row['profile_id'].'">'."\n");
                        echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
                        echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
                        echo("\n</form>\n");
                        echo("</td></tr>\n");
                    }
                }
                echo("</table>");
            }
            else
            {
                echo('<p>No rows found</p>');
            }

            if(isset($_SESSION['name']))
            {
                echo(
                    "<p><a href='add.php'>Add New Entry</a></p>");
            }
            else
            {
                echo("<p>");
                    echo("Attempt to ");
                    echo("<a href='add.php'>add data</a> without logging in - it should fail with an error message.");
                echo("</div>");
            }
            ?>
    </div>
</body>