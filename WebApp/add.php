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

        //Inserts profile
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
        
        //Inserts positions
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

        // TODO: Inserts education

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
            <p>Education: <input type=submit id="addEdu" value="+">
                <div id="education_fields">
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

                    var source = $("#pos-template").html();
                    $('#position_fields').append(source.replaceAll("@COUNTPOS@", countPos));
                })
            });

            countEdu = 0;

            $(document).ready(function(){
                window.console && console.log('Document ready called');
                $('#addEdu').click(function(event) {
                    event.preventDefault();
                    if(countEdu >= 9){
                        alert("Maximum of nine education entries exceeded");
                        return;
                    }
                    countEdu++;
                    window.console && console.log("Adding position "+countEdu);

                    var source = $("#edu-template").html();
                    $('#education_fields').append(source.replaceAll("@COUNTEDU@", countEdu));
                })
            });
        </script>
    </div>
    <script id="pos-template" type="text">
        <div id="position@COUNTPOS@">
            <p>Year: <input type="text" name="year@COUNTPOS@" value="" >
                <input type="button" value="-"
                onclick="$('#position@COUNTPOS@').remove(); return false;"></p>
            <textarea name="desc@COUNTPOS@" rows="8" cols="80"></textarea>
        </div>
    </script>
    <script id="edu-template" type="text">
        <div id="position@COUNTEDU@">
            <p>Year: <input type="text" name="year@COUNTEDU@" value="" >
                <input type="button" value="-"
                onclick="$('#position@COUNTEDU@').remove(); return false;"></p>
            <textarea name="desc@COUNTEDU@" rows="8" cols="80"></textarea>
        </div>
    </script>
</body>