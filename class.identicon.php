<?php

/**
 * 
 */
class Identicon {

    public static $size = 256;
    public $string;
    public $random_rotation = false;
    private $image;  //Cache the image here (so you get the same image even when using random rotation)
    private $resolution = 128;

// <editor-fold defaultstate="collapsed" desc="Identicon public functions">

    /**
     * Class constructor. Overrides the default values and calculates the sprite resolution
     * 
     * @param string $string
     * @param int $size
     */
    public function __construct($string, $size) {
        if (is_string($string)) {
            $this->string = md5($string);
        } else {
            error_log('Parameter $string is not a string. Aborting');
            return;
        }

        if (is_int($size)) {
            $this->size = $size;
            $this->resolution = $size / 2;
        } else {
            error_log('Parameter $size is not an integer value');
            return;
        }
    }

    /**
     * Outputs a base64 encoded stream of png image data
     * Should be used inside the src attribute of an HTML image tag
     * 
     * @return string
     */
    public function render() {
        $image = $this->get_image();
        ob_start();
        imagepng($image);
        return 'data:image/png;base64,' . base64_encode(ob_get_clean());
    }

    /**
     * Calls the render method so that the class instance can be directly echo'ed
     * 
     * @return string
     */
    public function __toString() {
        return $this->render();
    }

    /**
     * Outputs a fully working HTML image tag
     * 
     * @return string
     */
    public function image() {
        return '<img src="' . $this->render() . '">';
    }

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Identicon private functions">

    /**
     * If this identicon is using random rotation, return 90 multiplied by 0-3
     * Otherwise, return 90
     * @return int
     */
    private function get_rotation() {
        if ($this->random_rotation) {
            return 90 * mt_rand(0, 3);
        } else {
            return 90;
        }
    }

    /**
     * Checks if an image was already created and cached and creates a new one if not
     * 
     * @return image
     */
    private function get_image() {

        // Was this identicon already rendered?
        if (!$this->image) {
            /* parse hash string */

            $csh = hexdec(substr($this->string, 0, 1)); // corner sprite shape
            $ssh = hexdec(substr($this->string, 1, 1)); // side sprite shape
            $xsh = hexdec(substr($this->string, 2, 1)) & 7; // center sprite shape

            $cro = hexdec(substr($this->string, 3, 1)) & 3; // corner sprite rotation
            $sro = hexdec(substr($this->string, 4, 1)) & 3; // side sprite rotation
            $xbg = hexdec(substr($this->string, 5, 1)) % 2; // center sprite background

            /* corner sprite foreground color */
            $cfr = hexdec(substr($this->string, 6, 2));
            $cfg = hexdec(substr($this->string, 8, 2));
            $cfb = hexdec(substr($this->string, 10, 2));

            /* side sprite foreground color */
            $sfr = hexdec(substr($this->string, 12, 2));
            $sfg = hexdec(substr($this->string, 14, 2));
            $sfb = hexdec(substr($this->string, 16, 2));

            /* final angle of rotation */
            $angle = hexdec(substr($this->string, 18, 2));

            /* size of each sprite */
            $spriteZ = $this->resolution;

            /* start with blank 3x3 identicon */
            $identicon = imagecreatetruecolor($spriteZ * 3, $spriteZ * 3);
            imageantialias($identicon, TRUE);

            /* assign white as background */
            $bg = imagecolorallocate($identicon, 255, 255, 255);
            imagefilledrectangle($identicon, 0, 0, $spriteZ, $spriteZ, $bg);

            /* generate corner sprites */
            $corner = $this->get_sprite($csh, $cfr, $cfg, $cfb, $cro);
            imagecopy($identicon, $corner, 0, 0, 0, 0, $spriteZ, $spriteZ);
            $corner = imagerotate($corner, $this->get_rotation(), $bg);
            imagecopy($identicon, $corner, 0, $spriteZ * 2, 0, 0, $spriteZ, $spriteZ);
            $corner = imagerotate($corner, $this->get_rotation(), $bg);
            imagecopy($identicon, $corner, $spriteZ * 2, $spriteZ * 2, 0, 0, $spriteZ, $spriteZ);
            $corner = imagerotate($corner, $this->get_rotation(), $bg);
            imagecopy($identicon, $corner, $spriteZ * 2, 0, 0, 0, $spriteZ, $spriteZ);

            /* generate side sprites */
            $side = $this->get_sprite($ssh, $sfr, $sfg, $sfb, $sro);
            imagecopy($identicon, $side, $spriteZ, 0, 0, 0, $spriteZ, $spriteZ);
            $side = imagerotate($side, $this->get_rotation(), $bg);
            imagecopy($identicon, $side, 0, $spriteZ, 0, 0, $spriteZ, $spriteZ);
            $side = imagerotate($side, $this->get_rotation(), $bg);
            imagecopy($identicon, $side, $spriteZ, $spriteZ * 2, 0, 0, $spriteZ, $spriteZ);
            $side = imagerotate($side, $this->get_rotation(), $bg);
            imagecopy($identicon, $side, $spriteZ * 2, $spriteZ, 0, 0, $spriteZ, $spriteZ);

            /* generate center sprite */
            $center = $this->get_center($xsh, $cfr, $cfg, $cfb, $sfr, $sfg, $sfb, $xbg);
            imagecopy($identicon, $center, $spriteZ, $spriteZ, 0, 0, $spriteZ, $spriteZ);

            // $identicon=imagerotate($identicon,$angle,$bg);

            /* make white transparent */
            imagecolortransparent($identicon, $bg);

            /* create blank image according to specified dimensions */
            $resized = imagecreatetruecolor($this->size, $this->size);
            imageantialias($resized, TRUE);

            /* assign white as background */
            $bg = imagecolorallocate($resized, 255, 255, 255);
            imagefilledrectangle($resized, 0, 0, $this->size, $this->size, $bg);

            /* resize identicon according to specification */
            imagecopyresampled($resized, $identicon, 0, 0, (imagesx($identicon) - $spriteZ * 3) / 2, (imagesx($identicon) - $spriteZ * 3) / 2, $this->size, $this->size, $spriteZ * 3, $spriteZ * 3);

            /* make white transparent */
            //imagecolortransparent($resized,$bg);
            $this->image = $resized;
        }
        return $this->image;
    }

    /**
     * 
     * Creates a single sprite
     * 
     * 
     * @param type $shape
     * @param type $R
     * @param type $G
     * @param type $B
     * @param type $rotation
     * @return type
     */
    private function get_sprite($shape, $R, $G, $B, $rotation) {
        $spriteZ = $this->resolution;
        $sprite = imagecreatetruecolor($spriteZ, $spriteZ);
        imageantialias($sprite, TRUE);
        $fg = imagecolorallocate($sprite, $R, $G, $B);
        $bg = imagecolorallocate($sprite, 255, 255, 255);
        imagefilledrectangle($sprite, 0, 0, $spriteZ, $spriteZ, $bg);
        switch ($shape) {
            case 0: // triangle
                $shape = array(
                    0.5, 1,
                    1, 0,
                    1, 1
                );
                break;
            case 1: // parallelogram
                $shape = array(
                    0.5, 0,
                    1, 0,
                    0.5, 1,
                    0, 1
                );
                break;
            case 2: // mouse ears
                $shape = array(
                    0.5, 0,
                    1, 0,
                    1, 1,
                    0.5, 1,
                    1, 0.5
                );
                break;
            case 3: // ribbon
                $shape = array(
                    0, 0.5,
                    0.5, 0,
                    1, 0.5,
                    0.5, 1,
                    0.5, 0.5
                );
                break;
            case 4: // sails
                $shape = array(
                    0, 0.5,
                    1, 0,
                    1, 1,
                    0, 1,
                    1, 0.5
                );
                break;
            case 5: // fins
                $shape = array(
                    1, 0,
                    1, 1,
                    0.5, 1,
                    1, 0.5,
                    0.5, 0.5
                );
                break;
            case 6: // beak
                $shape = array(
                    0, 0,
                    1, 0,
                    1, 0.5,
                    0, 0,
                    0.5, 1,
                    0, 1
                );
                break;
            case 7: // chevron
                $shape = array(
                    0, 0,
                    0.5, 0,
                    1, 0.5,
                    0.5, 1,
                    0, 1,
                    0.5, 0.5
                );
                break;
            case 8: // fish
                $shape = array(
                    0.5, 0,
                    0.5, 0.5,
                    1, 0.5,
                    1, 1,
                    0.5, 1,
                    0.5, 0.5,
                    0, 0.5
                );
                break;
            case 9: // kite
                $shape = array(
                    0, 0,
                    1, 0,
                    0.5, 0.5,
                    1, 0.5,
                    0.5, 1,
                    0.5, 0.5,
                    0, 1
                );
                break;
            case 10: // trough
                $shape = array(
                    0, 0.5,
                    0.5, 1,
                    1, 0.5,
                    0.5, 0,
                    1, 0,
                    1, 1,
                    0, 1
                );
                break;
            case 11: // rays
                $shape = array(
                    0.5, 0,
                    1, 0,
                    1, 1,
                    0.5, 1,
                    1, 0.75,
                    0.5, 0.5,
                    1, 0.25
                );
                break;
            case 12: // double rhombus
                $shape = array(
                    0, 0.5,
                    0.5, 0,
                    0.5, 0.5,
                    1, 0,
                    1, 0.5,
                    0.5, 1,
                    0.5, 0.5,
                    0, 1
                );
                break;
            case 13: // crown
                $shape = array(
                    0, 0,
                    1, 0,
                    1, 1,
                    0, 1,
                    1, 0.5,
                    0.5, 0.25,
                    0.5, 0.75,
                    0, 0.5,
                    0.5, 0.25
                );
                break;
            case 14: // radioactive
                $shape = array(
                    0, 0.5,
                    0.5, 0.5,
                    0.5, 0,
                    1, 0,
                    0.5, 0.5,
                    1, 0.5,
                    0.5, 1,
                    0.5, 0.5,
                    0, 1
                );
                break;
            default: // tiles
                $shape = array(
                    0, 0,
                    1, 0,
                    0.5, 0.5,
                    0.5, 0,
                    0, 0.5,
                    1, 0.5,
                    0.5, 1,
                    0.5, 0.5,
                    0, 1
                );
                break;
        }
        /* apply ratios */
        for ($i = 0; $i < count($shape); $i++)
            $shape[$i] = $shape[$i] * $spriteZ;
        imagefilledpolygon($sprite, $shape, count($shape) / 2, $fg);
        /* rotate the sprite */
        for ($i = 0; $i < $rotation; $i++)
            $sprite = imagerotate($sprite, 90, $bg);
        return $sprite;
    }

    /**
     * Generates the center sprite
     * 
     * 
     * @param type $shape
     * @param type $fR
     * @param type $fG
     * @param type $fB
     * @param type $bR
     * @param type $bG
     * @param type $bB
     * @param type $usebg
     * @return type
     */
    private function get_center($shape, $fR, $fG, $fB, $bR, $bG, $bB, $usebg) {
        $spriteZ = $this->resolution;
        $sprite = imagecreatetruecolor($spriteZ, $spriteZ);
        imageantialias($sprite, TRUE);
        $fg = imagecolorallocate($sprite, $fR, $fG, $fB);
        /* make sure there's enough contrast before we use background color of side sprite */
        if ($usebg > 0 && (abs($fR - $bR) > 127 || abs($fG - $bG) > 127 || abs($fB - $bB) > 127))
            $bg = imagecolorallocate($sprite, $bR, $bG, $bB);
        else
            $bg = imagecolorallocate($sprite, 255, 255, 255);
        imagefilledrectangle($sprite, 0, 0, $spriteZ, $spriteZ, $bg);
        switch ($shape) {
            case 0: // empty
                $shape = array();
                break;
            case 1: // fill
                $shape = array(
                    0, 0,
                    1, 0,
                    1, 1,
                    0, 1
                );
                break;
            case 2: // diamond
                $shape = array(
                    0.5, 0,
                    1, 0.5,
                    0.5, 1,
                    0, 0.5
                );
                break;
            case 3: // reverse diamond
                $shape = array(
                    0, 0,
                    1, 0,
                    1, 1,
                    0, 1,
                    0, 0.5,
                    0.5, 1,
                    1, 0.5,
                    0.5, 0,
                    0, 0.5
                );
                break;
            case 4: // cross
                $shape = array(
                    0.25, 0,
                    0.75, 0,
                    0.5, 0.5,
                    1, 0.25,
                    1, 0.75,
                    0.5, 0.5,
                    0.75, 1,
                    0.25, 1,
                    0.5, 0.5,
                    0, 0.75,
                    0, 0.25,
                    0.5, 0.5
                );
                break;
            case 5: // morning star
                $shape = array(
                    0, 0,
                    0.5, 0.25,
                    1, 0,
                    0.75, 0.5,
                    1, 1,
                    0.5, 0.75,
                    0, 1,
                    0.25, 0.5
                );
                break;
            case 6: // small square
                $shape = array(
                    0.33, 0.33,
                    0.67, 0.33,
                    0.67, 0.67,
                    0.33, 0.67
                );
                break;
            case 7: // checkerboard
                $shape = array(
                    0, 0,
                    0.33, 0,
                    0.33, 0.33,
                    0.66, 0.33,
                    0.67, 0,
                    1, 0,
                    1, 0.33,
                    0.67, 0.33,
                    0.67, 0.67,
                    1, 0.67,
                    1, 1,
                    0.67, 1,
                    0.67, 0.67,
                    0.33, 0.67,
                    0.33, 1,
                    0, 1,
                    0, 0.67,
                    0.33, 0.67,
                    0.33, 0.33,
                    0, 0.33
                );
                break;
        }
        /* apply ratios */
        for ($i = 0; $i < count($shape); $i++)
            $shape[$i] = $shape[$i] * $spriteZ;
        if (count($shape) > 0)
            imagefilledpolygon($sprite, $shape, count($shape) / 2, $fg);
        return $sprite;
    }

    // </editor-fold>
}

?>
