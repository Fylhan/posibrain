$(document).ready(function(){
	$("#sendMsg").click(function(){
		$('#msgLabel').css({'color': 'black'});
		var pseudo = $('#pseudo').val();
		var msg = $('#msg').val();
		if (pseudo == '') {
			pseudo = 'Anonymous';
		}
		if (msg == '') {
			$('#msgLabel').css({'color': 'red'});
			return false;
		}
		if (msg != '') {
			$('#discussion').append('<strong>'+pseudo+'</strong>: '+msg+'<br />');
			$('#msg').val('');
            $.getJSON('../app/submit/sammy', {pseudo:pseudo, msg:msg}, function(result){
                if ('' != result && result.status && '' != result.data) {
                	var data = result.data;
                    $('#discussion').append('<strong>'+data.pseudo+'</strong>: '+data.msg+'<br /><br />');
                }
                else {
                    console.log('Gloups!', result);
                }
             });
        }
        return false;
	});
});