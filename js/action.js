function cpasswd(){
	
	if ( $('#newpasswd').val() == $('#repasswd').val() ) {
		
		//alert( $('#newpasswd').val() + $('#oldpasswd').val());
		$.ajax({  
			type: "POST",  
			url: "ajax.php?m=cpasswd",  
			data: { oldpasswd: $('#oldpasswd').val() , newpasswd: $('#newpasswd').val() },  
			success: function(data) { alert(data); }
		});
	}
}

function del(p){
		
		//alert( p );
		$.ajax({  
			type: "POST",  
			url: "ajax.php?m=del",  
			data: { hash : p },  
			success: function(data) { 
						alert(data); 
						window.location.reload();
						}
		});
}


function delcustomer(id){

    $.ajax({
        type: "POST",
        url: "ajax.php?m=delcustomer",
        data: { id : id },
        success: function(data) {
            alert(data);
            window.location.reload();
        }
    });
}

function delinfo(id){

    $.ajax({
        type: "POST",
        url: "ajax.php?m=delinfo",
        data: { id : id },
        success: function(data) {
            alert(data);
            window.location.reload();
        }
    });
}

function delall(id){

    $.ajax({
        type: "POST",
        url: "ajax.php?m=delall",
        data: { id : id },
        success: function(data) {
            alert(data);
            window.location.reload();
        }
    });
}

function delspider(id){

    $.ajax({
        type: "POST",
        url: "ajax.php?m=delspider",
        data: { id : id },
        success: function(data) {
            alert(data);
            window.location.reload();
        }
    });
}

function resetall(id){

    $.ajax({
        type: "POST",
        url: "ajax.php?m=resetall",
        data: { id : id },
        success: function(data) {
            alert(data);
            window.location.reload();
        }
    });
}
function resetscan(id){

    $.ajax({
        type: "POST",
        url: "ajax.php?m=resetscan",
        data: { id : id },
        success: function(data) {
            alert(data);
            window.location.reload();
        }
    });
}
function resetspider(id){

    $.ajax({
        type: "POST",
        url: "ajax.php?m=resetspider",
        data: { id : id },
        success: function(data) {
            alert(data);
            window.location.reload();
        }
    });
}
function resetinfo(id){

    $.ajax({
        type: "POST",
        url: "ajax.php?m=resetinfo",
        data: { id : id },
        success: function(data) {
            alert(data);
            window.location.reload();
        }
    });
}


function search11(id){

    $.ajax({
        type: "POST",
        url: "ajax.php?m=search11",
        data: { id : id },
        success: function(data) {
            alert(data);
            //window.location.reload();
        }
    });
}

function exportreport(p){
		window.location.href = 'ajax.php?m=export&hash=' + p;
}