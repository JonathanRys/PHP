<?php


function type($val){
	switch($val){
		case "other":
			return 10;
		break;
		case "bus":
			return 3;
		break;
		case "token":
			return 2;
		break;
		case "ring":
			return 3;
		break;
		case "mesh":
			return 5;
		break;
		case "star":
			return 2;
		break;
		case "fully":
			return 20;
		break;
		case "overlay":
			return 15;
		break;
	}
}

function scale($val){
	switch($val){
		case "other":
			return 15;
		break;
		case "pan":
			return 3;
		break;
		case "lan":
			return 5;
		break;
		case "han":
			return 2;
		break;
		case "san":
			return 10;
		break;
		case "can":
			return 20;
		break;
		case "man":
			return 100;
		break;
		case "wan";
			return 50;
		break;
		case "vpn":
			return 8;
		break;
		case "backbone":
			return 60;
		break;
		case "enterprise":
			return 80;
		break;
		case "internetwork":
			return 30;
		break;
		case"virtual":
			return 15;
		break;
			
	}
}

function num($val){
	switch($val){
		case 1: 
			return 5;	
		break;
		case 2:
			return 25;
		break;
		case 3:
			return 50;
		break;
		case 4:
			return 100;
		break;
	}
}

function way($val){
	switch($val){
		case "wired":
			return 2;
		break;
		case "wireless":
			return 1;
		break;
		case "hybrid":
			return 2;
		break;
	}
}

function score(){

	$way = $_GET["way"];
	$num = $_GET["num"];
	$scale = $_GET["scale"];
	$type = $_GET["type"];

$score = way($way) * 10 + num($num) * 10 + scale($scale) * 10 + type($type) * 10;

if ($score < 60){
	$score = 60;
}


return $score;

}

//********************************************
//**************** BEGIN CODE ****************
//********************************************

$score = score();

//output network installation data
echo "The estimated cost of your job is: $" . number_format($score * 0.6, 2) . "-$" . number_format($score, 2) . " plus parts. *This price is an estimate and is therefore non-binding, the actual price of your job may be more or less than the range listed. To get a better idea of what a job may cost, please <a href='mailto:info@bostonsystemdesign.com'>email us</a> your specifications";

//Output user controls
echo '<br /><br />';
echo '<form action="index.html">';
echo '<input type="button" onClick="window.history.go(-1);" value="Back" />&nbsp;';
echo '&nbsp;<button type="submit">Home</button>';
echo '</form>';
?>