<!DOCTYPE html>
<html>
<head>
    <meta lang="en" charset="utf-8" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    <title>Prime Numbers</title>
    <link rel="stylesheet" type="text/css" href="styles.css" />
</head>
<body>
<?php
#read passed variables
if (count($_GET)>0){$index=$_GET["index"];}
else{$index=0;}

if ($index<0){$index=0;}

$num=$index+500;

#open SQL database
$con = @mysql_connect("mysql15.000webhost.com","a6617813_prime","%%%%%%");

//check connection
if (!$con){
    echo '<div class="error">Could not connect: ' . mysql_error().'</div>';
    die('<div class="error">The database is busy, try reloading the page in a moment.<br/>
    <button class="reLoad" id="reload" onclick="location.reload();">Reload</button></div>');
}

#select: "test" database
mysql_select_db("a6617813_primes", $con);

#set query
$sql="SELECT Primes FROM prime_numbers WHERE Number=$num";

//check for errors
if (!mysql_query($sql)){
    die('<div class="error">Error: ' . mysql_error().'</div>');
}
$result = mysql_query($sql, $con);
$prime = mysql_fetch_array($result);

$breakPoint=500;
$height=20;
if(strlen($prime[0])==0){
    $width=0;
    echo '<div class="error">You have reached the end of data.</div>';
}
else{
    $width=intval(100/strlen($prime[0]));
    $breakPoint=$height*$width;
}

#Read list from SQL
$start=$index+1;
$end=($index+$breakPoint);

$sql="SELECT Primes FROM prime_numbers WHERE Number BETWEEN $start AND $end";
#check for errors
if (!mysql_query($sql)){
    die('<div class="error">Error: ' . mysql_error().'</div>');
}
$result = mysql_query($sql, $con);
$counter=1;
while ($row = mysql_fetch_array($result)) {
    $array[$counter] = $row[0];
    $counter++;
}
mysql_close($con);

$firstLine="";
if(isset($array)){$firstLine="The following numbers from $array[1] to ".end($array)." are prime:";}

echo '<div class="header"><h2>'.$firstLine.'</h2></div>
      <div class="tableMain"><table id="main">';
#create table
for($i=1;$i<=$height;$i++){
    echo '<tr>';
    for($j=1;$j<=$width;$j++){
        echo '<td title="Index: '.(($width * ($i - 1)) + $j + $index).'">'.$array[($width*($i-1))+$j].'</td>';
    }
    echo '</tr>';
}
echo '</table></div>
';
if(strlen($prime[0])>0){
    $prev=(floor(100/strlen($array[1]))*$height);
    $prev=($index-$prev);
}
else{$prev=0;}

$next=($index+$breakPoint);

echo '<div class="footer">    
    <form action="primessql.php" method="GET" class="lCtrl">
        <input id="prevIndex" type="hidden" name="index" value="'.$prev.'" />
        <input class="controls" value="&lt;&lt; Previous" id="pCtrls" formtarget="_self" type="submit"/>
    </form>
    <form class="finder">Start with the 
        <input class="findNumber" type="number" name="index" max="1000000000" value="'.($index+1).'"/>
        <sup>th</sup> prime number.
        <input class="go" value="Go" type="submit"/>
    </form>
    <form action="primessql.php" method="GET" class="rCtrl">
        <input id="nextIndex" type="hidden" name="index" value="'.$next.'" />
        <input class="controls" value="Next &gt;&gt;" id="nCtrls" formtarget="_self" type="submit" />
    </form>
</div>
';
?>
<script>
//Set event handlers to disable/enable "prev" button
window.onload=function(){
    if(document.getElementById("prevIndex").value=="-2000"){
        document.getElementById("pCtrls").disabled=true;
    }
}
</script>
</body>
</html>
