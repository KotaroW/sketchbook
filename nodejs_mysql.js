function Database() {

	var connection = null;

	this.init = function(dbfunc) {
		dbfunc = dbfunc ? dbfunc : this.connect;
		var schema = {
			properties : {
				host : {
					required : true
				},
				user : {
					required : true
				},
				password : {
					hidden : true
				}
			}
		};
		var prompt = require("prompt");

		prompt.start();
		prompt.get(schema, function(err, result) {
			if (err) {
				throw err;
			}
			else {
				dbfunc(result.host, result.user, result.password);
			}
		});
	}

	this.connect = function(host, user, password) {
		connection = (require("mysql")).createConnection({
			host : host,
			user : user,
			password : password
		});
		connection.connect(function(err) {
			if (err) {
				console.log("MySQL: Authentication failed");
			}
			else {
				console.log("connected");
				runDatabase();
			}
		});
	}

	function runDatabase() {
		var interface = (require("readline")).createInterface({
			input : process.stdin,
			output : process.stdout
		});

		interface.question("mysql>> ", function(answer) {
			var query = answer.trim();
			interface.close();
			if(query.toLowerCase() != "quit") {
				connection.query(query, function(err, result) {
					if (err) {
						console.log(err.toString());
					}
					else {
						console.log(result);
					}
					runDatabase();
				});
			}
			else {
				console.log("bye");
				connection.end();
			}
		});
	}
}

(new Database()).init();
