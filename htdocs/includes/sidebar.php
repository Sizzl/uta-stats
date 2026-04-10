<?php 
echo'
<td Valign="top" width="120" class="sidebar">
  <a href="http://www.unrealtournament.com/"><img src="images/utlogo.gif" title="UT Logo" alt="UT Logo" border="0"></a>
  <br />'.(isset($defaultsidebar) ? $defaultsidebar : "").'
  <br>
  <img src="images/characters/'.(isset($charimg) ? $charimg : "").'" title="UT Character" alt="UT Character" border="0">
  <br>
</td>
<td>
<img src="images/blankbar.gif" alt="Spacing" border="0" width="17" height="1"></td>
<td align="center" valign="top">
';
?>
