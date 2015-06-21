<?php
/**
 * Multimedia Display OpenSeaDragon Viewer
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * Multimedia Display OpenSeaDragon Viewer class
 * 
 */
class MultimediaDisplay_Viewer_OpenSeaDragon extends MultimediaDisplay_Viewer_Abstract
{

    public function __construct() {
        //parent::__construct();
        $this->setupParameters();
        $this->name = 'OpenSeaDragon';
        $this->defaultProfileName = 'openSeaDragonDefault';
    }

     /**
     * Install default profile
     *
     * Install default item types, elements, and profiles for easy setup
     *
     * @return void
     */
    public function installDefaults($params=null) {
        $defaultParams = array(
            'typeName' => 'Tiled Zoomable Image',
            'typeDesc' => 'An image file served in jpeg2000 or similar format, allowing for tiled display and deep zoom',
            'profileName' => $this->defaultProfileName,
            'viewerName' => 'OpenSeaDragon'
        );
        $params = empty($params) ? $defaultParams : $params;
        return parent::InstallDefaults($params,$this->_paramInfo);
    }

    /**
     * Set up parameters for this viewer
     *
     * @return void
     */
    public function setupParameters() {
        $this->_paramInfo = array(
            array(
                'name' => 'images',
                'label' => 'Image Location',
                'description' => 'The url of the directory storing the image to zoom into.',
                'type' => 'string',
                //'value' => '',
                'required' => 'true',
                'default' => '',
//                'files' => ''
            ),array(
                'name' => 'dvi',
                'label' => 'DVI Location',
                'description' => 'The url of the dvi file which defines the structure of the tiled images.',
                'type' => 'string',
                //'value' => '',
                'required' => 'true',
                'default' => '',
            ),
            array(
                'name' => 'width',
                'label' => 'Width',
                'description' => 'Width of the media display',
                'type' => 'css',
                //'value' => '',    //for enum type only
                'required' => 'false',
                'default' => '400px',
            ),
            array(
                'name' => 'height',
                'label' => 'Height',
                'description' => 'Height of the media display',
                'type' => 'css',
                //'value' => '',    //for enum type only
                'required' => 'false',
                'default' => '300px'
            )
        );
    }
    /**
     * Queue header scripts
     *
     * Queues script libraries and stylesheets to include in header
     *
     * @return null
     */
    public function viewerHead($params) {
        queue_js_file('openseadragon.min', 'javascripts/openseadragon');
    }

    /**
     * Retrieve body html
     *
     * Retrieves markup to include in the main content body of item show pages
     *
     * @return string Html to include in the header, 
     * linking to stylesheets and javascript libraries
     */
    public function getBodyHtml($params) 
    {
        ob_start();
        ?>
        <div id="osd-viewer" width="<?php echo $params['width']; ?>" height="<?php echo $params['height'];?>"></div>
        <script>
        jQuery('#osd-viewer').prependTo(jQuery('primary'));
        var viewer = OpenSeaDragon({
          id: "osd-viewer",
          prefixUrl: "<?php echo $params['images'];?>",
          tileSources: "<?php echo $params['dzi'];?>"
        });
        </script>
<?php
        return ob_get_clean();
    }
}
