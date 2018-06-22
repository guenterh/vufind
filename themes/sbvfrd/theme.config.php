<?php
return array(
  'extends' => 'bootstrap3',

  'less' => array(
    'active' => false,
    'components/js-tree.less',
    'compiled.less'
  ),

  'css' => array(
  ),

  'js'      => array(
    'vendor/jquery/plugin/jquery.cookie.js',
    'vendor/jquery/plugin/loadmask/jquery.loadmask.js',
    'vendor/chosen/chosen.jquery.min.js',

    'vendor/jstorage/jstorage.min.js', //used for favorites - there is still some amount of JS code inline of the page -> Todo: Refactoring in upcoming Sprints
    'vendor/handlebars/handlebars.js', //wird in swissbib/AdvancedSearch.js verwendet
    'vendor/respond/respond.js:lt IE 9',
    'vendor/html5shiv/html5shiv.js:lt IE 9',

    'vendor/jsTree/jstree.min.js',

    'polyfill/es6-promise.auto.min.js',

    'swissbib/swissbib.js',
    'swissbib/common.js',
    'swissbib/AdvancedSearch.js',
    'swissbib/Holdings.js',
    'swissbib/HoldingFavorites.js',
    'swissbib/FavoriteInstitutions.js',
    'swissbib/Accordion.js',
    'swissbib/Settings.js',
    'swissbib/OffCanvas.js',

    'lib/autocomplete.js',
  ),
  'favicon' => 'favicon.ico',
  'helpers' => array(
    'factories'  => array(
      'VuFind\View\Helper\Root\Auth'                => 'Swissbib\View\Helper\Swissbib\Factory::getAuth',
      'Swissbib\VuFind\View\Helper\Root\Record'     => 'VuFind\View\Helper\Root\RecordFactory',
      'Swissbib\VuFind\View\Helper\Root\Citation'   => 'VuFind\View\Helper\Root\CitationFactory',
      'Swissbib\View\Helper\RecordLink'             => 'VuFind\View\Helper\Root\RecordLinkFactory',
      'Swissbib\View\Helper\LayoutClass'            => 'VuFind\View\Helper\Bootstrap3\LayoutClassFactory',
      'Swissbib\View\Helper\Root\SearchTabs'        => 'VuFind\View\Helper\Root\SearchTabsFactory',
      'Swissbib\View\Helper\Root\Piwik'             => 'VuFind\View\Helper\Root\PiwikFactory',
      'nationalLicences'          => 'Swissbib\View\Helper\Swissbib\Factory::getNationalLicences',
      'autoSuggestConfig'         => 'Swissbib\View\Helper\Swissbib\Factory::getAutoSuggestConfig',
      'getextendedlastsearchlink' => 'Swissbib\View\Helper\Swissbib\Factory::getExtendedLastSearchLink',
      'includeTemplate'           => 'Swissbib\View\Helper\Swissbib\Factory::getIncludeTemplate',
      'translateFacets'           => 'Swissbib\View\Helper\Swissbib\Factory::getFacetTranslator',
      'formatRelatedEntries'      => 'Swissbib\View\Helper\Swissbib\Factory::getFormatRelatedEntries',
    ),
    'aliases' => array(
        'auth'          => 'VuFind\View\Helper\Root\Auth',
        'record'        => 'Swissbib\VuFind\View\Helper\Root\Record',
        'layoutClass'   => 'Swissbib\View\Helper\LayoutClass',
        'citation'      => 'Swissbib\VuFind\View\Helper\Root\Citation',
        'recordlink'    => 'Swissbib\View\Helper\RecordLink',
        'searchtabs'    => 'Swissbib\View\Helper\Root\SearchTabs',
        'piwik'         => 'Swissbib\View\Helper\Root\Piwik',
    ),
    'invokables' => array(
      //'translate' => 'Swissbib\VuFind\View\Helper\Root\Translate',
    )
  )
);
