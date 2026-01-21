(function ($, Drupal, once) {

    Drupal.behaviors.searchToggle = {
      attach: function (context) {
  
        $(once('search-toggle', '.search_wrapper', context)).on('click', function () {
          $('.region--search', context).toggleClass('show-search');
        });
  
      }
    };
  
  })(jQuery, Drupal, once);
  