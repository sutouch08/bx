var HOME = BASE_URL + 'report/inventory/stock_balance/';

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
    return
  }

  if(option == 0){
    $('#btn-wh-all').removeClass('btn-primary');
    $('#btn-wh-range').addClass('btn-primary');
    $('#wh-modal').modal('show');
  }
}


function toggleDate(option){
  $('#currentDate').val(option);
  if(option == 1){
    $('#btn-date-now').addClass('btn-primary');
    $('#btn-date-range').removeClass('btn-primary');
    $('#date').val('');
    $('#date').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-date-now').removeClass('btn-primary');
    $('#btn-date-range').addClass('btn-primary');
    $('#date').removeAttr('disabled');
  }
}

$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});


function getReport() {
  clearErrorByClass('e');

  let h = {
    'allProduct' : $('#allProduct').val(),
    'allWhouse' : $('#allWarehouse').val(),
    'pdFrom' : $('#pdFrom').val().trim(),
    'pdTo' : $('#pdTo').val().trim(),
    'date' : $('#date').val(),
    'warehouse' : []
  };

  if(h.allProduct == 0 && h.pdFrom.length == 0) {
    $('#pdFrom').hasError();
    return false;
  }

  if(h.allProduct == 0 && h.pdTo.length == 0) {
    $('#pdTo').hasError();
    return false;
  }

  if(h.allWhouse == 0) {
    let count = $('.chk:checked').length;
    if(count == 0) {
      $('#wh-modal').modal('show');
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
    url:HOME + 'get_report',
    type:'POST',
    cache:'false',
    data: {
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();
      if(isJson(rs)) {
        let source = $('#template').html();
        let data = JSON.parse(rs);
        let output = $('#rs');
        render(source,  data, output);
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs)
    }
  });
}


function doExport() {
  clearErrorByClass('e');

  let h = {
    'allProduct' : $('#allProduct').val(),
    'allWhouse' : $('#allWarehouse').val(),
    'pdFrom' : $('#pdFrom').val().trim(),
    'pdTo' : $('#pdTo').val().trim(),
    'date' : $('#date').val(),
    'warehouse' : []
  };

  if(h.allProduct == 0 && pdFrom.length == 0) {
    $('#pdFrom').hasError();
    return false;
  }

  if(h.allProduct == 0 && pdTo.length == 0) {
    $('#pdTo').hasError();
    return false;
  }

  if(h.allWhouse == 0) {
    let count = $('.chk:checked').length;
    if(count == 0) {
      $('#wh-modal').modal('show');
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
  $('#reportForm').submit();
}
