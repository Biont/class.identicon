<?php
require 'class.identicon.php';
?>

<html>
    <body>
        <form method="GET">
            <input type="text" name="string" value="inputstring">
            <input type="number" name="size" value="256">
            <input type="submit" name="identicon_submit">
        </form>



        <?php
        if (isset($_GET['identicon_submit'])) {
            $identicon = new Identicon($_GET['string'],$_GET['size']);
            echo $identicon->image();
            echo '<p>';
            echo '<img src="'.$identicon.'">';
        }
        ?>


    </body>
</html>
