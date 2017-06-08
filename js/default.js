jQuery(document).ready(function(){
  "use strict";
  var ingredientsData = [];
  var data = {
      'action': 'get_ingredients',
      'nonce': ajax_object.nonce,
  };
  jQuery.ajax({
    url: ajax_object.ajax_url,
    data: data,
    cache: false,
    context: document.body,
    dataType: 'json',
    type: 'GET',
    success: function(data, textStatus, jqXHR){
      jQuery(data).each(function(index, response){
        ingredientsData.push(response.name);
      });
    },
    error: function(jqXHR, textStatus, errorThrown){
      console.error(textStatus, errorThrown);
    }
  });
  jQuery('.recipe-ingredient-input').autocomplete({
    source: ingredientsData,
    minLength: 1
  });
  jQuery('#freez-recipes-add-ingredient').on('click', function(){
    var newRow = jQuery('#freez-recipes-metaboxes .freez-recipes-ingredients').first().clone();
    newRow.find('input[type=text]').val('');
    newRow.find('input[type=number]').val('');
    newRow.find('.recipe-ingredient-input').autocomplete({
      source: ingredientsData,
      minLength: 1
    })
    newRow.appendTo(jQuery('#freez-recipes-metaboxes'));
  });

});
