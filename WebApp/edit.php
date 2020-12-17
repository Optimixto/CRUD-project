<?php
    session_start();
    require_once "pdo.php";
    include_once "util.php";
    
    // Logged in check
    if ( ! isset($_SESSION['name']) ) {
        die('ACCESS DENIED');
    }

    //Cancel handler
    if ( isset($_POST['cancel']) ) {
        header('Location: index.php');
        return;
    }
    
    //Makes sure profile_id is present
    if ( !isset($_GET['profile_id']) ) {
        $_SESSION['error'] = "Missing profile_id";
        header('Location: index.php');
        return;
    }

    //Grabs data to fill form
    $row = loadProfile($pdo, $_GET['profile_id']);
    if ( $row === false )
    {
        $_SESSION['error'] = 'Bad value for profile_id';
        header('Location: index.php');
        return;
    }

    // Input handler
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $msg = validateProfile();
        if(is_string($msg))
        {
            $_SESSION['error'] = $msg;
            header("Location: edit.php?profile_id=".$_GET['profile_id']);
            return;
        }

        $msg = validatePos();
        if(is_string($msg))
        {
            $_SESSION['error'] = $msg;
            header("Location: add.php");
            return;
        }

        $sql = "UPDATE profile SET first_name = :fn,
                last_name =:ln, email =:em, headline =:he, summary =:su WHERE profile_id = :pid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'],
            ':pid' => $_GET['profile_id'])
        );

        //Clear position entries
        $stmt = $pdo->prepare('DELETE FROM Position
            WHERE profile_id = :pid');
        $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
        
        //from add.php, we insert the new data
        $rank = 1;
        for($i=1; $i<=9; $i++)
        {
            if(!isset($_POST['year'.$i])) continue;
            if(!isset($_POST['desc'.$i])) continue;
            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];

            $stmt = $pdo->prepare('INSERT INTO Position
                (profile_id, rank, year, description)
                VALUES (:pid, :rank, :year, :desc)');
            $stmt->execute(array(
                ':pid' => $_REQUEST['profile_id'],
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $desc
            ));
            $rank++;
        }

        $_SESSION['success'] = "Profile updated";
        header("Location: index.php");
        return;
    }

    //Load positions data
    $positions = loadPos($pdo, $_REQUEST['profile_id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Alejandro Garcia Muñoz (8c7a1f68) - profiles DB</title>
    <?php include "headImports.php" ?>
</head>
<body>
    <div class="container">
        <h1>Editing profile for <?php echo($_SESSION['name']);?></h1>
        
        <h2>Editing usermobile</h2>
        <?php
            flashMessages();

            //Placeholder vars for current values
            $fn = htmlentities($row['first_name']);
            $ln = htmlentities($row['last_name']);
            $em = htmlentities($row['email']);
            $he = htmlentities($row['headline']);
            $su = htmlentities($row['summary']);
            $user_id = $row['user_id'];
            $profile_id = $_GET['profile_id'];
        ?>

        <form method="post">
            <p><b>First Name:</b>
            <input type="text" name="first_name" size="40" value="<?= $fn ?>"></p>
            <p><b>Last Name:</b>
            <input type="text" name="last_name" size="40" value="<?= $ln ?>"></p>
            <p><b>Email:</b>
            <input type="text" name="email" size="30" value="<?= $em ?>"></p>
            <p><b>Headline:</b></p>
            <p><input type="text" name="headline" size="40" value="<?= $he ?>"></p>
            <p><b>Summary:</b></p>
            <p><textarea name="summary" rows="8" cols="60"><?= $su ?></textarea>
            <?php
                $pos = 0;
                echo('<p>Position: <input type=submit id="addPos" value="+">');
                echo('<div id="position_fields">'."\n");

                foreach($positions as $position)
                {
                    $pos++;
                    echo('<div id="position'.$pos.'">'."\n");
                    echo('<p>Year: <input type="text" name="year'.$pos.'"');
                        echo('value="'.$position['year'].'" />'."\n");
                    echo('<input type="button" value="-"');
                        echo('onclick="$(\'#position'.$pos.'\').remove(); return false;">'."\n");
                    echo("</p>\n");
                    echo('<textarea name="desc'.$pos.'" rows="8" cols="80">'."\n");
                    echo(htmlentities($position['description'])."\n");
                    echo("\n</textarea>\n</div>\n");
                }

                echo('</div>');
            ?>
            </p>
            <p>
                <input type="submit" value="Save"/>
                <input type="submit" name="cancel" value="Cancel"/>
            </p>
        </form>
        <script>
            countPos = 0;

            $(document).ready(function(){
                window.console && console.log('Document ready called');
                $('#addPos').click(function(event) {
                    event.preventDefault();
                    if(countPos >= 9){
                        alert("Maximum of nine position entries exceeded");
                        return;
                    }
                    countPos++;
                    window.console && console.log("Adding position "+countPos);
                    $('#position_fields').append(
                        '<div id="position'+countPos+'"> \
                        <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                        <input type="button" value="-" \
                            onclick="$(\'#position' +countPos+'\').remove(); return false;"></p>\
                        <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                    </div>');
                })
            });
        </script>
    </div>
</body>