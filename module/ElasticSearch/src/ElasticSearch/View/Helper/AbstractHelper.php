<?php
/**
 * AbstractHelper.php
 *
 * PHP Version 7
 *
 * Copyright (C) swissbib 2018
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
 *
 * @category VuFind
 * @package  ElasticSearch\View\Helper
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace ElasticSearch\View\Helper;

use ElasticSearch\VuFind\RecordDriver\ElasticSearch as ElasticSearch;
use VuFind\Search\Base\Results as Results;
use Zend\Config\Config as ZendConfig;

/**
 * Class AbstractHelper
 *
 * Abstract view helper that implements some utilities commonly required for
 * several views.
 *
 * @category VuFind
 * @package  ElasticSearch\View\Helper
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
abstract class AbstractHelper extends \Zend\View\Helper\AbstractHelper
{
    private $_driver;

    /**
     * Returns driver
     *
     * @return ElasticSearch
     */
    public function getDriver()
    {
        return $this->_driver;
    }

    /**
     * Sets driver
     *
     * @param ElasticSearch $_driver The driver
     *
     * @return void
     */
    protected function setDriver(ElasticSearch $_driver = null)
    {
        $this->_driver = $_driver;
    }

    /**
     * The name of the underlying record driver to be rendered.
     *
     * @return string|null
     */
    abstract public function getDisplayName();

    /**
     * The type of data this helper handles. Used to resolve type specific urls
     * like for the detail page link.
     *
     * @return string
     */
    abstract public function getType(): string;

    /**
     * Provides the type to use as search queries.
     *
     * @return string
     */
    abstract public function getSearchType(): string;

    /**
     * Resolves Label With Display Name
     *
     * @param string $translationKeyBase Translation Key Base
     *
     * @return string
     */
    public function resolveLabelWithDisplayName(string $translationKeyBase)
    {
        $displayName = $this->getDisplayName();
        $label = null;

        if (is_null($displayName)) {
            $label = $this->getView()->translate(
                sprintf(
                    '%s.no.name', $translationKeyBase
                )
            );
        } else {
            $label = $this->getView()->translate($translationKeyBase);
            $label = sprintf($label, $displayName);
        }

        return $label;
    }

    private $_metadataHelper;

    /**
     * Returns metadata
     *
     * @return MetadataHelper
     */
    public function getMetadata(): MetadataHelper
    {
        if (is_null($this->_metadataHelper)) {
            $this->_metadataHelper = new MetadataHelper();
            $this->_metadataHelper->setSource($this);
            $this->_metadataHelper->setView($this->getView());
            $this->_metadataHelper->setPrefix($this->getMetadataPrefix());
            $this->_metadataHelper->setMetadataMethodMap(
                $this->getMetadataMethodMap()
            );
        }

        return $this->_metadataHelper;
    }

    /**
     * Template method subclasses may override to provide a prefix for
     * localized
     * labels for a specific purpose. It will be set on the metadata view
     * helper.
     *
     * @return string
     */
    protected function getMetadataPrefix(): string
    {
        return '';
    }

    /**
     * Template method subclasses may override to provide an array that maps
     * metadata keys on methods on this helper. It will be set on the metadata
     * view helper. Then you can call the MetadataViewHelper#getMetadataList()
     * method with the keys of this array to retrieve these metadata
     * information.
     *
     * @return array
     */
    protected function getMetadataMethodMap(): array
    {
        return [];
    }

    /**
     * Converts the field specified by $name into a string. Fields are expected to be
     * an array of values which will be joined by the given $delimiter. In case the
     * field is either null, an empty array or an empty string, null will be the
     * result.
     * This method is useful for fields which contain ready to use lists of strings
     * like the localized display fields.
     *
     * @param string $name      The name of the field to convert into a string.
     * @param string $delimiter The delimiter join elements in the field's array.
     *
     * @return string
     */
    protected function fieldToString(string $name, string $delimiter)
    {
        $field = 'get' . ucfirst($name);
        $value = $this->getDriver()->{$field}();

        if (is_string($value) && strlen($value) > 0) {
            $value = [$value];
        }

        $available = (is_array($value) && count($value) > 0);

        return $available ? $this->escape(implode($delimiter, $value)) : null;
    }

    /**
     * Provides the localized label for the link on the detail page of the
     * underlying managed record driver. The method is used by the
     * getDetailPageLink() method for link generation.
     *
     * @return string
     */
    abstract protected function getDetailPageLinkLabel();

    /**
     * Returns Link to detail page
     * If not null it is treated as the localization key and will be resolved
     * before it is merged into the template.
     *
     * @param string      $template The template
     * @param string|null $label    The label
     *
     * @return string
     */
    public function getDetailPageLink(string $template, string $label = null
    ): string {
        $label = is_null($label) ? $this->getDetailPageLinkLabel()
            : $this->getView()->translate($label);

        $route = sprintf('page-detail-%s', $this->getType());
        $segments = ['id' => $this->getDriver()->getUniqueID()];
        $url = $this->getView()->url($route, $segments);

        return sprintf($template, $url, $label);
    }

    /**
     * Generates a label string with counting information based on the given results.
     *
     * @param Results $results The results to use for retrieving counting
     *                         information.
     *
     * @return string
     */
    public function getResultsCountingInfoLabel(Results $results)
    {
        $total = $results->getResultTotal();
        $loaded = count($results->getResults());
        $first = $loaded > 0 ? 1 : 0;
        $template = $this->getView()->translate('page.detail.media.list.hits');

        return $this->escape(sprintf($template, $first, $loaded, $total));
    }

    /**
     * Shortcut to HTML-escape the given string using the escapeHtml() Escaper from
     * the connected view.
     *
     * @param string $value The value to HTML-escape
     *
     * @return string|null
     */
    protected function escape(string $value = null)
    {
        return is_null($value) ? null : $this->getView()->escapeHtml($value);
    }

    /**
     * Utilizes the URL escaper and performs batch escaping in case the given value
     * is an array.
     *
     * @param string|array|null $value the value to escape.
     *
     * @return array|string|null
     */
    public function escapeUrl($value = null)
    {
        $result = null;

        if (is_array($value)) {
            $result = [];
            foreach ($value as $key => $url) {
                $result[$key] = $this->getView()->escapeUrl($url);
            }
        } else if (!is_null($value)) {
            $result = $this->getView()->escapeUrl($value);
        }

        return $result;
    }

    /**
     * Creates a string that contains the resolve translation value for the given key
     * and appends the specified number of results in round brackets.
     *
     * @param string $translationKey The key to localize the headline text.
     * @param int    $numResults     The total number of results of the according
     *                               search.
     * @param bool   $useDisplayName Indicates whether to use the translation key to
     *                               resolve a label based on the display name of the
     *                               helper's underlying record.
     *
     * @return string
     */
    public function getSearchResultHeadline(
        string $translationKey, int $numResults, bool $useDisplayName = false
    ): string {
        $headline = $useDisplayName
            ? $this->resolveLabelWithDisplayName($translationKey)
            : $this->getView()->translate($translationKey);

        return sprintf('%s (%u)', $headline, $numResults);
    }

    /**
     * Has Thumbnail
     *
     * @return bool
     */
    public function hasThumbnail(): bool
    {
        $thumbnail = $this->getThumbnailFromRecord();
        return is_string($thumbnail);
    }

    /**
     * Resolves the url to the thumbnail image for a person.
     *
     * @return string
     */
    public function getThumbnailPath()
    {
        $thumbnail = $this->getThumbnailFromRecord();
        return is_string($thumbnail) ? $thumbnail : null;
    }

    /**
     * Tries to resolve a thumbnail for the underlying record driver by delegating
     * to the 'record' view helper which attempts to grab a thumbnail path from the
     * record first and if that fails it tries to build a URL from an external server
     * configuration as last option.
     *
     * @return string|null
     */
    protected function getThumbnailFromRecord()
    {
        $recordHelper = $this->getView()->record($this->getDriver());
        return $recordHelper->getThumbnailFromRecord(false);
    }

    /**
     * Generates a reference link to an external resource about the record when the
     * given link matches one of the patterns in the record references configuration.
     *
     * @param string     $template   The template string to use.
     * @param string     $link       The link to be checked and meroged into
     *                                        the template string.
     * @param ZendConfig $references All configured record references.
     *
     * @return string
     * In case the given link does not match on one of the record reference patterns,
     * then an empty string is returned.
     */
    public function getRecordReference(
        string $template, string $link, ZendConfig $references
    ): string {

        $result = '';

        foreach ($references as $id => $reference) {
            if (preg_match($reference->pattern, $link) === 1) {
                $result = sprintf($template, $link, $reference->label);
                break;
            }
        }

        return $result;
    }

    /**
     * Gets the MediaSearchLink
     *
     * @param string $template       The template
     * @param string $label          The label to be rendered.
     * @param bool   $translateLabel Indicates whether to treat the label parameter
     *                               as localization key or to use it as is.
     *
     * @return string
     */
    public function getMediaSearchLink(
        string $template, string $label, bool $translateLabel = false
    ): string {
        $label = $translateLabel ? $this->getView()->translate($label) : $label;
        $url = $this->getView()->url('search-results');
        $url = sprintf(
            '%s?lookfor=%s&type=%s', $url,
            urlencode($this->getDriver()->getName()), $this->getSearchType()
        );

        return sprintf($template, $url, $label);
    }

}
