$(document).ready(function(){
	$('.askDel').each(function(){
			$(this).click(function(){
				var answer = confirm($(this).attr('alt'));
				return answer;
			});

		});
});
