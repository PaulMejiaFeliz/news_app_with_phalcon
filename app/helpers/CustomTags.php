<?php

namespace Newsapp\Helpers;

use Phalcon\Tag;
use Phalcon\Http\Request;
use Newsapp\Helpers\RequestHelper;

/**
 * Class used to display custom controls
 */
class CustomTags extends Tag
{
    private $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * return a set of navigation controls for a pagination
     *
     * @param int $totalPages
     * @param int $currentPage
     * @param int $pageLinksCount Maximun number of links in the pagination controls
     * @return string HTML representation off the navigation controls
     */
    public function pagination(int $totalPages, int $currentPage, int $pageLinksCount = 5) : string
    {
        if (!$totalPages) {
            return '';
        }

        $baseUrl = RequestHelper::removeFromQueryString(['page']) . '&page=';
        $hasPreviews = $currentPage == 1 ? 'disabled' : '';
        $hasNext = $currentPage == $totalPages ? 'disabled' : '';
        $linksMiddle = floor($pageLinksCount / 2);

        $html = "<a href='/{$baseUrl}1' class='btn btn-default {$hasPreviews}'><<</a>&nbsp;";
        $html .= "<a href='/{$baseUrl}" . ($currentPage - 1) . "' class='btn btn-default {$hasPreviews}'><</a>&nbsp;";

        $forIterator = ($totalPages > $pageLinksCount) ?  $totalPages - $pageLinksCount + 1 : 1;
        $forCondition = $totalPages;
        if ($currentPage > $pageLinksCount - $linksMiddle && $currentPage < $totalPages - $linksMiddle + 1) {
            $forIterator = $currentPage -  $linksMiddle;
            $forCondition = $currentPage + $linksMiddle;
        } elseif ($currentPage <= $totalPages - $pageLinksCount - $linksMiddle) {
            $forIterator = 1;
            $forCondition = $totalPages < $pageLinksCount ? $totalPages : $pageLinksCount;
        }

        for ($i = $forIterator; $i <= $forCondition; $i++) {
            $html .= "<a href='/{$baseUrl}{$i}' class='btn btn-default";
            $html .= ($i == $currentPage) ? ' disabled' : '';
            $html .= "'>{$i}</a>&nbsp;";
        }

        $html .= "<a href='/{$baseUrl}" . ($currentPage + 1) . "' class='btn btn-default {$hasNext}'>></a>&nbsp;";
        $html .= "<a href='/{$baseUrl}{$totalPages}' class='btn btn-default {$hasNext}'>>></a>&nbsp;";

        return $html;
    }

     /**
     * return an a tag for an order by column title
     *
     * @param string $label Labe of the a tag
     * @param string $value Name of the column to order by
     * @return string
     */
    public function orderByAnchor(string $label, string $value) : string
    {
        $order = [ 'order' => $value ];
        $icon = '';
        $order['reverseOrder'] = 'false';
        if ($this->request->hasQuery('order') && $this->request->getQuery('order') == $value) {
            if ($this->request->hasQuery('reverseOrder') && $this->request->getQuery('reverseOrder') == 'true') {
                $icon = '<i class=\'glyphicon glyphicon-arrow-up\'></i>';
            } else {
                $order['reverseOrder'] = 'true';
                $icon = '<i class=\'glyphicon glyphicon-arrow-down\'></i>';
            }
            
        }
        return '<a href=\'' . RequestHelper::addToQueryString($order) . "'>{$label} {$icon}</a>";
    }
}
