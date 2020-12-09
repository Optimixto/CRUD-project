<?php
    session_start();
    require_once "pdo.php";

    // Logged in check
    if ( ! isset($_SESSION['name']) ) {
        die('Not logged in');
    }

    //Makes sure profile_id is present
    if ( !isset($_GET['profile_id']) ) {
        $_SESSION['error'] = "Missing profile_id";
        header('Location: index.php');
        return;
    }

    // Delete handler
    if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
        $sql = "DELETE FROM profile WHERE profile_id = :zip";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':zip' => $_POST['profile_id']));
        $_SESSION['success'] = 'Record deleted';
        header( 'Location: index.php' ) ;
        return;
    }

    if (isset($_POST['cancel']))
    {
        header( 'Location: index.php' ) ;
        return;
    }

    // Grabbing data from the database
    $stmt = $pdo->prepare("SELECT profile_id, first_name, last_name
                            FROM profile WHERE profile_id = :zip");
    $stmt->execute(array(":zip" => $_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ( $row === false ) {
        $_SESSION['error'] = 'Bad value for profile_id';
        header( 'Location: index.php' ) ;
        return;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Alejandro Garcia Muñoz (8c7a1f68) - Profiles DB</title>
    <?php include "headImports.php" ?>
</head>
<body>
    <div class="container">
        <h1>Deleting Profile</h1>
        <p><b>First Name: </b><?= htmlentities($row['first_name']) ?></p>
        <p><b>Last Name: </b><?= htmlentities($row['last_name']) ?></p>
        <p>Are you sure?</p>

        <form method="post">
        <input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
        <input type="submit" value="Delete" name="delete">
        <input type="submit" value="Cancel" name="cancel">
        </form>
    </div>
</body>