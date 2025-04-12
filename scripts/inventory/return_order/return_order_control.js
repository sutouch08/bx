$('#barcode').keyup(function(e){
  if(e.keyCode == 13){
    var barcode = $.trim($(this).val());
    var qty = $('#qty').val();
    doReceive();
  }
});


$('#invoice-box').keyup(function(e){
  if(e.keyCode === 13){
    load_invoice();
  }
})
