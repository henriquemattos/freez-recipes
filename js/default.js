var i = 1;
var alert = '<div class="alert alert-dismissible fade in" role="alert">'
  + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>'
  + '</div>';
jQuery(document).ready(function(){
  // add new table row
  jQuery('.btn-add-recipe').live('click', function(){
    var tableBody = jQuery(this).parent().parent();
    var newRow = tableBody.first().clone();
    newRow.find('.btn-add-recipe').removeClass('btn-sucess btn-add-recipe').
      addClass('btn-warning btn-remove-recipe').find('.glyphicon').removeClass('glyphicon-plus').
      addClass('glyphicon-minus');
    newRow.find('.input-recipe-ingredient').val('');
    newRow.appendTo(tableBody.parent());
  });

  // remove table row
  jQuery('.btn-remove-recipe').live('click', function(){
    jQuery(this).parent().parent().remove();
  });

  jQuery('form#save-ingredient').on('submit', function(e){
    e.preventDefault();
    var data = {
      'action': 'set_ingredients',
      'nonce': ajax_object.nonce,
      'data': jQuery(this).serializeJSON()
    };
    jQuery.ajax({
      url: ajax_object.ajax_url,
      data: data,
      cache: false,
      context: document.body,
      dataType: 'json',
      type: 'POST',
      beforeSend: function(jqXHR, settings){
        jQuery('.alert-dismissible').alert('close');
      },
      success: function(data, textStatus, jqXHR){
        var msg = '<p>' + textStatus + ': ' + data.response + '</p>';
        jQuery('#alert').prepend(jQuery(alert).append(msg).addClass('alert-success').alert());
      },
      complete: function(jqXHR, textStatus){
        window.scrollTo(0, 0);
      },
      error: function(jqXHR, textStatus, errorThrown){
        var msg = '<p>' + textStatus + ': ' + errorThrown + '</p>';
        jQuery('#alert').prepend(jQuery(alert).append(msg).addClass('alert-danger').alert());
      }
    });
  });
});
