<?php
/**
 * Multimedia Display Ohms Viewer
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * Multimedia Display Ohms Viewer class
 * 
 */
class MultimediaDisplay_Viewer_Ohms extends MultimediaDisplay_Viewer_Abstract
{
    
    public function __construct() {
        //parent::__construct();
        $this->setupParameters();
        $this->name = 'Ohms';
        $this->defaultProfileName = 'ohmsViewerDefault';
    }

    private function _filterCssParams($params,$indices) {
        foreach($indices as $index) {
            if(is_numeric($params[$index]))
                $params[$index] = $params[$index].'px';
            else if (is_object($params[$index]) && is_numeric($params[$index]->text))
                $params[$index] = $params[$index]->text.'px';
        }
        return $params;
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
            'typeName' => 'Synchonized Oral History',
            'typeDesc' => 'Oral history object synchronized with text, to be displayed using the OHMS viewer.',
            'profileName' => $this->defaultProfileName,
            'viewerName' => 'Ohms'
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
                'name' => 'width',
                'label' => 'Width',
                'description' => 'The width of the Ohms Viewer panel through which the public views the content of this book. Accepts syntax "250px", "250", or "70%".',
                'type' => 'css',
                //'value' => '',
                'required' => 'false',
                'default' => '100%'
            ),
            array(
                'name' => 'height',
                'label' => 'Height',
                'description' => 'The height of the Ohms Viewer panel through which the public views the content of this book. Accepts syntax "250px", "250", or "70%".',
                'type' => 'css',
                //'value' => '',
                'required' => 'false',
                'default' => '500px',
            ),
            array(
                'name' => 'cacheFileName',
                'label' => 'Cache File Name',
                'description' => 'The name of the cache file to load.',
                'type' => 'string',
                //'value' => '',
                'required' => 'true',
                'default' => '',
                'files' => 'xml'
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
        $configIni = physical_path_to('javascripts/ohmsviewer/config/config.ini');
        $config = parse_ini_file($configIni, true);

        if(empty($params['cacheFileName'])) {
            throw new Exception('Item cannot be displayed. No cache file specified for Ohms Viewer.');
            return;
        }

        $cachefile = is_array($params['cacheFileName']) ? $params['cacheFileName'][0] : $params['cacheFileName'];
        require_once physical_path_to('javascripts/ohmsviewer/lib/CacheFile.class.php');

        queue_css_file(array(
                'viewer',
                'jquery-ui.toggleSwitch',
                'jquery-ui-1.8.16.custom',
                'font-awesome',
                'jquery.fancybox',
                'jquery.fancybox-buttons',
                'jquery.fancybox-thumbs',
                'jplayer.blue.monday',
            ), 'all', false, 'javascripts/ohmsviewer/css');

        // Use the default location for css in view or theme directory.
        if (!empty($config['css'])) {
            queue_css_file($config['css']);
        }

        queue_js_file(array(
                'jquery',
                'jquery-ui',
            ), 'vendor');

        queue_js_file(array(
                'jquery-ui.toggleSwitch.js',
                'viewer_legacy.js',
                'jquery.jplayer.min.js',
                'jquery.easing.1.3.js',
                'jquery.scrollTo-min.js',
                'fancybox_2_1_5/source/jquery.fancybox.pack.js',
                'fancybox_2_1_5/source/helpers/jquery.fancybox-buttons.js',
                'fancybox_2_1_5/source/helpers/jquery.fancybox-media.js',
                'fancybox_2_1_5/source/helpers/jquery.fancybox-thumbs.js',
            ), 'javascripts/ohmsviewer/js');
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
        $params = $this->_filterCssParams($params,array('width','height'));
        if(empty($params['cacheFileName'])) {
            throw new Exception('Item cannot be displayed. No cache file specified for Ohms Viewer.');
            return;
        }

        $configIni = physical_path_to('javascripts/ohmsviewer/config/config.ini');
        $config = parse_ini_file($configIni, true);

        require_once physical_path_to('javascripts/ohmsviewer/lib/CacheFile.class.php');

        $cachefile = isset($params['cacheFileName'][0]) ? $params['cacheFileName'][0] : $params['cacheFileName'];
        $cachefile = isset($cachefile['path']) ? $cachefile['path'] : $cachefile;
        $cacheFile = CacheFile::getInstance($cachefile, FILES_DIR, $config);

        ob_start();
        ?>
        <script type="text/javascript">
		var jumpToTime = null;
		if(location.href.search('#segment') > -1)
		{
			var jumpToTime = parseInt(location.href.replace(/(.*)#segment/i, ""));
			if(isNaN(jumpToTime))
			{
				jumpToTime = 0;
			}
		}
	</script>
        <div id="audio-panel">
        <?php
        //include_once physical_path_to('javascripts/ohmsviewer/tmpl/player_' . $cacheFile->playername . '.tmpl.php');
        include_once physical_path_to('javascripts/ohmsviewer/tmpl/player_legacy.tmpl.php');
?>
        </div>
        <div id="ohms-main">
          <h2>Transcript</h2>
          <div id="ohms-main-panels">
            <div id="content-panel">
              <div id="transcript-panel">
                <?php echo $cacheFile->transcript; ?>
              </div>
              <div id="index-panel">
                <?php echo $cacheFile->index; ?>
              </div>
            </div>
            <div id="searchbox-panel">
              <?php include_once physical_path_to('javascripts/ohmsviewer/tmpl/search.tmpl.php'); ?>
            </div>
          </div>
        </div>
              
        <div style="clear:both; color:white; margin-top:30px;text-align:left;">
          <p>
<?php
              if($cacheFile->rights) {
                 echo '<span><h3>Rights Statement:</h3>';
                 echo $cacheFile->rights;
                 echo '</span>';
              }
?>
          </p>
          <p>
<?php
            if($cacheFile->usage) {
              echo '<span><h3>Usage Statement:</h3>';
              echo $cacheFile->usage;
              echo '</span>';
             } 
?>
          </p>
        </div>
            <script type="text/javascript">
            jQuery(document).ready(function() {

                jQuery('a.indexSegmentLink').on('click', function(e) {
                    var linkContainer = '#segmentLink' + jQuery(e.target).data('timestamp');

                    e.preventDefault();
                    if(jQuery(linkContainer).css("display") == "none")
                        {
                            jQuery(linkContainer).fadeIn(1000);
                        }
                    else
                        {
                            jQuery(linkContainer).fadeOut();
                        }
				
                    return false;
                });
		   
                jQuery('.segmentLinkTextBox').on('click', function() {
                    jQuery(this).select();
                });
	
                if(jumpToTime !== null)
                    {
                        jQuery('div.point').each(function(index) {
                            if(parseInt(jQuery(this).find('a.indexJumpLink').data('timestamp')) == jumpToTime)
                                {
                                    jumpLink = jQuery(this).find('a.indexJumpLink');
                                    jQuery('#accordionHolder').accordion({active: index});
                                    var interval = setInterval(function() {
						
                                        if(Math.floor(jQuery('#subjectPlayer').data('jPlayer').status.currentTime) == jumpToTime)  {
                                            clearInterval(interval);
                                        }
                                        else
                                            {
                                                jumpLink.click();
                                            }
                                    }, 500);
                                    jQuery(this).find('a.indexJumpLink').click();
                                }
                        });
                    }
        jQuery(".fancybox").fancybox();
        jQuery(".various").fancybox({
            //  maxWidth : width,
            // maxHeight : height,
            fitToView : false,
            width : '70%',
            height : '70%',
            autoSize : false,
            closeClick : false,
            openEffect : 'none',
            closeEffect : 'none'
            });
        jQuery('.fancybox-media').fancybox({
            openEffect : 'none',
            closeEffect : 'none',
            width : '80%',
            height : '80%',
            fitToView : true,
            helpers : {
      media : {}
    }
    });
        jQuery(".fancybox-button").fancybox({
      prevEffect : 'none',
            nextEffect : 'none',
            closeBtn : false,
            helpers : {
      title : { type : 'inside' },
            buttons : {}
    }
    });
    });

            var cachefile = '<?php echo $cacheFile->cachefile; ?>';
            jQuery('#content').find('h1').after(jQuery("#audio-panel"));
            jQuery("#audio-panel").after(jQuery('#ohms-main'));
      </script>
      <style>
        #ohms-main {
           width:<?php echo $params['width'];?>;
        }
        #ohms-main #transcript-panel {
           height:<?php echo $params['height'];?>;
        }

      </style>
<?php
        return ob_get_clean();
    }
}
