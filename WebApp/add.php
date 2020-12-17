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

    // Input handler
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $msg = validateProfile();
        if(is_string($msg))
        {
            $_SESSION['error'] = $msg;
            header("Location: add.php");
            return;
        }

        $msg = validatePos();
        if(is_string($msg))
        {
            $_SESSION['error'] = $msg;
            header("Location: add.php");
            return;
        }

        // TODO: Validate education

        $stmt = $pdo->prepare('INSERT INTO Profile
            (user_id, first_name, last_name, email, headline, summary)
            VALUES ( :uid, :fn, :ln, :em, :he, :su)');

        $stmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'])
        );

        $profile_id = $pdo->lastInsertId();
        
        //Position entries insert
        $rank = 1;
        for($i=0; $i<=9; $i++)
        {
            if(!isset($_POST['year'.$i])) continue;
            if(!isset($_POST['desc'.$i])) continue;
            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];

            $stmt = $pdo->prepare('INSERT INTO Position
                (profile_id, rank, year, description)
                VALUES (:pid, :rank, :year, :desc)');
            $stmt->execute(array(
                ':pid' => $profile_id,
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $desc
            ));
            $rank++;
        }

        $_SESSION['success'] = "Record added";
        header("Location: index.php");
        return;
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Alejandro Garcia Muñoz (8c7a1f68) - users DB</title>
    <?php include "headImports.php" ?>
</head>
<body>
    <div class="container">
        <h1>Adding profile for <?php echo($_SESSION['name']);?></h1>
        <?php
            flashMessages();
        ?>
        <form method="post">
            <p>First Name:
            <input type="text" name="first_name" size="40"></p>
            <p>Last Name:
            <input type="text" name="last_name" size="40"></p>
            <p>Email:
            <input type="text" name="email" size="30"></p>
            <p>Headline:</p>
            <p><input type="text" name="headline" size="40"></p>
            <p>Summary:</p>
            <p><textarea name="summary" rows="8" cols="60"></textarea>
            <p>Position: <input type=submit id="addPos" value="+">
                <div id="position_fields">
                </div>
            </p>
            <p>
                <input type="submit" value="Add"/>
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