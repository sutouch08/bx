var HOME = BASE_URL + 'report/inventory/stock_balance_zone/';

$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});

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


$('#pdFrom').autocomplete({
  source : BASE_URL + 'auto_complete/get_item_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    var pdFrom = arr[0];
    $(this).val(pdFrom);
    var pdTo = $('#pdTo').val();
    if(pdTo.length > 0 && pdFrom.length > 0){
      if(pdFrom > pdTo){
        $('#pdTo').val(pdFrom);
        $('#pdFrom').val(pdTo);
      }
    }
  }
});


$('#pdTo').autocomplete({
  source:BASE_URL + 'auto_complete/get_item_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    var pdTo = arr[0];
    $(this).val(pdTo);
    var pdFrom = $('#pdFrom').val();
    if(pdTo.length > 0 && pdFrom.length > 0){
      if(pdFrom > pdTo){
        $('#pdTo').val(pdFrom);
        $('#pdFrom').val(pdTo);
      }
    }
  }
})


function toggleAllWarehouse(option){
  $('#allWarehouse').val(option);
  if(option == 1){
    $('#btn-wh-all').addClass('btn-primary');
    $('#btn-wh-range').removeClass('btn-primary');
    $('.chk').removeAttr('checked');
    return
  }

  if(option == 0){
    $('#btn-wh-all').removeClass('btn-primary');
    $('#btn-wh-range').addClass('btn-primary');
    $('#wh-modal').modal('show');
  }

  zone_init();
}


function toggleAllZone(option){
  $('#allZone').val(option);
  if(option == 1){
    $('#btn-zone-all').addClass('btn-primary');
    $('#btn-zone-range').removeClass('btn-primary');
    $('#zone-code').val('').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-zone-all').removeClass('btn-primary');
    $('#btn-zone-range').addClass('btn-primary');
    $('#zone-code').removeAttr('disabled').focus();
  }
}


function zone_init() {
  var warehouse = "";
  let i = 0;
  $('.chk').each(function(index, el) {
    if($(this).is(':checked')){
      if(i == 0) {
        warehouse = warehouse + $(this).val();
      }
      else{
        warehouse = warehouse + "|"+$(this).val();
      }

      i++;
    }
  });

  if(warehouse.length > 0){
    warehouse = "/"+warehouse;
  }

  $('#zone-code').autocomplete({
    source:BASE_URL + 'auto_complete/get_zone_code_and_name' + warehouse,
    autoFocus:true,
    close:function(){
      var rs = $(this).val();
      var rs = rs.split(' | ');
      if(rs.length == 2) {
        $(this).val(rs[0]);
      }
      else{
        $(this).val('');
      }
    }
  })
}


$('.chk').change(function(){
  zone_init();
})


function getReport() {
  clearErrorByClass('e');

  let h = {
    'allProduct' : $('#allProduct').val(),
    'pdFrom' : $('#pdFrom').val().trim(),
    'pdTo' : $('#pdTo').val().trim(),
    'allWhouse' : $('#allWarehouse').val(),
    'allZone' : $('#allZone').val(),
    'zoneCode' : $('#zone-code').val().trim(),
    'date' : $('#date').val(),
    'warehouse' : []
  };

  if(h.allProduct == 0) {
    if(h.pdFrom.length == 0){
      $('#pdFrom').hasError();
      return false;
    }

    if(h.pdTo.length == 0){
      $('#pdTo').hasError();
      return false;
    }
  }


  if(h.allWhouse == 0){
    var count = $('.chk:checked').length;
    if(count == 0) {
      $('#wh-modal').modal('show');
      return false;
    }
  }

  if(h.allZone == 0) {
    if(h.zoneCode.length == 0) {
      $('#zone-code').hasError();
      return false;
    }
  }

  if(h.allWhouse == 0) {
    $('.chk:checked').each(function() {
      h.warehouse.push($(this).val());
    });
  }

  load_in();

  $.ajax({
    url: HOME + 'get_report',
    type:'POST',
    cache:'false',
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        var source = $('#template').html();
        var data = JSON.parse(rs);
        var output = $('#rs');
        render(source,  data, output);
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  });
}


function doExport(){
  clearErrorByClass('e');

  let h = {
    'allProduct' : $('#allProduct').val(),
    'pdFrom' : $('#pdFrom').val().trim(),
    'pdTo' : $('#pdTo').val().trim(),
    'allWhouse' : $('#allWarehouse').val(),
    'allZone' : $('#allZone').val(),
    'zoneCode' : $('#zone-code').val().trim(),
    'date' : $('#date').val(),
    'warehouse' : []
  };

  if(h.allProduct == 0) {
    if(h.pdFrom.length == 0){
      $('#pdFrom').hasError();
      return false;
    }

    if(h.pdTo.length == 0){
      $('#pdTo').hasError();
      return false;
    }
  }


  if(h.allWhouse == 0){
    var count = $('.chk:checked').length;
    if(count == 0) {
      $('#wh-modal').modal('show');
      return false;
    }
  }

  if(h.allZone == 0) {
    if(h.zoneCode.length == 0) {
      $('#zone-code').hasError();
      return false;
    }
  }

  if(h.allWhouse == 0) {
    $('.chk:checked').each(function() {
      h.warehouse.push($(this).val());
    });
  }

  let token = generateUID();
  $('#data').val(JSON.stringify(h));
  $('#token').val(token);

  get_download(token);
  $('#exportForm').submit();
}

$(document).ready(function(){
  zone_init();
})
