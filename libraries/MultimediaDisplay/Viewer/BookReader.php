<?php
/**
 * Multimedia Display BookReader Viewer
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * Multimedia Display BookReader Viewer class
 * 
 */
class MultimediaDisplay_Viewer_BookReader extends MultimediaDisplay_Viewer_Abstract
{

    public function __construct() {
        //parent::__construct();
        $this->setupParameters();
        $this->name = 'BookReader';
        $this->defaultProfileName = 'bookReaderDefault';
    }

     /**
     * Install default profile
     *
     * Install default item types, elements, and profiles for easy setup
     *
     * @return void
     */
    public function installDefaults($params = null) {
        $defaultParams = array(
            'typeName' => 'eBook',
            'typeDesc' => 'A digital representation of a bound, paged book, to be displayed using Internet Archive Book Reader. The content of this book may be stored either in Omeka or on the Internet Archive.',
            'profileName' => $this->defaultProfileName,
            'viewerName' => 'BookReader'
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
                'name' => 'url',
                'label' => 'Url',
                'description' => 'The URL associated with the content of this book. If this parameter is not set, Omeka will look for the pages of the book as files attached to this item.',
                'type' => 'string',
                //'value' => '',
                'required' => 'false',
                'default' => '',
                'files' => 'bmp,jpg,jpeg,gif,png',
            ),
            array(
                'name' => 'width',
                'label' => 'Width',
                'description' => 'The width of the BookReader panel through which the public views the content of this book.',
                'type' => 'css',
                //'value' => '',
                'required' => 'false',
                'default' => '800px',
            ),
            array(
                'name' => 'height',
                'label' => 'Height',
                'description' => 'The height of the BookReader panel through which the public views the content of this book.',
                'type' => 'css',
                //'value' => '',
                'required' => 'false',
                'default' => '600px',
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
        if(is_array($params['url'])) {
            queue_css_file(array(
                    'BookReader',
                    'BookReaderEmbed',
                ), 'all', false, 'javascripts/bookreader');

            queue_js_file(array(
                    'jquery.min',
                    'jquery-ui-1.8.5.custom.min',
                    'dragscrollable',
                    'jquery.colorbox-min',
                    'jquery.ui.ipad',
                    'jquery.bt.min',
                    'BookReader',
                ), 'javascripts/bookreader');
        }
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
        if(!is_array($params['url'])){
            $split = explode('archive.org/stream/',$params['url']);
            if(count($split)>1)
                $url = $split[1];
            $split = explode('#',$url);
            if(count($split)>1)
                $url = $split[0];
            
            ?>
            <div id="bookreader-div">
            <iframe src="http://www.archive.org/stream/<?php echo $url;?>#mode/2up?ui=embed" width="<?php echo $params['width'];?>" height="<?php echo $params['height'];?>"></iframe>
            <?php 
            //'.$params[''].'
            ?></div>
        <script type="text/javascript">
        jQuery('#content').find('h1').after(jQuery('#bookreader-div'));
        </script>
            <?php
            return;
        }

        // Build the BookReader from files of the item.
        $brDir = WEB_PLUGIN . '/MultimediaDisplay/views/shared/javascripts/bookreader/';
        $urls = array();
        // Width and Height can't be set, because item is unknown,
        // so it will be forced below.
        // $pageWidths = array();
        // $pageHeights= array();
        // $pageNums = array();
        // $pageLabels = array();
        foreach ($params['url'] as $url) {
            $urls[] = $url['url'];
        }
?>
    <style>
        #BookReader {
            padding-bottom: 20px !important;
            position: relative;
            overflow: hidden;
            height: <?php echo $params['height']; ?>;
            max-height: <?php echo $params['height']; ?>;
            max-width: <?php echo $params['width']; ?>;
            width: <?php echo $params['width']; ?>;
        }
        div#BRnav {
            position: absolute;
        }
    </style>

    <div id="BookReader"></div>
    <script type="text/javascript">
        jQuery('#content').find('h1').after(jQuery('#BookReader'));

        br = new BookReader();

        var urls = <?php echo json_encode($urls); ?>;
    <?php /*
        // br.pageWidths = <?php echo json_encode($pageWidths); ?>;
        // br.pageHeights = <?php echo json_encode($pageHeights); ?>;
        // br.pageNums = <?php echo json_encode($pageNums); ?>;
        // br.pageLabels = <?php echo json_encode($pageLabels); ?>;
    */ ?>
        // TODO Use the title of the item only.
        br.bookTitle= $('title').text();
        br.bookUrl  = <?php echo json_encode(WEB_ROOT); ?>;
        br.numLeafs = urls.length;
        br.imagesBaseURL = <?php echo json_encode($brDir . 'images/'); ?>;

        // bookreader("bookreader", ArchiveBook("tomsawyer"));
        br.getPageWidth = function(index) {
            // TODO Return the true width or a default.
            return <?php echo (integer) $params['width']; ?>;
        }
        br.getPageHeight = function(index) {
            // TODO Return the true height or a default.
            return <?php echo (integer) $params['height']; ?>;
        }
        br.getPageURI = function(index, reduce, rotate) {
            url = urls[index];
            return url;
        }
        br.getPageSide = function(index) {
            if (0 == (index & 0x1)) {
                return 'R';
            } else {
                return 'L';
            }
        }
        br.canRotatePage = function(index) {
            return false;
        }
        br.getPageNum = function(index) {
            return index+1;
        }
        br.getSpreadIndices = function(pindex) {
            var spreadIndices = [null, null];
            if ('rl' == this.pageProgression) {
                // Right to Left
                if (this.getPageSide(pindex) == 'R') {
                    spreadIndices[1] = pindex;
                    spreadIndices[0] = pindex + 1;
                } else {
                    // Given index was LHS
                    spreadIndices[0] = pindex;
                    spreadIndices[1] = pindex - 1;
                }
            } else {
                // Left to right
                if (this.getPageSide(pindex) == 'L') {
                    spreadIndices[0] = pindex;
                    spreadIndices[1] = pindex + 1;
                } else {
                    // Given index was RHS
                    spreadIndices[1] = pindex;
                    spreadIndices[0] = pindex - 1;
                }
            }

            return spreadIndices;
        }
        br.buildInfoDiv = function(jInfoDiv) {
        }
        br.getEmbedURL = function(viewParams) {
            var url = <?php echo json_encode(WEB_ROOT) /* TODO Use the item url. */; ?>;
            return url;
        }
        br.getEmbedCode = function(frameWidth, frameHeight, viewParams) {
            return "<iframe src='" + this.getEmbedURL(viewParams) + "' width='" + frameWidth + "' height='" + frameHeight + "' frameborder='0' ></iframe>";
        }

        br.init();

        $('#BRtoolbar').find('.read').hide();
        $('#BRreturn').html($('#BRreturn').text());
        $('#textSrch').hide();
        $('#btnSrch').hide();

        </script>
<?php
    }
}
