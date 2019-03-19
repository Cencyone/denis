
var PhysicalFilter = {
    brandsUrl: '',
    propertiesUrl: '',
    baseUrl: '',
    manyPhysicalURL: false,
    brands: {},
    properties: {},
    useMultiSegmentsURL: function () {
        this.manyPhysicalURL = true;
        return this;
    },
    init: function (formObj) {
        this.brands = $(formObj).find('input[name="brand[]"]:checked');
        // this.properties = $(formObj).find('input[name^="p["]:checked');
        this.properties = $(formObj).find('input[name^="p["]:checked, input[name^="pv["]:checked');
    },
    formBrandsUrl: function (formObj) {
        if (this.brands.length > 1 && !this.manyPhysicalURL) {
            return false;
        }

        if ((this.brands.length && this.properties.length) && !this.manyPhysicalURL) {
            return false;
        }

        $(this.brands).each(function () {
            if (PhysicalFilter.brandsUrl) {
                PhysicalFilter.brandsUrl += ';'
            }

            PhysicalFilter.brandsUrl += 'brand-' + $(this).data('url');
            $(this).attr('disabled', true);
        });
    },
    formPropertiesUrl: function (formObj) {
        if (this.properties.length > 1 && !this.manyPhysicalURL) {
            return false;
        }

        if ((this.brands.length && this.properties.length) && !this.manyPhysicalURL) {
            return false;
        }

        $(this.properties).each(function () {
            if (PhysicalFilter.propertiesUrl) {
                PhysicalFilter.propertiesUrl += ';'
            }

            PhysicalFilter.propertiesUrl += 'property-' + $(this).data('physical');
            $(this).attr('disabled', true);
        });

    },
    clearUrl: function () {
        this.baseUrl = location.pathname;
        var cutToProperty = null;
        var cutToBrand = null;
        var cutTo = null;

        cutToProperty = this.baseUrl.indexOf('/property-');
        cutToBrand = this.baseUrl.indexOf('/brand-');
        cutTo = Math.min(cutToProperty, cutToBrand);
        cutTo = cutTo === -1 ? Math.max(cutToProperty, cutToBrand) : this.baseUrl.length;

        if (cutTo !== -1) {
            this.baseUrl = this.baseUrl.substring(0, cutTo);
        }
    },
    getUrl: function (formObj) {
        this.init(formObj);
        this.formBrandsUrl(formObj);
        this.formPropertiesUrl(formObj);
        this.clearUrl();
        this.propertiesUrl = (this.propertiesUrl && this.brandsUrl) ? ';' + this.propertiesUrl : this.propertiesUrl;

        return this.baseUrl + '/' + this.brandsUrl + this.propertiesUrl;
    }
};


/* DEPRECATED! Using in New Level Templates */
$(document).on('ready', function () {
    $('#catalogForm').submit(function (e) {
        e.preventDefault();

        var defaults = ['default', 'mins', 'maxs']
        $(this).find("[type='text'], [type='hidden']").each(function (e) {
            var input = $(this);

            for (var key in defaults) {
                var defaultValue = input.data(defaults[key])
                if (defaultValue && defaultValue == input.val()) {
                    input.attr('disabled', true);
                }
            }

        });
        var formAction = PhysicalFilter
            .useMultiSegmentsURL()
            .getUrl(this);
        var formData = $(this).serialize();
        location.assign( formAction + (formData ? '?' + formData : formData));


    });
});