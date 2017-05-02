process.exit();

require("jsdom").env("", function(err, window) {
    if (err) {
        console.error(err);
        return;
    }

	var mysql = require('mysql');

	var connection = mysql.createConnection({
	host     : 'localhost',
	user     : 'root',
	password : '',
	database : 'geowiki'
	});

    var $ = require("jquery")(window);

    count = 0;

    var list = [];

	connection.connect(function(err) {


		var query = connection.query('SELECT id, wikipedia, html FROM texts WHERE raw IS NOT null AND html IS NOT null AND loc IS NOT null LIMIT 0, 500', null, function(err, result) {

			for (var i = 0; i < result.length; i++) {

				var data = result[i].html;
				var id = result[i].id;

			    console.log("Starting "+id);

			    var obj = {};
			    obj.id = id;
			    obj.text = data;

			    list.push(obj);


			}

			//console.log(list);


			pushtodb(list);


		});



	});    

	function pushtodb(list){


		if (list.length == 0) {

			process.exit();

		} else {

			var text = list[0].text;
			var id = list[0].id;
		    var plain = $(text).text();

			connection.query('UPDATE texts SET plain = ? WHERE id = ?', [plain, id], function (error, results, fields) {

				count++;
				console.log(count);

				delete list[0];

				list = list.filter(function (item) {
					return item !== undefined;
				});

				pushtodb(list);

			});			 


		}


   


	}




});