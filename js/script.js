$(document).on("click", "#delete_article", function(e){

	e.preventDefault();

	var db = $(this).attr("db");
	var session = $("body").attr("session");

	$(this).find("button").prop("disabled", true);

	$.ajax({
		url: "/process/delete_article.php",
		data: {
			session: session,
			db: db
		},
		type: "POST",
		success: function(data){

			var href = $("#menu a:not(.active):first").attr("href");
			window.location.href = href;

		}
	})



});

$(document).on("submit", "#article_insert", function(e){

	e.preventDefault();

	var url = $("#article_url").val();

	if (url.indexOf("https://en.wikipedia.org/wiki") != 0) {

		alert("Incorrect URL. The URL must start with 'https://en.wikipedia.org/wiki/'.");

	} else if (url.indexOf("#") != -1) {

		alert("Hashtag anchor detected. Please remove from the URL.");

	} else {

		$(this).find("button").prop("disabled", true);

		var title = url.replace("https://en.wikipedia.org/wiki/", "");
		var session = $("body").attr("session");

		$.ajax({
			url: "/process/post_article.php",
			data: {
				session: session,
				title: title
			},
			type: "POST",
			success: function(data){

				data = $.trim(data);

				window.location.href = "?id="+session+"&article="+data;

			}
		})


	}


});

$(document).on("click", "#switch a", function(e){

	e.preventDefault();
	var url = $(this).attr("href");

	$("#fulltext, #plaintext, #html").hide();
	$(url).show();

	$("#switch a").removeClass("active");
	$(this).addClass("active");

});


$(window).on("load", function(){

	resizeFulltext();
	cleanFulltext();

});

$(window).on("resize", function(){

	resizeFulltext();

});

function resizeFulltext(){

	var height = $(window).height() - $("nav").outerHeight() - 15 - 15;
	$("#details, #left").css("height", height+"px");

	height -= $("#switch").outerHeight(true);



	$("#fulltext, #html, #plaintext, #occurrence").css("height", height+"px");

}

function getIndicesOf(searchStr, str, caseSensitive) {
    var searchStrLen = searchStr.length;
    if (searchStrLen == 0) {
        return [];
    }
    var startIndex = 0, index, indices = [];
    if (!caseSensitive) {
        str = str.toLowerCase();
        searchStr = searchStr.toLowerCase();
    }
    while ((index = str.indexOf(searchStr, startIndex)) > -1) {
        indices.push(index);
        startIndex = index + searchStrLen;
    }
    return indices;
}


function cleanFulltext(){

	if ($("#fulltext").length > 0) {

		var text = $("#plaintext").text().replace(/\n/g, "<br>");
		$("#plaintext").html(text);


		$("#fulltext a.loc").each(function(){

			var text = $(this).text().trim();

			if (text == "") {

				$(this).remove();

			}

		});

		var locations = {};
		var geonames = {};

		var lengthList = {};
		var sortable = [];

		$("#fulltext a.loc").each(function(){

			var displayText = $(this).attr("display").trim();
			var href = $(this).attr("href").trim();

			lengthList[displayText] = href;

			if (sortable.indexOf(displayText) === -1) {

				sortable.push(displayText);

			}

		});

		sortable.sort(function(a, b){
			return b.length - a.length;
		});

		console.log(sortable);

		var text = $("#plaintext").text();

		$.each(sortable, function(i,o){


			var indices = getIndicesOf(o, text);

			var links = "";

			$.each(indices, function(j,k){


				var snippet = "..." + text.substr(k-100, 100) + "<<" + o.toUpperCase() + ">>" + text.substr(k+o.length, 100) + "...";

				console.log(snippet);

				snippet = unescape(encodeURIComponent(snippet));

				links += "<a href='#' class='index_number' length='"+o.length+"' index='"+k+"' snippet='"+window.btoa(snippet)+"'>"+k+"</a> ";



			});

			var link = lengthList[o];

			$("#occurrence_table tbody").append("<tr><td><a href='"+link+"' target='_blank'>"+o+"</a></td><td>"+links+"</td></tr>");



		});


		$("#occurrence_table").DataTable({

    		lengthMenu: [[-1], ["All"]],
    		scrollX: true

		});


		$("#fulltext a.loc").each(function(){

			var text = $(this).attr("source").trim()+":::"+$(this).attr("matched").trim();
			var gn = $(this).attr("loc");

			if (locations[text] == undefined) {

				locations[text] = 0;

			}

			locations[text]++;
			geonames[text] = gn;


		});

		console.log(locations);

		$.each(locations, function(i,o){

			var iArray = i.split(":::");

			$("#summary tbody").append("<tr gn='"+geonames[i]+"'><td>"+o+"</td><td>"+'<a href="https://en.wikipedia.org/wiki/'+iArray[0]+'" target="_blank">'+iArray[0]+"</a></td><td>"+'<a href="https://en.wikipedia.org/wiki/'+iArray[1]+'" target="_blank">'+iArray[1]+"</a></td><td><a href='https://www.google.ca/maps/place/"+geonames[i]+"' target='_blank'>"+geonames[i]+"</a></td></tr>")

		});

		$("#summary").DataTable({

    		lengthMenu: [[-1], ["All"]],
    		scrollX: true

		});

		return false;

		$("#summary tr[gn]").each(function(){

			var gn = $(this).attr("gn");

			$.ajax({
				url: "http://api.geonames.org/get?geonameId="+gn+"&username=chriskkim",
				success: function(data, status, jqXHR){

					console.log(data);

					var lat = $(data).find("lat").text();
					var lng = $(data).find("lng").text();
					var gnid = $(data).find("geonameId").text();

					$("#summary tbody tr[gn='"+gnid+"'] td:last").text(lat+", "+lng);
					$("#summary tbody tr:not(.complete):first").addClass("complete");

					if ($("#summary tbody tr").length == $("#summary tbody tr.complete").length) {



					}




				}
			})


		});

	}

}




$(document).on("click", "#api_submit", function(e){

	e.preventDefault();

	var text = $("#api_data").val().trim();
	var context = $("#api_context").val().trim();

	if (text != "") {

		var data = {};

		data.query_names = [];
		data.context_locations = [];

		if (context != "") {

			var obj = {};
			obj.name = "Test",
			obj.location = [context];

			data.context_locations.push(obj);

		}

		var array = text.split("\n");

		$.each(array, function(i,o){

			if (o != "") {

				data.query_names.push(o);

			}

		});

		var json = JSON.stringify(data);

		$("#api_input_raw").text(json);

		var curlJson = json.replace(/[']/g, "'\\''");

		$("#api_endpoint_raw").text("curl http://geowiki.ckprototype.com/api/search.php -X POST -d 'data="+curlJson+"'");

		$.ajax({
			url: "/api/search.php",
			data: {
				data: json
			},
			type: "POST",
			success: function(data){

				$("#api_output_raw").text(data);

				var test = JSON.parse(data);

				$("#api_output_array_raw").html(JSON.stringify(test, null, 2));


			}
		});


	} else {

		alert("Please enter at least one name.");

	}




});

function syntaxHighlight(json) {
    if (typeof json != 'string') {
         json = JSON.stringify(json, undefined, 2);
    }
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
        var cls = 'number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'boolean';
        } else if (/null/.test(match)) {
            cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}



$(document).on("click", ".index_number", function(e){

	e.preventDefault();

	var index = $(this).attr("index");
	var length = $(this).attr("index");
	var snippet = $(this).attr("snippet");

	var str2 = decodeURIComponent(escape(window.atob(snippet)));

	alert(str2);

});