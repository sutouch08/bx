var click = 0;

$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});


//---- เปลี่ยนสถานะออเดอร์  เป็นบันทึกแล้ว
function saveOrder(){
  var order_code = $('#order_code').val();
	$.ajax({
		url: BASE_URL + 'orders/sponsor/save/'+ order_code,
		type:"POST",
    cache:false,
		success:function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({
          title: 'Saved',
          type: 'success',
          timer: 1000
        });

				setTimeout(function(){
          editOrder(order_code)
        }, 1200);

			}else{
				swal("Error ! ", rs , "error");
			}
		}
	});
}


$("#customerCode").autocomplete({
	source: BASE_URL + 'auto_complete/get_sponsor',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customerCode").val(code);
			$("#customer").val(name);
      getBudget(code);
		}else{
			$("#customerCode").val('');
			$('#customer').val('');
      $('#budget-amount').val(0.00);
      $('#budget-amount').data('amount', 0);
      $('#budget-id').val('');
      $('#budget-code').val('');
		}
	}
});


$("#customer").autocomplete({
	source: BASE_URL + 'auto_complete/get_sponsor',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customerCode").val(code);
			$("#customer").val(name);
      getBudget(code);
		}else{
			$("#customerCode").val('');
			$("#customer").val('');
      $('#budget-amount').val(0.00);
      $('#budget-amount').data('amount', 0);
      $('#budget-id').val('');
      $('#budget-code').val('');
		}
	}
});


function getBudget(code) {
  load_in();

  $.ajax({
    url:HOME + 'get_budget',
    type:'GET',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs){
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        $('#budget-amount').val(ds.amount_label);
        $('#budget-amount').data('amount', ds.amount);
        $('#budget-id').val(ds.budget_id);
        $('#budget-code').val(ds.budget_code);
      }
      else {
        $('#budget-amount').val(0.00);
        $('#budget-amount').data('amount', 0);
        $('#budget-id').val('');
        $('#budget-code').val('');
      }
    },
    error:function(rs) {
      load_out();
      $('#budget-amount').val(0.00);
      $('#budget-amount').data('amount', 0);
      $('#budget-id').val('');
      $('#budget-code').val('');
    }
  });
}


$('#customer').focusout(function(){
  var code = $(this).val();
  if(code.length == 0)
  {
    $('#customerCode').val('');
    $('#budget-amount').val(0.00);
    $('#budget-amount').data('amount', 0);
    $('#budget-id').val('');
    $('#budget-code').val('');
  }
});


$('#customerCode').focusout(function(){
  var code = $(this).val();
  if(code.length == 0)
  {
    $('#customer').val('');
    $('#budget-amount').val(0.00);
    $('#budget-amount').data('amount', 0);
    $('#budget-id').val('');
    $('#budget-code').val('');
  }
});


function add() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('e');

    let h = {
      'customer_code' : $('#customerCode').val(),
      'customer_name' : $('#customer').val(),
      'date_add' : $('#date').val(),
      'empName' : $('#empName').val(),
      'warehouse_code' : $('#warehouse').val(),
      'budget_id' : $('#budget-id').val(),
      'budget_code' : $('#budget-code').val(),
      'budget_amount' : parseDefault(parseFloat($('#budget-amount').data('amount')), 0),
      'transformed' : $('#transformed').val(),
      'remark' : $('#remark').val().trim()
    };

    if(h.customer_code.length == 0) {
      $('#customerCode').hasError();
      click = 0;
      return false;
    }

    if(h.customer_name.length == 0) {
      $('#customer').hasError();
      click = 0;
      return false;
    }

    if( ! isDate(h.date_add))
    {
      $('#datae').hasError();
      click = 0;
      return false;
    }

    if(h.budget_id == 0 || h.budget_id == null || h.budget_id == "") {
      $('#budget-amount').hasError();
      click = 0;
      return false;
    }

    if(h.budget_amount <= 0) {
      $('#budget-amount').hasError();
      click = 0;
      return false;
    }

    if(h.empName.length == 0)
    {
      $('#empName').hasError();
      click = 0;
      return false;
    }

    if(h.warehouse_code == "") {
      $('#warehouse').hasError();
      swal('กรุณาเลือกคลัง');
      click = 0;
      return false;
    }

    load_in();

    $.ajax({
      url:BASE_URL + 'orders/sponsor/add',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
            window.location.href = BASE_URL + 'orders/sponsor/edit_detail/'+ ds.code;
          }
          else {
            beep();
            showError(ds.message);
          }
        }
        else {
          beep();
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        showError(rs);
      }
    });
  }

}


function validateOrder() {
  var prefix = $('#prefix').val();
  var runNo = parseInt($('#runNo').val());
  let code = $('#code').val();

  if(code.length == 0){
    $('#addForm').submit();
    return false;
  }

  let arr = code.split('-');

  if(arr.length == 2){
    if(arr[0] !== prefix){
      swal('Prefix ต้องเป็น '+prefix);
      return false;
    }else if(arr[1].length != (4 + runNo)){
      swal('Run Number ไม่ถูกต้อง');
      return false;
    }else{
      $.ajax({
        url: BASE_URL + 'orders/orders/is_exists_order/'+code,
        type:'GET',
        cache:false,
        success:function(rs){
          if(rs == 'not_exists'){
            $('#addForm').submit();
          }else{
            swal({
              title:'Error!!',
              text: rs,
              type: 'error'
            });
          }
        }
      })
    }

  }else{
    swal('เลขที่เอกสารไม่ถูกต้อง');
    return false;
  }
}


var customer;
var channels;
var payment;
var date;


function getEdit(){
  let approved = $('#is_approved').val();
  if(approved == 1){
    $('#remark').removeAttr('disabled');
  } else {
    $('.edit').removeAttr('disabled');
  }

  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
  customer = $("#customerCode").val();
	channels = $("#channels").val();
	payment  = $("#payment").val();
	date = $("#date").val();
}


//---- เพิ่มรายการสินค้าเช้าออเดอร์
function addToOrder(){
  var order_code = $('#order_code').val();
	//var count = countInput();
  var data = [];
  $(".order-grid").each(function(index, element){
    if($(this).val() != ''){
      var code = $(this).attr('id');
      var arr = code.split('qty_');
      data.push({'code' : arr[1], 'qty' : $(this).val()});
    }
  });

	if(data.length > 0 ){
		$("#orderGrid").modal('hide');
		$.ajax({
			url: BASE_URL + 'orders/orders/add_detail/'+order_code,
			type:"POST",
      cache:"false",
      data: {
        'data' : data
      },
			success: function(rs){
				load_out();
				var rs = $.trim(rs);
				if( rs == 'success' ){
					swal({
            title: 'success',
            type: 'success',
            timer: 1000
          });
					$("#btn-save-order").removeClass('hide');
					updateDetailTable(); //--- update list of order detail
				}else{
					swal("Error", rs, "error");
				}
			}
		});
	}
}




// JavaScript Document
function updateDetailTable(){
	var order_code = $("#order_code").val();
	$.ajax({
		url: BASE_URL + 'orders/orders/get_detail_table/'+order_code,
		type:"GET",
    cache:"false",
		success: function(rs){
			if( isJson(rs) ){
				var source = $("#detail-table-template").html();
				var data = $.parseJSON(rs);
				var output = $("#detail-table");
				render(source, data, output);
			}
			else
			{
				var source = $("#nodata-template").html();
				var data = [];
				var output = $("#detail-table");
				render(source, data, output);
			}
		}
	});
}



function removeDetail(id, name){
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '" + name + "' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: BASE_URL + 'orders/orders/remove_detail/'+ id,
				type:"POST",
        cache:"false",
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({ title: 'Deleted', type: 'success', timer: 1000 });
						updateDetailTable();
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
  });
}




$("#pd-box").autocomplete({
	source: BASE_URL + 'auto_complete/get_style_code',
	autoFocus: true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    $(this).val(arr[0]);
  }
});




$('#pd-box').keyup(function(event) {
	if(event.keyCode == 13){
		var code = $(this).val();
		if(code.length > 0){
			setTimeout(function(){
				getProductGrid();
			}, 300);

		}
	}
});


$('#item-code').autocomplete({
	source:BASE_URL + 'auto_complete/get_product_code',
	minLength: 4,
	autoFocus:true
});

$('#item-code').keyup(function(e){
	if(e.keyCode == 13){
		var code = $(this).val();
		if(code.length > 4){
			setTimeout(function(){
				getItemGrid();
			}, 200);
		}
	}
});


$('#input-qty').keyup(function(e){
	if(e.keyCode == 13){
		addItemToOrder();
	}
});


//---- เพิ่มรายการสินค้าเช้าออเดอร์
function addItemToOrder(){
	var orderCode = $('#order_code').val();
	var qty = parseDefault(parseInt($('#input-qty').val()), 0);
	var limit = parseDefault(parseInt($('#stock-qty').val()), 0);
	var itemCode = $('#item-code').val();
  var data = [{'code':itemCode, 'qty' : qty}];

	if(qty > 0 && qty <= limit){
		load_in();
		$.ajax({
			url:BASE_URL + 'orders/orders/add_detail/'+orderCode,
			type:"POST",
			cache:"false",
			data:{
				'data' : data
			},
			success: function(rs){
				load_out();
				var rs = $.trim(rs);
				if( rs == 'success' ){
					swal({
						title: 'success',
						type: 'success',
						timer: 1000
					});

					$("#btn-save-order").removeClass('hide');
					updateDetailTable(); //--- update list of order detail

					setTimeout(function(){
						$('#item-code').val('');
						$('#stock-qty').val('');
						$('#input-qty').val('');
						$('#item-code').focus();
					},1200);


				}else{
					swal("Error", rs, "error");
				}
			}
		});
	}
}




//--- ตรวจสอบจำนวนที่คีย์สั่งใน order grid
function countInput(){
	var qty = 0;
	$(".order-grid").each(function(index, element) {
        if( $(this).val() != '' ){
			qty++;
		}
  });
	return qty;
}



function validUpdate(){
	var date_add = $("#date").val();
	var customer_code = $("#customerCode").val();
  var customer_name = $('#customer').val();
	var user_ref = $("#user_ref").val();
  var warehouse = $('#warehouse').val();

	//---- ตรวจสอบวันที่
	if( ! isDate(date_add) ){
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	//--- ตรวจสอบลูกค้า
	if( customer_code.length == 0 || customer_name == "" ){
		swal("ชื่อลูกค้าไม่ถูกต้อง");
		return false;
	}

  if(user_ref == ""){
    swal('กรุณาระบุผู้เบิก[ผู้สั่งงาน]');
    return false;
  }


  if(warehouse == ""){
    swal("กรุณาเลือกคลัง");
    return false;
  }

  updateOrder();
}





function updateOrder() {
  let h = {
    'code' : $('#order_code').val(),
    'customer_code' : $('#customerCode').val(),
    'customer_name' : $('#customer').val(),
    'date_add' : $('#date').val(),
    'empName' : $('#user_ref').val(),
    'warehouse_code' : $('#warehouse').val(),
    'transformed' : $('#transformed').val(),
    'remark' : $('#remark').val()
  };


  if(h.customer_code.length == 0 || h.customer_name.length == 0) {
    swal('ชื่อผู้รับไม่ถูกต้อง');
    return false;
  }

  if( ! isDate(h.date_add))
  {
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  if(h.empName.length == 0)
  {
    swal('ชื่อผู้เบิกไม่ถูกต้อง');
    return false;
  }

  if(h.warehouse_code == ""){
    swal('กรุณาเลือกคลัง');
    return false;
  }

	load_in();

	$.ajax({
		url:BASE_URL + 'orders/sponsor/update_order',
		type:"POST",
		cache:"false",
		data:{
      "data" : JSON.stringify(h)
    },
		success: function(rs) {
			load_out();

			if( rs == 'success' ){
				swal({
          title: 'Done !',
          type: 'success',
          timer: 1000
        });

				setTimeout(function(){
          window.location.reload();
        }, 1200);

			}else{
				swal({
          title: "Error!",
          text: rs,
          type: 'error'
        });
			}
		}
	});
}



function recalDiscount(){
	updateOrder(1);
}



// JavaScript Document
function changeState(){
  var order_code = $("#order_code").val();
  var state = $("#stateList").val();
  var id_address = $('#address_id').val();
  var id_sender = $('#id_sender').val();
  var trackingNo = $('#trackingNo').val();
  var tracking = $('#tracking').val();
  var reason_id = $('#reason-id').val();
  var cancle_reason = $.trim($('#cancle-reason').val());
  let force_cancel = $('#force-cancel').is(':checked') ? 1 : 0;

  if(state == 9 && cancle_reason.length < 10) {
    showCancleModal();
    return false;
  }

  if( state != 0){
    load_in();
    $.ajax({
      url:BASE_URL + 'orders/orders/order_state_change',
      type:"POST",
      cache:"false",
      data:{
        "order_code" : order_code,
        "state" : state,
        "id_address" : id_address,
        "id_sender" : id_sender,
        "tracking" : tracking,
        "reason_id" : reason_id,
        "cancle_reason" : cancle_reason,
        "force_cancel" : force_cancel
      },
      success:function(rs){
        load_out();
        var rs = $.trim(rs);
        if(rs == 'success'){
          swal({
            title:'success',
            text:'status updated',
            type:'success',
            timer: 1000
          });

          setTimeout(function(){
            window.location.reload();
          }, 1500);

        }
        else {
          swal({
            title:"Error !",
            text:rs,
            type: "error",
            html:true
          }, function() {
            window.location.reload();
          });
        }
      },
      error:function(xhr, status, error) {
        load_out();
        swal({
          title:'Error!',
          text:xhr.responseText,
          type:'error',
          html:true
        }, function() {
          window.location.reload();
        })
      }
    });
  }
}




function setNotExpire(option){
  var order_code = $('#order_code').val();
  load_in();
  $.ajax({
    url:BASE_URL + 'orders/orders/set_never_expire',
    type:'POST',
    cache:'false',
    data:{
      'order_code' : order_code,
      'option' : option
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer: 1000
        });

        setTimeout(function(){
          window.location.reload();
        },1500);
      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}

function unExpired(){
  var order_code = $('#order_code').val();
  load_in();
  $.ajax({
    url:BASE_URL + 'orders/orders/un_expired',
    type:'GET',
    cache:'false',
    data:{
      'order_code' : order_code
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success')
      {
        swal({
          title:'Success',
          type:'success',
          timer: 1000
        });

        setTimeout(function(){
          window.location.reload();
        },1500);
      }
      else
      {
        swal('Error', rs, 'error');
      }
    }
  })
}
