jQuery(document).ready(function($){
	var $imyurls = $("#imyurls");
	if(window.localStorage){
		$.each(window.localStorage,function(imyurl,original){
			$imyurls.removeClass("hide").prepend('<tr><td>' + imyurl + '</td><td>' + original + '</td></tr>');
			});
		}
	
	var $shorten = $("#shorten");
	$shorten.on("submit",function(e){
		var submit = $shorten.find('[type="submit"]').attr("disabled","disabled");
		var params = $shorten.serializeArray();
		$.each(params,function(i,field){
			switch(field.name){
				case "url":
					field.value = $.trim(field.value.replace(/[^a-z0-9-~+_.?\[\]\^#=!&;,\/:%@$\|*\'"()\\x80-\\xff]/i,''));
					break;
				case "extension":
					field.value = $.trim(field.value.replace(/[^a-zA-Z0-9]/i,'').substr(0,25));
					break;
				}
			});
		$.post("/api/v1/shorten.json",$.param(params))
			.done(function(data){
				$imyurls.removeClass("hide").prepend('<tr><td>test</td><td>test</td></tr>');
				console.log(data);
				// if(window.localStorage){}
				})
			.fail(function(data){
				if(data.error){alert('There was an error. Code: "' + data.error + '". Please try again.');}
				else{alert('There was an unspecified error. Please try again.');}	
				})
			.always(function(){
				submit.removeAttr("disabled");
				});
		return false;
		});
});