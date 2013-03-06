$(function(){

	if(typeof(EventSource)!=="undefined")
	{
		var source=new EventSource("/SSevents/BasicInfo");
		source.addEventListener("cachesize", function (event) {
			$("#cachesize").html(event.data);
		}, true);

		source.addEventListener("memorysize", function (event) {
			$("#memorysize").html(event.data + "k");
		}, true);

	}
});
