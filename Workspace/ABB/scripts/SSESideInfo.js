$(function(){

	var ssesource = null;

	if(typeof(EventSource)!=="undefined")
	{

		$("#LiveUpdateButton").click(function(){
			if(ssesource == null || ssesource.readyState == EventSource.CLOSED){
				ssesource = new EventSource("/SSevents/BasicInfo");

				for(i = 0; i < SSENames.length; i++){
					ssesource.addEventListener(SSENames[i], SSEHandlers[i], true);
				}

				$("#LiveUpdateIcon").removeClass("icon-play").addClass("icon-pause");
			}
			else{
				ssesource.close();
				$("#LiveUpdateIcon").removeClass("icon-pause").addClass("icon-play");
			}
		});

		var SSENames = new Array();
		var SSEHandlers = new Array();

		function addSSEvent(eventname, handler) {
			SSENames.push(eventname);
			SSEHandlers.push(handler);
			if(ssesource != null || ssesource.readyState != EventSource.CLOSED){
				ssesource.addEventListener(eventname, handler, true);
			}
		}

		ssesource = new EventSource("/SSevents/BasicInfo");

		addSSEvent("pointsize", function (event) {
			$("#pointsize").html(event.data);
		});

		addSSEvent("usedmemory", function (event) {
			$("#usedmemory").html(event.data + "k");
		});

		addSSEvent("clustersize", function (event) {
			$("#clustersize").html(event.data);
		});
	}
});
