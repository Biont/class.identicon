<?php require 'class.identicon.php'; ?>

<html>
    <body>
        <form method="GET">
            <input type="text" name="string" value="<?=(isset($_GET['string'])?$_GET['string']:'Input string')?>">
            <input type="number" name="size" value="<?=(isset($_GET['size'])?$_GET['size']:256)?>">
            <input type="submit" name="identicon_submit">
        </form>



        <?php
        if (isset($_GET['identicon_submit'])) {
            $identicon = new Identicon($_GET['string'],(int)$_GET['size']);
            
            
            // Randomly rotate elements when symmetry is not wanted
            $identicon->random_rotation=true;
            
            // Call the image() method to receive a complete html element displaying the image
            echo $identicon->image();
            
            
            echo '<p>';
            
            // Directly echo the instance to get the data stream.
            // Since this is the second time the image is rendered, cached data will be served
            // When using random rotation, this is important
            echo '<img src="'.$identicon.'">';
        }
        ?>


    </body>
</html>
