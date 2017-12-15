<!DOCTYPE HTML>
<html>
<head>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<style>
#divSelect {
    width: 50px;
    height: 50px;
    padding: 10px;
	margin: auto;
    border: 3px solid #000000;
	overflow: hidden;
}
#divSelectOptions {
    width: 160px;
    height: 50px;
    padding: 10px;
	margin: auto;
    border: 3px solid #000000;
	float: right;
}
#divWrapper {
    width: 275px;
    height: 70px;
    padding: 10px;
	margin: auto;
    <!-- border: 3px solid #000000; -->
	overflow: hidden;
}
#divBe {
    width: 400px;
    height: 60px;
    padding: 10px;
	margin: auto;
    border: 3px solid #73AD21;
}
#divNotBe {
	width: 400px;
    height: 60px;
    padding: 10px;
	margin: auto;
    border: 3px solid #FF0000;
}
#divBin {
	width: 600px;
    height: 60px;
    padding: 10px;
	margin: auto;
    border: 3px solid #000000;
}
#divLogo {
	width: 400px;
    height: 120px;
	margin: auto;
}
#divButton {
	width: 60px;
    height: 10px;
	margin: auto;
	border: 20px solid #FFFFFF;
}
</style>
<script>
function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.getData("text");
	if ( event.target.nodeName !== "IMG" )
	{
		if(document.getElementById(data).id == 'Everything' || document.getElementById(data).id == 'bakery_name' || document.getElementById(data).id == 'pastry_name')
		{
			if(ev.target.id == 'divSelect' || ev.target.id == 'divSelectOptions') //moving to valid div
				ev.target.appendChild(document.getElementById(data));
		}
		else
		{
			if(ev.target.id == 'divBe' || ev.target.id == 'divNotBe' || ev.target.id == 'divBin') //moving to valid div
			{
				ev.target.appendChild(document.getElementById(data));
				
				var calorieValue = null;
				if(document.getElementById(data).id == 'Calories' && ev.target.id !== 'divBin')
				{
					while(!calorieValue || !(/^\d+$/.test(calorieValue))) //a number and not null
					{
						calorieValue = prompt("Calorie <", "300");
					}
					document.getElementById(data).title = calorieValue;
				}
			}
		}
	}
}
function childOf(/*child node*/c, /*parent node*/p){ //returns boolean
  while((c=c.parentNode)&&c!==p); 
  return !!c; 
}
function doQuery()
{
	//alert('hello!');
	var queryString = 'SELECT DISTINCT ';
	var divSelect = document.getElementById('divSelect');
	var divBe = document.getElementById('divBe');
    var divNotBe = document.getElementById('divNotBe');
	
	var childId = divSelect.childNodes[0].id;
	if(typeof(childId) !== 'undefined')
		if(childId == 'Everything')
			queryString = queryString + '* FROM pastry_info AS p INNER JOIN bakery_info AS b ON p.Bakery_info_bakery_id = b.bakery_id ';
		else if(childId == 'pastry_name')
			queryString = queryString + 'pastry_name FROM pastry_info AS p INNER JOIN bakery_info AS b ON p.Bakery_info_bakery_id = b.bakery_id ';
		else if(childId == 'bakery_name')
			queryString = queryString + 'b.*, pastry_name FROM pastry_info AS p INNER JOIN bakery_info AS b ON p.Bakery_info_bakery_id = b.bakery_id ';
	queryString = queryString + 'WHERE '
	
	var firstAnd = true; //dont put an and/or in front and remove WHERE if never toggled
	var firstPastry = true;
	var firstBakery = true;
	var pastryString = ''; //build sub OR conditions for like things
	var bakeryString = '';
	var calorieString = '';
	for (var ii = 0; ii < divBe.childNodes.length; ii++)
	{
		var childId = divBe.childNodes[ii].id;
		if(typeof(childId) !== 'undefined')
		{
			if(childId == 'Calories')
				if(firstAnd) //check if there was an and element first
				{
					var calorieVar = document.getElementById(childId);
					calorieString = calorieString + 'calories < ' + calorieVar.title + ' ';
					firstAnd = false;
				}
				else
				{
					if(!firstAnd && conditionalAnd) //there are other conditionals, add an and
					{
						queryString = queryString + 'AND ';
						conditionalAnd = false;
					}
					var calorieVar = document.getElementById(childId);
					calorieString = calorieString + 'calories < ' + calorieVar.title + ' ';
				}
			else if(childId == 'Cookie' || childId == 'Pie' || childId == 'Donut' || childId == 'Cake')
				if(firstAnd) //check if there was an and element first
				{
					pastryString = pastryString + 'Type = \'' + childId + '\' ';
					firstAnd = false;
					firstPastry = false;
				}
				else
					if(firstPastry)
					{
						pastryString = pastryString + 'Type = \'' + childId + '\' ';
						firstPastry = false;
					}
					else
						pastryString = pastryString + 'OR Type = \'' + childId + '\' ';
			else
				if(firstAnd) //check if there was an and element first
				{
					bakeryString = bakeryString + 'bakery_name Like \'%' + childId + '%\' ';
					firstAnd = false;
					firstBakery = false;
				}
				else
					if(firstBakery)
					{
						bakeryString = bakeryString + 'bakery_name Like \'%' + childId + '%\' ';
						firstBakery = false;
					}
					else
						bakeryString = bakeryString + 'OR bakery_name Like \'%' + childId + '%\' ';
		}
	}
	
	var conditionalAnd = false; //add an and if there were true elements added
	if(calorieString && (bakeryString || pastryString))
	{
		queryString = queryString + calorieString + 'AND ';
		conditionalAnd = true;
	}
	else if(calorieString)
	{
		queryString = queryString + calorieString;
		conditionalAnd = true;
	}
	if(bakeryString && pastryString) //combine sub OR's
	{
		queryString = queryString + '(' + pastryString.trim() + ') AND (' + bakeryString.trim() + ') ';
		conditionalAnd = true;
	}
	else if(bakeryString || pastryString)
	{
		queryString = queryString + '(' + pastryString.trim() + bakeryString.trim() + ') ';
		conditionalAnd = true;
	}
	
	//begin building NOT elements
	firstPastry = true;
	firstBakery = true;
	pastryString = ''; //build sub OR conditions for like things
	bakeryString = '';
	calorieString = '';
	for (var ii = 0; ii < divNotBe.childNodes.length; ii++)
	{
		var childId = divNotBe.childNodes[ii].id;
		if(typeof(childId) !== 'undefined')
		{
			if(childId == 'Calories')
				if(firstAnd) //check if there was an and element first
				{
					var calorieVar = document.getElementById(childId);
					calorieString = calorieString + 'NOT calories < ' + calorieVar.title + ' ';
					firstAnd = false;
				}
				else
				{
					if(!firstAnd && conditionalAnd) //there are other conditionals, add an and
					{
						queryString = queryString + 'AND ';
						conditionalAnd = false;
					}
					var calorieVar = document.getElementById(childId);
					calorieString = calorieString + 'NOT calories < ' + calorieVar.title + ' ';
				}
			else if(childId == 'Cookie' || childId == 'Pie' || childId == 'Donut' || childId == 'Cake')
						if(firstAnd) //check if there was an and element first
						{
							pastryString = pastryString + 'NOT Type = \'' + childId + '\' ';
							firstAnd = false;
							firstPastry = false;
						}
						else if(firstPastry)
						{
							if(!firstAnd && conditionalAnd) //there are other conditionals, add an and
							{
								queryString = queryString + 'AND ';
								conditionalAnd = false;
							}
							pastryString = pastryString + 'NOT Type = \'' + childId + '\' ';
							firstPastry = false;
						}
						else
							pastryString = pastryString + 'AND NOT Type = \'' + childId + '\' ';
			else if(firstAnd) //check if there was an and element first
			{
				bakeryString = bakeryString + 'NOT bakery_name Like \'%' + childId + '%\' ';
				firstAnd = false;
				firstBakery = false;
			}
			else if(firstBakery)
			{
				if(!firstAnd && conditionalAnd) //there are other conditionals, add an and
				{
					queryString = queryString + 'AND ';
					conditionalAnd = false;
				}
				bakeryString = bakeryString + 'NOT bakery_name Like \'%' + childId + '%\' ';
				firstBakery = false;
			}
			else
				bakeryString = bakeryString + 'AND NOT bakery_name Like \'%' + childId + '%\' ';
		}
	}
	
	if(calorieString && (bakeryString || pastryString))
	{
		queryString = queryString + calorieString + 'AND ';
	}
	else
		queryString = queryString + calorieString;
	if(bakeryString && pastryString) //combine sub OR's
	{
		queryString = queryString + '(' + pastryString.trim() + ') AND (' + bakeryString.trim() + ') ';
	}
	else if(bakeryString || pastryString)
	{
		queryString = queryString + '(' + pastryString.trim() + bakeryString.trim() + ') ';
	}
	
	if(firstAnd)//remove WHERE
		queryString = queryString.replace('WHERE', ' '); //remove WHERE if no conditions were added
		
	queryString = queryString.trim() + ';'; //trim white space off ends and add ';'

	document.getElementById('myTextarea').value = queryString;
	
	sendQuery(queryString);
}

function sendQuery(stringToSend) {
	$.post("interface.php", { query: stringToSend }, 
		function(data){
			//alert("Data: " + data);// + "\nStatus: " + status);
			$("#output").html(data);
		});
}

</script>
</head>
<body>

<!--<h1 align="center">Pastry Database</h1>-->
<div id="divLogo"><img src="DBLogo.png" width="400" height="120"></div>

<h3 align="center">Show me...</h3>
<div id="divWrapper">
	<div id="divSelectOptions" ondrop="drop(event)" ondragover="allowDrop(event)">
		<img id="Everything" src="EVERYTHING.png" draggable="true" ondragstart="drag(event)" width="50" height="50">
		<img id="bakery_name" src="bakery_that_sells.png" draggable="true" ondragstart="drag(event)" width="50" height="50">
		<img id="pastry_name" src="pastry_name.png" draggable="true" ondragstart="drag(event)" width="50" height="50">
	</div>
	<div id="divSelect" ondrop="drop(event)" ondragover="allowDrop(event)"></div>
</div>

<h3 align="center">I want it to be...</h3>
<div id="divBe" ondrop="drop(event)" ondragover="allowDrop(event)"></div>
<h3 align="center">But not to be...</h3>
<div id="divNotBe" ondrop="drop(event)" ondragover="allowDrop(event)"></div>
<h3 align="center">Choose from these elements</h3>
<div id="divBin" ondrop="drop(event)" ondragover="allowDrop(event)">
	<img id="Cookie" src="ChocoChipCookie.jpeg" draggable="true" ondragstart="drag(event)" width="50" height="50">
	<img id="Pie" src="Pie.gif" draggable="true" ondragstart="drag(event)" width="50" height="50">
	<img id="Donut" src="Donut.png" draggable="true" ondragstart="drag(event)" width="50" height="50">
	<img id="Cake" src="Cake.png" draggable="true" ondragstart="drag(event)" width="50" height="50">
	<img id="Oscars_Ovens" src="Oscars_Ovens.jpeg" draggable="true" ondragstart="drag(event)" width="50" height="50">
	<img id="Erwins_Eatery" src="Erwins_Eatery.jpeg" draggable="true" ondragstart="drag(event)" width="50" height="50">
	<img id="Donovans_Delicacy" src="Donovans_Delicacies.jpeg" draggable="true" ondragstart="drag(event)" width="50" height="50">
	<img id="Billys_Bakery" src="billys_bakery.jpeg" draggable="true" ondragstart="drag(event)" width="50" height="50">
	<img id="Calories" src="Calories.jpg" title="300" draggable= "true" ondragstart="drag(event)" width="50" height="50">
</div>
<div id="divButton"><button id="queryButton" onclick="doQuery();">Search</button></div>
<h3>Results</h3>
<div id="output"> </div>
<textarea id="myTextarea" rows="6" cols="80">
Results will be shown here.
</textarea>

</body>
</html>
