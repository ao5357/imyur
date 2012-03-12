jQuery(document).ready(function($){
	if(window.localStorage){
		localStorage.setItem("test","stuff");
		console.log(window.localStorage);
		}
});