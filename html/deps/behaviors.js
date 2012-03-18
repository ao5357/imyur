jQuery(document).ready(function($){
	var $imyurls = $("#imyurls");
	if(window.localStorage){
		$.each(window.localStorage,function(i,storedObj){
			$imyurls.removeClass("hide").prepend('<tr><td>' + storedObj + '</td><td>' + localStorage.key(i) + '</td></tr>');
			});
		}
	
	var $shorten = $("#shorten");
	$shorten.on("submit",function(e){
		var submit = $shorten.find('[type="submit"]').attr("disabled","disabled");
		var params = $shorten.serializeArray(),saniParams = {};
		$.each(params,function(i,field){
			switch(field.name){
				case "url":
					field.value = $.trim(field.value.replace(/[^a-z0-9-~+_.?\[\]\^#=!&;,\/:%@$\|*\'"()\\x80-\\xff]/i,''));
					saniParams.url = field.value;
					break;
				case "extension":
					field.value = $.trim(field.value.replace(/[^a-zA-Z0-9]/i,'').substr(0,25));
					saniParams.extension = (field.value.length) ? '.' + field.value : '';
					break;
				case "subdomain":
					saniParams.subdomain = (field.value.length) ? field.value + '.' : '';
				}
			});
		$.post("/api/v1/shorten.json",$.param(params))
			.done(function(data){
				var imyurl = 'http://' + saniParams.subdomain + 'imyur.com/' + data.hash + saniParams.extension;
				$imyurls.removeClass("hide").prepend('<tr><td>' + imyurl + '</td><td>' + saniParams.url + '</td></tr>');
				if(window.localStorage){
					localStorage.setItem(imyurl,saniParams.url);
					}
				$shorten[0].reset();
				})
			.fail(function(){
				alert('There was an error. Please try again.');
				})
			.always(function(){
				submit.removeAttr("disabled");
				});
		return false;
		});
});