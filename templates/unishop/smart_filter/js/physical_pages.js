;(function ($) {
  $.imcSeoUrl = {
    add: function (options) {

      /* Apply script only on category page */
      if (!options.catUrl)
        return false;

      var url = options.catUrl;
      /* Add property name to category url for making Seo-friendly url */
      var urlSegment = '';

      var segments = {};

      options.fields.each(function () {
        var $this = $(this);
        var parent = $($this.closest('[data-filter-name]'));
        var position = parent.data('filter-position');
        var name = parent.data('filter-name');
        var alias = $this.data('filter-alias');

        if (segments[name] === undefined) {
          segments[name] = {position: position, values: [alias]};
        } else {
          segments[name].values.push(alias)
        }

        /* Disable property to prevent sending additional param it query string */
        $this.attr('disabled', true);

      });

      //sort segments by position
      var sortedNames = Object.keys(segments).sort(function (a, b) {
        return segments[a].position - segments[b].position
      });

      //add values to segment sort segment values
      for (var key in sortedNames) {
        var name = sortedNames[key]
        urlSegment += name + '-' + segments[name].values.sort(function (a, b) {
            if (a < b) {
              return -1;
            } else if (a > b) {
              return 1;
            }
            return 0;
          }).join('-or-') + '/';
      }


      url = url + ( '/' + urlSegment);

      /* Change form action with Seo-friendly url */
      options.form.attr('action', url);
    }
  }
})(jQuery);