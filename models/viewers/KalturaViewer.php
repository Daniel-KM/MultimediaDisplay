<?php
/**
 * Multimedia Display Kaltura Viewer
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * Multimedia Display Kaltura Viewer class
 * 
 */
class Mmd_Kaltura_Viewer extends Mmd_Abstract_Viewer
{

    public function __construct() {
        //parent::__construct();
        $this->setupParameters();
        $this->name = 'Kaltura';
        $this->defaultProfileName = 'kalturaDefault';
    }

     /**
     * Install default profile
     *
     * Install default item types, elements, and profiles for easy setup
     *
     * @return void
     */
    public function installDefaults($params) {
        $defaultParams = array(
            'typeName' => 'Kaltura Streaming Media',
            'typeDesc' => 'A video or audio file to be streamed from a Kaltura server',
            'profileName' => $this->defaultProfileName,
            'viewerName' => 'Kaltura'
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
                'name' => 'uiconfID',
                'label' => 'UI Configuration ID',
                'description' => 'The id of the UI configuration set up in Kaltura',
                'type' => 'string',
                //'value' => '',    //for enum type only
                'required' => 'true',
                'default' => '8129212'
            ),
            array(
                'name' => 'entryID',
                'label' => 'Entry ID',
                'description' => 'The entry id which identifies the item in Kaltura',
                'type' => 'string',
                //'value' => '',
                'required' => 'true',
                'default' => ''
            ),
            array(
                'name' => 'width',
                'label' => 'Width',
                'description' => 'The width of the Kaltura panel through which the public views the content of this document.',
                'type' => 'css',
                //'value' => '',
                'required' => 'false',
                'default' => '600px',
            ),
            array(
                'name' => 'height',
                'label' => 'Height',
                'description' => 'The height of the Kaltura panel through which the public views the content of this document.',
                'type' => 'css',
                //'value' => '',
                'required' => 'false',
                'default' => '400px',
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
    }

    /**
     * Retrieve body html
     *
     * Retrieves markup to include in the main content body of item show pages
     *
     * @return string Html to include in the header, 
     * linking to stylesheets and javascript libraries
     */
    public function getBodyHtml($params) {
?>

        <script type="text/javascript" src="http://www.kaltura.com/p/475671/sp/47567100/embedIframeJs/uiconf_id/<?php echo $params['uiconfID'];?>/partner_id/475671"></script>
        <object 
          id="kaltura_player_1337299051" 
          name="kaltura_player_1337299051" 
          type="application/x-shockwave-flash" 
          allowFullScreen="true" 
          allowNetworking="all" 
          allowScriptAccess="always" 
          height="<?php echo $params['height'];?>" 
          width="<?php echo $params['width'];?>" 
          bgcolor="#FFFFFF" 
          xmlns:dc="http://purl.org/dc/terms/" 
          xmlns:media="http://search.yahoo.com/searchmonkey/media/" 
          rel="media:video" 
          resource="http://www.kaltura.com/index.php/kwidget/cache_st/1337299051/wid/_475671/uiconf_id/<?php echo $params['uiconfID'];?>/entry_id/<?php echo $params['entryID'];?>" 
          data="http://www.kaltura.com/index.php/kwidget/cache_st/1337299051/wid/_475671/uiconf_id/<?php echo $params['uiconfID'];?>/entry_id/<?php echo $params['entryID'];?>"
        >
              <param name="allowFullScreen" value="true" />
              <param name="allowNetworking" value="all" />
              <param name="allowScriptAccess" value="always" />
              <param name="bgcolor" value="#000000" /> 
       <?php
          //<param name="flashVars" value="&{FLAVOR}" />//This might fail?? !>
          ?>
              <span property="media:width" content="<?php $params['width'];?>"></span>
              <span property="media:height" content="<?php $params['height'];?>"></span>
              <span property="media:type" content="application/x-shockwave-flash"></span>
        </object>
          <script>
       jQuery('#kaltura_player_1337299051').insertAfter(jQuery('#content').find('h1'));
          </script>

        <?php
        return ;
    }

}
