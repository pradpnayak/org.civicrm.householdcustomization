CRM.$(function($) {
  var noOfChildrenField = CRM.vars.noOfChildrenField.hideField;
  showHideChildFields($('#' + noOfChildrenField));
  $('#' + noOfChildrenField).click(function(){
    showHideChildFields($(this));
  });
  function showHideChildFields(obj) {
    var fieldValue = $(obj).val();
    var childrensFields = CRM.vars.customFields;
    $.each(childrensFields, function(index, values) {
      $.each(values, function(key, value) {
        if (fieldValue && fieldValue >= index) {
          $('#editrow-' + value).show();
        }
        else {
          $('#editrow-' + value).hide();
        }
      });
    });
  }
});
