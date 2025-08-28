var HOME = BASE_URL + 'report/purchase/receive_po_by_product/';

function toggleAllProduct(option){
  $('#allProduct').val(option);
  if(option == 1){
    $('#btn-pd-all').addClass('btn-primary');
    $('#btn-pd-range').removeClass('btn-primary');
    $('#pdFrom').val('');
    $('#pdFrom').attr('disabled', 'disabled');
    $('#pdTo').val('');
    $('#pdTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-pd-all').removeClass('btn-primary');
    $('#btn-pd-range').addClass('btn-primary');
    $('#pdFrom').removeAttr('disabled');
    $('#pdTo').removeAttr('disabled');
    $('#pdFrom').focus();
  }
}


function toggleAllDocument(option){
  $('#allDoc').val(option);
  if(option == 1){
    $('#btn-doc-all').addClass('btn-primary');
    $('#btn-doc-range').removeClass('btn-primary');
    $('#docFrom').val('');
    $('#docFrom').attr('disabled', 'disabled');
    $('#docTo').val('');
    $('#docTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-doc-all').removeClass('btn-primary');
    $('#btn-doc-range').addClass('btn-primary');
    $('#docFrom').removeAttr('disabled');
    $('#docTo').removeAttr('disabled');
    $('#docFrom').focus();
  }
}


function toggleAllVendor(option){
  $('#allVendor').val(option);
  if(option == 1){
    $('#btn-vender-all').addClass('btn-primary');
    $('#btn-vender-range').removeClass('btn-primary');
    $('#venderFrom').val('');
    $('#venderFrom').attr('disabled', 'disabled');
    $('#venderTo').val('');
    $('#venderTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-vender-all').removeClass('btn-primary');
    $('#btn-vender-range').addClass('btn-primary');
    $('#venderFrom').removeAttr('disabled');
    $('#venderTo').removeAttr('disabled');
    $('#venderFrom').focus();
  }
}


function toggleAllPO(option){
  $('#allPO').val(option);
  if(option == 1){
    $('#btn-po-all').addClass('btn-primary');
    $('#btn-po-range').removeClass('btn-primary');
    $('#poFrom').val('');
    $('#poFrom').attr('disabled', 'disabled');
    $('#poTo').val('');
    $('#poTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-po-all').removeClass('btn-primary');
    $('#btn-po-range').addClass('btn-primary');
    $('#poFrom').removeAttr('disabled');
    $('#poTo').removeAttr('disabled');
    $('#poFrom').focus();
  }
}



$('#pdFrom').autocomplete({
  source:BASE_URL + 'auto_complete/get_item_code',
  autoFocus:true,
  close:function(){
    var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
    if(arr.length == 2){
      var item = arr[0];
      $(this).val(item);
      if(item.length){
        var pdTo = $('#pdTo').val();
        if(pdTo.length > 0){
          if(item > pdTo){
            $('#pdTo').val(item);
            $(this).val(pdTo);
          }
        }
      }

      $('#pdTo').focus();
    }
  }
});



$('#pdTo').autocomplete({
  source:BASE_URL + 'auto_complete/get_item_code',
  autoFocus:true,
  close:function(){
    var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
    if(arr.length == 2){
      var item = arr[0];
      $(this).val(item);
      if(item.length){
        var pdFrom = $('#pdFrom').val();
        if(pdFrom.length > 0){
          if(item < pdFrom){
            $('#pdFrom').val(item);
            $(this).val(pdFrom);
          }
        }
      }
    }
  }
});



$('#venderFrom').autocomplete({
  source:BASE_URL + 'auto_complete/get_vender_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
    var vender = arr[0];
    $(this).val(vender);
    if(vender.length){
      var venderTo = $('#venderTo').val();
      if(venderTo.length > 0){
        if(vender > venderTo){
          $('#venderTo').val(vender);
          $(this).val(venderTo);
        }
      }
    }

    $('#venderTo').focus();
  }
});


$('#venderTo').autocomplete({
  source:BASE_URL + 'auto_complete/get_vender_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
    var vender = arr[0];
    $(this).val(vender);
    if(vender.length){
      var venderFrom = $('#venderFrom').val();
      if(venderFrom.length > 0){
        if(vender < venderFrom){
          $('#venderFrom').val(vender);
          $(this).val(venderFrom);
        }
      }
    }
  }
});




//--- Date picker
$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#fromDate').datepicker('option','maxDate', sd);
  }
});


function getReport(){
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

  var allProduct = $('#allProduct').val();
  var pdFrom = $('#pdFrom').val();
  var pdTo = $('#pdTo').val();

  var allDoc = $('#allDoc').val();
  var docFrom = $('#docFrom').val();
  var docTo = $('#docTo').val();

  var allVendor = $('#allVendor').val();
  var venderFrom = $('#venderFrom').val();
  var venderTo = $('#venderTo').val();

  var allPO = $('#allPO').val();
  var poFrom = $('#poFrom').val();
  var poTo = $('#poTo').val();

  if(allProduct == 0){
    if(pdFrom.length == 0){
      $('#pdFrom').addClass('has-error');
      swal('Error!', 'สินค้าไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#pdFrom').removeClass('has-error');
    }

    if(pdTo.length == 0){
      $('#pdTo').addClass('has-error');
      swal('Error!', 'สินค้าไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#pdTo').removeClass('has-error');
    }
  }else{
    $('#pdFrom').removeClass('has-error');
    $('#pdTo').removeClass('has-error');
  }

  if(allDoc == 0){
    if(docFrom.length == 0){
      $('#docFrom').addClass('has-error');
      swal('Error!', 'เอกสารไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#docFrom').removeClass('has-error');
    }

    if(docTo.length == 0){
      $('#docTo').addClass('has-error');
      swal('Error!', 'เอกสารไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#docTo').removeClass('has-error');
    }
  }else{
    $('#docFrom').removeClass('has-error');
    $('#docTo').removeClass('has-error');
  }

  if(allVendor == 0){
    if(venderFrom.length == 0){
      $('#venderFrom').addClass('has-error');
      swal('Error!', 'ผู้ขายไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#venderFrom').removeClass('has-error');
    }

    if(venderTo.length == 0){
      $('#venderTo').addClass('has-error');
      swal('Error!', 'ผู้ขายไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#venderTo').removeClass('has-error');
    }
  }else{
    $('#venderFrom').removeClass('has-error');
    $('#venderTo').removeClass('has-error');
  }


  if(allPO == 0){
    if(poFrom.length == 0){
      $('#poFrom').addClass('has-error');
      swal('Error!', 'ใบสั่งซื้อไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#poFrom').removeClass('has-error');
    }

    if(poTo.length == 0){
      $('#poTo').addClass('has-error');
      swal('Error!', 'ใบสั่งซื้อไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#poTo').removeClass('has-error');
    }
  }else{
    $('#poFrom').removeClass('has-error');
    $('#poTo').removeClass('has-error');
  }

  if(!isDate(fromDate)){
    $('#fromDate').addClass('has-error');
    swal('Error!', 'วันที่ไม่ถูกต้อง', 'error');
    return false;
  }else{
    $('#fromDate').removeClass('has-error');
  }

  if(!isDate(toDate)){
    $('#toDate').addClass('has-error');
    swal('Error!', 'วันที่ไม่ถูกต้อง', 'error');
    return false;
  }else{
    $('#toDate').removeClass('has-error');
  }

  var data = [
    {'name' : 'allProduct', 'value' : allProduct},
    {'name' : 'pdFrom', 'value' : pdFrom},
    {'name' : 'pdTo', 'value' : pdTo},
    {'name' : 'allDoc', 'value' : allDoc},
    {'name' : 'docFrom', 'value' : docFrom},
    {'name' : 'docTo', 'value' : docTo},
    {'name' : 'fromDate', 'value' : fromDate},
    {'name' : 'toDate', 'value' : toDate},
    {'name' : 'allVendor', 'value' : allVendor},
    {'name' : 'venderFrom', 'value' : venderFrom},
    {'name' : 'venderTo', 'value' : venderTo},
    {'name' : 'allPO', 'value' : allPO},
    {'name' : 'poFrom', 'value' : poFrom},
    {'name' : 'poTo', 'value' : poTo}
  ];

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'GET',
    cache:'false',
    data:data,
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(isJson(rs)){
        var source = $('#template').html();
        var data = $.parseJSON(rs);
        var output = $('#rs');
        render(source,  data, output);
      }
    }
  });

}


function doExport(){
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

  var allProduct = $('#allProduct').val();
  var pdFrom = $('#pdFrom').val();
  var pdTo = $('#pdTo').val();

  var allDoc = $('#allDoc').val();
  var docFrom = $('#docFrom').val();
  var docTo = $('#docTo').val();

  var allVendor = $('#allVendor').val();
  var venderFrom = $('#venderFrom').val();
  var venderTo = $('#venderTo').val();

  var allPO = $('#allPO').val();
  var poFrom = $('#poFrom').val();
  var poTo = $('#poTo').val();

  if(allProduct == 0){
    if(pdFrom.length == 0){
      $('#pdFrom').addClass('has-error');
      swal('Error!', 'สินค้าไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#pdFrom').removeClass('has-error');
    }

    if(pdTo.length == 0){
      $('#pdTo').addClass('has-error');
      swal('Error!', 'สินค้าไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#pdTo').removeClass('has-error');
    }
  }else{
    $('#pdFrom').removeClass('has-error');
    $('#pdTo').removeClass('has-error');
  }

  if(allDoc == 0){
    if(docFrom.length == 0){
      $('#docFrom').addClass('has-error');
      swal('Error!', 'เอกสารไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#docFrom').removeClass('has-error');
    }

    if(docTo.length == 0){
      $('#docTo').addClass('has-error');
      swal('Error!', 'เอกสารไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#docTo').removeClass('has-error');
    }
  }else{
    $('#docFrom').removeClass('has-error');
    $('#docTo').removeClass('has-error');
  }

  if(allVendor == 0){
    if(venderFrom.length == 0){
      $('#venderFrom').addClass('has-error');
      swal('Error!', 'ผู้ขายไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#venderFrom').removeClass('has-error');
    }

    if(venderTo.length == 0){
      $('#venderTo').addClass('has-error');
      swal('Error!', 'ผู้ขายไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#venderTo').removeClass('has-error');
    }
  }else{
    $('#venderFrom').removeClass('has-error');
    $('#venderTo').removeClass('has-error');
  }


  if(allPO == 0){
    if(poFrom.length == 0){
      $('#poFrom').addClass('has-error');
      swal('Error!', 'ใบสั่งซื้อไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#poFrom').removeClass('has-error');
    }

    if(poTo.length == 0){
      $('#poTo').addClass('has-error');
      swal('Error!', 'ใบสั่งซื้อไม่ถูกต้อง', 'error');
      return false;
    }else{
      $('#poTo').removeClass('has-error');
    }
  }else{
    $('#poFrom').removeClass('has-error');
    $('#poTo').removeClass('has-error');
  }

  if(!isDate(fromDate)){
    $('#fromDate').addClass('has-error');
    swal('Error!', 'วันที่ไม่ถูกต้อง', 'error');
    return false;
  }else{
    $('#fromDate').removeClass('has-error');
  }

  if(!isDate(toDate)){
    $('#toDate').addClass('has-error');
    swal('Error!', 'วันที่ไม่ถูกต้อง', 'error');
    return false;
  }else{
    $('#toDate').removeClass('has-error');
  }

  var token = $('#token').val();
  get_download(token);

  $('#reportForm').submit();

}
