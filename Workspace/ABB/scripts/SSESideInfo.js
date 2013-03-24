var ssesource = null;

$(function(){

	if(typeof(EventSource)!=="undefined")
	{
		ssesource = new EventSource("/SSevents/BasicInfo");
		ssesource.addEventListener("pointsize", function (event) {
			$("#pointsize").html(event.data);
		}, true);

		ssesource.addEventListener("usedmemory", function (event) {
			$("#usedmemory").html(event.data + "k");
		}, true);
		
		ssesource.addEventListener("clustersize", function (event) {
			$("#clustersize").html(event.data);
		}, true);

	}
});
