function addNew(){
  window.location.href = HOME + 'add_new';
}


function goBack(){
  window.location.href = HOME;
}


function getEdit(code){
  window.location.href = HOME + 'edit/'+code;
}


function update() {
  clearErrorByClass('e')
  let code = $('#code').val().trim();
  let name = $('#name').val().trim();

  if(name.length == 0) {
    $('#name').hasError();
    return false;
  }

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'name' : name
    },
    success:function(rs) {
      if(rs.trim() == 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });
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
  })
}


function getDelete(code, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },
  function(){
    window.location.href = HOME + 'delete/' + code;
  })
}
