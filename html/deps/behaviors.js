jQuery(document).ready(function($){
	if(window.localStorage){
		localstorage.setItem("test","stuff");
		console.log(window.localStorage);
		}
});