<?php
  if (isset($_GET['q']))
    $q = $_GET['q'];
?>
<html>
<head>
<title>whois info (brajan)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">
<script>
function m(el) {
  if (el.defaultValue==el.value) el.value = ""
}
</script>
</head>

<body bgcolor="#FFFFFF">
<?php 
$ntarget = "";
function message($msg){
echo "<font face=\"verdana,arial\" size=2>$msg</font>";
flush();
}

function arin($q){
$msg = "";
$server = "whois.arin.net";
message("<p><b>Results of IP Whois:</b><blockquote>");
if (!$q = gethostbyname($q))
  $msg .= "ERROR: IP not specified!!";
else{
  message("Connecting with $server...<br><br>");
  if (! $sock = fsockopen($server, 43, $num, $error, 20)){
    unset($sock);
    $msg .= "Connection with $server (port 43) canceled becouse of timeout";
    }
  else{
    fputs($sock, "$q\n");
    while (!feof($sock)){
      if (isset($buffer))
        $buffer .= fgets($sock, 10240); 
      else
        $buffer = fgets($sock, 10240); 
    }
    fclose($sock);
    }
   if (preg_match('/RIPE\.NET/i', $buffer))
     $nextServer = "whois.ripe.net";
   else if (preg_match('/whois\.apnic\.net/i', $buffer))
     $nextServer = "whois.apnic.net";
   else if (preg_match('/nic\.ad\.jp/i', $buffer)){
     $nextServer = "whois.nic.ad.jp";
     $extra = "/e";
     }
   else if (preg_match('/whois\.registro\.br/i', $buffer))
     $nextServer = "whois.registro.br";
   if(isset($nextServer)){
     $buffer = "";
     message("Searching for the proper WHOIS server: $nextServer...<br><br>");
     if(! $sock = fsockopen($nextServer, 43, $num, $error, 10)){
       unset($sock);
       $msg .= "Connection with $nextServer (port 43) canceled becouse of timeout";
       }
     else{
       fputs($sock, "$q$extra\n");
       while (!feof($sock))
         $buffer .= fgets($sock, 10240);
       fclose($sock);
       }
     }
  $buffer = str_replace(" ", "&nbsp;", $buffer);
  $msg .= nl2br($buffer);
  }
$msg .= "</blockquote></p>";
message($msg);
}

if((!$q) || (!preg_match("/^[\w\d\.\-]+\.[\w\d]{1,4}$/i",$q)) ){
  message("ERROR: This is not valid IP!");
  exit;
  }
$pobierz = arin($q);
?>
</body>
