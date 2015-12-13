<?php
/**
 * Multimedia Display Embedded Content Viewer
 * 
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * Multimedia Display Embedded Content Viewer class
 * 
 */
class MultimediaDisplay_Viewer_Embed extends MultimediaDisplay_Viewer_Abstract
{
    public function display()
    {
        return 'test display';
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

    }

    /**
     * Queue header scripts
     *
     * Queues script libraries and stylesheets to include in header
     *
     * @return null
     */
    public function viewerHead($params)
    {

    }
}
