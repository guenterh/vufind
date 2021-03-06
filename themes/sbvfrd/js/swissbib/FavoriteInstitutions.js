swissbib.FavoriteInstitutions = {

    baseUrl: '/MyResearch/Favorites',

    /**
     * Values for autocomplete list (cached)
     */
    autocompleteValues: [],


    /**
     * Initialize favorite management
     *
     * @param  {Object|Boolean}  availableInstitutions    List of institutions of false of already cached
     */
    init: function (availableInstitutions) {
        'use strict';
        this.baseUrl = window.path + this.baseUrl;

        // The institutions should already be cached
        if (!availableInstitutions) {
            availableInstitutions = this.getInstitutionsFromStorage();
        } else {
            // New institutions downloaded, save them
            this.saveInstitutionsToStorage(availableInstitutions);
        }

        this.installAutocomplete(availableInstitutions);
        this.installHandlers();
    },


    /**
     * Install autocompleter
     *
     * @param  {Object}  availableInstitutions
     */
    installAutocomplete: function (availableInstitutions) {
        'use strict';
        $('input#query').autocomplete({
            static: availableInstitutions,
            callback: $.proxy(this.onInstitutionSelect,this)
        });
    },


    /**
     * Install click handlers
     *
     */
    installHandlers: function () {
        'use strict';
        var that = this;
        $('#favorites-table').find('.deleteFavoriteInstitution').click(function (event) {
            var institutionCode = $(this).data('institution');
            that.deleteInstitution(institutionCode);
        });
    },


    /**
     * Handle institution selection
     *
     * @param  {Object}  event
     * @param  {Object}  ui
     */
    onInstitutionSelect: function (datum, obj, eventType) {
        'use strict';
        this.clearSearchField();
        this.addInstitution(datum.value);

        return false;
    },


    /**
     * Delete an institution and update list
     *
     * @param  {String}  institutionCode
     */
    deleteInstitution: function (institutionCode) {
        'use strict';
        this.sendRequestOnUpdateList('delete', institutionCode);
    },


    /**
     * Add institution and update list
     *
     * @param  {String}  institutionCode
     */
    addInstitution: function (institutionCode) {
        'use strict';
        this.sendRequestOnUpdateList('add', institutionCode);
    },


    /**
     * Send a request to the given action with the institution as parameter
     * Update list with response
     *
     * @param  {String}  action
     * @param  {String}  institutionCode
     */
    sendRequestOnUpdateList: function (action, institutionCode) {
        'use strict';
        var that = this,
            url = this.baseUrl + '/' + action,
            data = {
                institution: institutionCode,
                list: true
            };

        $('#user-favorites').mask('Update...');

        $('#user-favorites').load(url, data, function () {
            that.installHandlers();
            $('#user-favorites').unmask();
        });
    },


    /**
     * Clear search field value
     *
     */
    clearSearchField: function () {
        'use strict';
        $('#query').val('');
    },


    /**
     * Get institution list from local storage
     *
     * @returns {Object}
     */
    getInstitutionsFromStorage: function () {
        'use strict';
        return $.jStorage.get('favorite-institutions');
    },


    /**
     * Add institution list to local storage
     *
     * @param  {Object}  institutions
     */
    saveInstitutionsToStorage: function (institutions) {
        'use strict';
        $.jStorage.set('favorite-institutions', institutions);
    }

};