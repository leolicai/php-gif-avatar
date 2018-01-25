<?php
/**
 * IndexController.php
 *
 */

namespace Application\Controller;


use Zend\Http\Response;

class IndexController extends AppController
{

    private function imageAlphaMask( &$picture, $mask ) {

        // Get sizes and set up new picture
        $xSize = imagesx( $picture );
        $ySize = imagesy( $picture );
        $newPicture = imagecreatetruecolor( $xSize, $ySize );
        imagesavealpha( $newPicture, true );
        imagefill( $newPicture, 0, 0, imagecolorallocatealpha( $newPicture, 0, 0, 0, 127 ) );

        // Resize mask if necessary
        if( $xSize != imagesx( $mask ) || $ySize != imagesy( $mask ) ) {
            $tempPic = imagecreatetruecolor( $xSize, $ySize );
            imagecopyresampled( $tempPic, $mask, 0, 0, 0, 0, $xSize, $ySize, imagesx( $mask ), imagesy( $mask ) );
            imagedestroy( $mask );
            $mask = $tempPic;
        }

        // Perform pixel-based alpha map application
        for( $x = 0; $x < $xSize; $x++ ) {
            for ($y = 0; $y < $ySize; $y++) {
                $alpha = imagecolorsforindex($mask, imagecolorat($mask, $x, $y));
                $alpha = $alpha['alpha'];
                $color = imagecolorsforindex($picture, imagecolorat($picture, $x, $y));
                //preserve alpha by comparing the two values
                if ($color['alpha'] > $alpha)
                    $alpha = $color['alpha'];
                //kill data for fully transparent pixels
                if ($alpha == 127) {
                    $color['red'] = 0;
                    $color['blue'] = 0;
                    $color['green'] = 0;
                }
                imagesetpixel($newPicture, $x, $y, imagecolorallocatealpha($newPicture, $color['red'], $color['green'], $color['blue'], $alpha));
            }
        }

        // Copy back to original picture
        imagedestroy( $picture );
        $picture = $newPicture;
    }

    public function indexAction()
    {

        $response = $this->getResponse();
        if (!$response instanceof Response) {
            $response = new Response();
        }

        //header("Content-Type: image/png");

        $tplUri = APP_BOOTSTRAP_PATH . DIRECTORY_SEPARATOR . 'girl.jpg';
        $maskUri = APP_BOOTSTRAP_PATH. DIRECTORY_SEPARATOR . 'mask.png';
        $outUri = APP_BOOTSTRAP_PATH . DIRECTORY_SEPARATOR . 'output.png';

        /** Case 1:
        $source = imagecreatefromjpeg($tplUri);
        $mask = imagecreatefrompng($maskUri);
        $this->imageAlphaMask($source, $mask);
        imagepng($source);
        imagedestroy($source);
        imagedestroy($mask);
        //*/

        /** Case 2
        $base = new \Imagick($tplUri);
        $mask = new \Imagick($maskUri);

        $base->resizeImage(750, 750, \Imagick::FILTER_LANCZOS, 1);

        $base->compositeImage($mask, \Imagick::COMPOSITE_COPYOPACITY, 0, 0);
        $base->setImageFormat('png');

        $base->writeImage($outUri);
        $mask->destroy();
        echo $base->getImageBlob();
        $base->destroy();
        //*/

        //phpinfo();

        return $response;

    }

}