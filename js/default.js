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
  jQuery('.btn-remove-ingredient').live('click', function(){
    jQuery(this).parent().remove();
  });
  jQuery('#freez-recipes-form-view-print').live('submit', function(e){
    if(!jQuery('#freez-recipes-form-view-print input[type=checkbox]:checked').length){
      e.preventDefault();
      alert('Você precisa selecionar pelo menos uma receita para salvar o PDF da lista de compras.');
      return true;
    }
  });
  jQuery('#freez-recipes-form-view-print input[type=checkbox]').live('click', function(event){
    var checkedBoxes = jQuery('#freez-recipes-form-view-print input[type=checkbox]:checked').length;
    if (checkedBoxes > 6) {
      event.preventDefault();
      alert("Você só pode selecionar até 6 receitas para ver lista de ingredientes ou gerar PDF.");
      return false;
    }
  });
  jQuery('#freez-recipes-pdf-view').on('click', function(){
    if(!jQuery('#freez-recipes-form-view-print input[type=checkbox]:checked').length){
      alert('Você precisa selecionar pelo menos uma receita para ver a lista de ingredientes.');
      return true;
    }
    jQuery('#freez-recipes-form-action').val('freez_recipes_view');
    jQuery.ajax({
      url: ajax_object.ajax_url,
      data: jQuery('#freez-recipes-form-view-print').serializeArray(),
      cache: false,
      context: document.body,
      dataType: 'json',
      type: 'POST',
      success: function(data, textStatus, jqXHR){
        var popupBlockerChecker = {
          check: function(popup_window){
            var _scope = this;
            if (popup_window) {
              if(/chrome/.test(navigator.userAgent.toLowerCase())){
                setTimeout(function () {
                  _scope._is_popup_blocked(_scope, popup_window);
                },200);
              }else{
                popup_window.onload = function () {
                  _scope._is_popup_blocked(_scope, popup_window);
                };
              }
            }else{
              _scope._displayError();
            }
          },
          _is_popup_blocked: function(scope, popup_window){
            if ((popup_window.innerHeight > 0)==false){ scope._displayError(); }
          },
          _displayError: function(){
            alert("Seu bloqueador de popup está ativado! Por favor adicione este site em sua lista de permissões.");
          }
        };

        var wnd = window.open('about:blank', 'Home Chefs - Lista de Compras');
        popupBlockerChecker.check(wnd);
        wnd.document.write(data.html);
        wnd.document.close();
      },
      error: function(jqXHR, textStatus, errorThrown){
        console.error(textStatus, errorThrown);
      }
    });
    jQuery('#freez-recipes-form-action').val('freez_recipes_print');
  });
});
