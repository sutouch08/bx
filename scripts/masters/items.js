function addNew(){
  window.location.href = HOME + 'add_new';
}

function getEdit(id){
	window.location.href = HOME + 'edit/'+id;
}

function viewDetail(id) {
  window.location.href = HOME + 'view_detail/'+id;
}


function add() {
  $('.r').removeClass('has-error');
  $('.e').text('');

	let error = 0;

	let h = {
    'code' : $('#code').val().trim(),
    'name' : $('#name').val().trim(),
    'style' : $('#style').val().trim(),
    'color_code' : $('#color').val().trim(),
    'size_code' : $('#size').val().trim(),
    'barcode' : $('#barcode').val().trim(),
    'cost' : parseDefault(parseFloat($('#cost').val()), 0),
    'price' : parseDefault(parseFloat($('#price').val()), 0),
    'unit_code' : $('#unit_code').val(),
    'brand_code' : $('#brand').val(),
    'main_group_code' : $('#mainGroup').val(),
    'group_code' : $('#group').val(),
    'sub_group_code' : $('#subGroup').val(),
    'category_code' : $('#category').val(),
    'kind_code' : $('#kind').val(),
    'type_code' : $('#type').val(),
    'collection_code' : $('#collection').val(),
    'year' : $('#year').val(),
    'count_stock' : $('#count_stock').is(':checked') ? 1 : 0,
    'can_sell' : $('#can_sell').is(':checked') ? 1 : 0,
    'is_api' : $('#is_api').is(':checked') ? 1 : 0,
    'active' : $('#active').is(':checked') ? 1 : 0
  };


  if(h.code.length === 0) {
    set_error($('#code'), $('#code-error'), "required");
		error++;
  }

	if(h.name.length === 0) {
		set_error($('#name'), $('#name-error'), "required");
		error++;
	}

	if(error > 0) {
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			"data" : JSON.stringify(h)
		},
		success:function(rs) {
			load_out();
			var rs = rs.trim();
			if(rs == 'success') {
				swal({
					title:"Success",
					type:'success',
					timer:1000
				});

        setTimeout(() => {
          addNew();
        }, 1200);
			}
			else {
				showError(rs);
			}
		},
		error:function(rs) {
			showError(rs);
		}
	})
}


function update() {
  $('.r').removeClass('has-error');
  $('.e').text('');

  let h = {
    'code' : $('#code').val().trim(),
    'name' : $('#name').val().trim(),
    'style' : $('#style').val().trim(),
    'color_code' : $('#color').val().trim(),
    'size_code' : $('#size').val().trim(),
    'barcode' : $('#barcode').val().trim(),
    'cost' : parseDefault(parseFloat($('#cost').val()), 0),
    'price' : parseDefault(parseFloat($('#price').val()), 0),
    'unit_code' : $('#unit_code').val(),
    'brand_code' : $('#brand').val(),
    'main_group_code' : $('#mainGroup').val(),
    'group_code' : $('#group').val(),
    'sub_group_code' : $('#subGroup').val(),
    'category_code' : $('#category').val(),
    'kind_code' : $('#kind').val(),
    'type_code' : $('#type').val(),
    'collection_code' : $('#collection').val(),
    'year' : $('#year').val(),
    'count_stock' : $('#count_stock').is(':checked') ? 1 : 0,
    'can_sell' : $('#can_sell').is(':checked') ? 1 : 0,
    'is_api' : $('#is_api').is(':checked') ? 1 : 0,
    'active' : $('#active').is(':checked') ? 1 : 0
  };

	if(h.name.length === 0) {
    $('#name').hasError('required');
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			"data" : JSON.stringify(h)
		},
		success:function(rs) {
			load_out();
			if(rs == 'success') {
				swal({
					title:"Success",
					type:'success',
					timer:1000
				});
			}
			else {
				showError(rs);
			}
		},
		error:function(rs) {
			showError(rs);
		}
	})
}


function duplicate(id){
  window.location.href = HOME + 'duplicate/'+id;
}


function addDuplicate() {
  $('.r').removeClass('has-error');
  $('.e').text('');

	let error = 0;

  let h = {
    'c_code' : $('#c-code').val(),
    'code' : $('#code').val().trim(),
    'name' : $('#name').val().trim(),
    'style' : $('#style').val().trim(),
    'color_code' : $('#color').val().trim(),
    'size_code' : $('#size').val().trim(),
    'barcode' : $('#barcode').val().trim(),
    'cost' : parseDefault(parseFloat($('#cost').val()), 0),
    'price' : parseDefault(parseFloat($('#price').val()), 0),
    'unit_code' : $('#unit_code').val(),
    'brand_code' : $('#brand').val(),
    'main_group_code' : $('#mainGroup').val(),
    'group_code' : $('#group').val(),
    'sub_group_code' : $('#subGroup').val(),
    'category_code' : $('#category').val(),
    'kind_code' : $('#kind').val(),
    'type_code' : $('#type').val(),
    'collection_code' : $('#collection').val(),
    'year' : $('#year').val(),
    'count_stock' : $('#count_stock').is(':checked') ? 1 : 0,
    'can_sell' : $('#can_sell').is(':checked') ? 1 : 0,
    'is_api' : $('#is_api').is(':checked') ? 1 : 0,
    'active' : $('#active').is(':checked') ? 1 : 0
  };

  if(h.code.length === 0) {
    set_error($('#code'), $('#code-error'), "required");
		error++;
  }

  if(h.c_code == h.code) {
    set_error($('#code'), $('#code-error'), "รหัสซ้ำ");
		error++;
  }

	if(h.name.length === 0) {
		set_error($('#name'), $('#name-error'), "required");
		error++;
	}

	if(error > 0) {
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			"data" : JSON.stringify(h)
		},
		success:function(rs) {
			load_out();
			var rs = rs.trim();
			if(rs == 'success') {
				swal({
					title:"Success",
					type:'success',
					timer:1000
				});

        setTimeout(() => {
          addNew();
        }, 1200);
			}
			else {
				showError(rs);
			}
		},
		error:function(xhr) {
			showError(rs);
		}
	})
}


$('#style').autocomplete({
  source: BASE_URL + 'auto_complete/get_style_code',
  autoFocus:true,
  close:function() {
    let rs = $(this).val();
    let arr = rs.split(' | ');
    if(arr.length == 2) {
      $(this).val(arr[0]);
    }
    else {
      $(this).val('');
    }
  }
});

$('#color').autocomplete({
  source: BASE_URL + 'auto_complete/get_color_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var err = rs.split(' | ');
    if(err.length == 2){
      $(this).val(err[0]);
    }else{
      $(this).val('');
    }
  }
});


$('#size').autocomplete({
  source:BASE_URL + 'auto_complete/get_size_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var err = rs.split(' | ');
    if(err.length == 2){
      $(this).val(err[0]);
    }else{
      $(this).val('');
    }
  }
});


function clearFilter(){
  var url = HOME + 'clear_filter';
  var page = BASE_URL + 'masters/products';
  $.get(url, function(){
    goBack();
  });
}


function getDelete(id, code, no){
  let url = BASE_URL + 'masters/items/delete_item/';// + encodeURIComponent(code);
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + code + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: url,
      type:'GET',
      cache:false,
      data:{
        'id' : id
      },
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            type:'success',
            timer:1000
          });

          $('#row-'+no).remove();
        }else{
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    })

  })
}

function getTemplate(){
  var token	= new Date().getTime();
	get_download(token);
	window.location.href = BASE_URL + 'masters/items/download_template/'+token;
}

function getSearch(){
  $('#searchForm').submit();
}
