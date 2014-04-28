<?
echo'
<div class="text" align="left">
<p><span class="txttitle">Credits</span></p>

<blockquote><p>
�1. <a href="#developers">Developers</a><br>
�2. <a href="#changelog">Change Log</a><br>
�3. <a href="#todolist">To Do List</a><br>
�4. <a href="#thanks">Thanks</a><br>
�5. <a href="#copyright">Copyright Notices</a><br>
</p></blockquote>

<p><a name="developers"></a><b>Developers</b></p>
<blockquote><p>UTStats was developed by azazel, )�DoE�(-AnthraX, PJMODOS and toa.<br>
All original pages are W3C <a href="http://validator.w3.org/check?uri=referer" target="_blank">HTML 4.01</a> and
<a href="http://jigsaw.w3.org/css-validator/" target="_blank">CSS</a> compliant</p>

</blockquote>

<p><a name="changelog"></a><b>Change Log</b></p>
<blockquote><p>
<dl>
	<dt>beta 4.0</dt>
	<dd>Added:<br>
	Many pages overhauled<br>
	Database overhauled<br>
	Option to import bots or not (off by default)<br>
	Command Line Interface now outputs to text not html<br>
	Ranking stuff on match and player pages include gold/silver/bronze cups for each gametype<br>
	Rankings tweaked so new players get even less points<br>
	Maps page now sortable<br>
	Flag Assists now show, get the new <a href="http://www.unrealadmin.org/forums/showthread.php?t=9561" target="_blank">Smart CTF</a><br>
	Report generator outputting to Clanbase and bbcode format<br>
	Support added for custom weapons and gametypes<br>
	Admin page including server/player merging, deletion of players/matches, renaming of "game types" etc<br>
	Option to compress logs when backing them up (requires bzip/gzip support in php)<br>
	More debugging stuff added<br>
	Accuracy package optimised and recoded for better performance (it will not lag the server in anyway now)<br>
	More detailed weapon statistics added<br>
	Totals page expanded with information like on the old NGStats<br>
	JailBreak should now display its statistics properly<br>
	Purge logs option added<br>
	Graphs now display better regardless of data used<br>
	CTF4 Compatibility<br>
	Date and Game Type filtering on Recent Matches page<br>
	Ability to Ban players<br>
	Ability to ignore matches < X minues in length<br>
	IP Search within Administration<br>
	Ability to ignore matches less than X minutes in length<br>
	Option to import UTDC logs (admin viewable only)<br><br></dd>

	<dd>Bug Fixes:<br>
	Ranking overhauled to better reflect average game play of players<br>
	Cleaned up the importer<br>
	Teamscores now shown correctly regardless of player switching activity<br>
	Kills matrix is now created on combined player records<br>
	Kills against bots no longer counted if bots are not imported<br>
	Domination logs only log when players are in<br>
	Teamkills identified as kills in non-team games (gg Epic :/)<br>
	Eff etc fixed because of above Teamkills bug<br>
	Last line not logging of buffer fixed<br><br></dd>

	<dt>beta 3.2</dt>
	<dd>Added:<br>
	Debugging Option<br>
	Better FTP Capabilities<br>
	Filters carried over on next last etc on player page<br><br></dd>

	<dd>Bug Fixes:<br>
	Imports failing on some versions of php 4.3.x<br>
	Totals page fixed<br>
	Totals info at the top of match pages fixed<br><br></dd>

	<dt>beta 3.1</dt>
	<dd>Added:<br>
	Kills Matchup Matrix
	Country Flags for Players<br>
	Hover Hints over key parts of the page (eg. K F D S)<br>
	Some Graphs<br><br></dd>

	<dd>Bug Fixes:<br>
	Importer can now import unlimited logs<br>
	Kills on match pages not listed<br>
	Games where nothing happens no longer imported<br>
	Players who have 0 kills &amp; 0 deaths no longer get imported<br>
	FTP script re-written<br>
	Pickups removed from insta pages<br>
	Translocator entries removed from logs (throws not kills)<br>
	Multis & Sprees report correct player now<br>
	Kills correctly worked out on non-Team Games<br>

	Frags correctly worked out on all games<br><br></dd>

	<dt>beta 3.0</dt>
	<dd>Added:<br>
	SmartCTF events<br>
	UTGL Compatibility<br><br>
	Updated:<br>
	UTStats actor re-written from scratch, it now uses NGLog files<br>
	Database re-written from scratch<br>
	PHP code re-written from scratch<br><br></dd>

	<dd>Bug Fixes:<br>
	Too many to think about<br><br></dd>

	<dt>beta 2.0</dt>
	<dd>Code rewritten from ground up then lost :(<br><br></dd>


	<dt>beta 1.2</dt>
	<dd>Added:<br>
	Accuracy Code (best in insta but works on all weapons)<br>
	UT2004 spree scheme<br>
	Who killed the Flag Carrier<br>
	<br>
	Updated:<br>
	Complete overhaul of pages/theme to mimic closley UT2004 Stats by Epic<br>
	Cap times added to Clanbase Report<br>
	Stats database, now at least 10-20x smaller<br>
	<br>
	Bug Fixes:<br>
	TeamKills no longer appear in DM<br>
	TeamKills no longer mess up overall stats<br>
	Bot kills etc no longer included in overall stats<br>
	Sprees are unique<br><br></dd>

	<dt>beta 1.1</dt>
	<dd>Added:<br>
	Clanbse Reports for CTF Match\'s<br>
	30 Recent Match\'s to Player View<br><br></dd>

	<dt>beta 1</dt>
	<dd>Stats output for:<br>
	Player Joins/Leaves<br>
	Match Start/End<br>
	Frags and Item Pickups<br>
	Sprees (Doubles/Multis and Domination/Monster etc)<br>
	Events</dd>

</dl></blockquote>

<p><a name="todolist"></a><b>To Do List</b></p>
<blockquote><dl><dd>Centralise stats</dd></dl>
</blockquote>

<p><a name="thanks"></a><b>Thanks</b></p>
<blockquote>
<dl>
<dd>Epic for making a game that we still play<br>
	kostaki for the database pointers, scoring system and the <a href="http://www.inzane.de/" target="_blank">inzane</a> public servers :)<br>
	Limited for the late night sesions, the linux script and the original zero_out function<br>
	L0cky and Flash for the original FTP Script<br>
	Loph for the 6 different reports o/<br>
	Rush for the improved linux script, testing, suggestions and bug finding<br>
	TNSe for being TNSe<br>
	Truff for testing, suggestions and constant bug finding<br>
	Truff Community for testing, suggestions and input<br>
	UnrealAdmin.org testers and suggesters<br></dd></dl></blockquote>

<p><a name="copyright"></a><b>Copyright Notices</b></p>
<blockquote><dl>
<dd>UTStats<br>
	Copyright (C) 2004/2005 <a href="http://utstats.unrealadmin.org/" target="_blank">UTStats</a><br>
	<br>
	This program is free software; you can redistribute and/or modify<br>
	it under the terms of the Open Unreal Mod License.<br>
	<br>
	If you do make any changes, fixes or updates posting them on the<br>
	forum would be appreciated.<br>
	<br>
	UT Query PHP script v1.01 by Almar Joling, 2003<br>
	<a href="http://www.persistentrealities.com/" target="_blank">www.persistentrealities.com</a><br>
	<br>
	pemftp Class by Alexey Dotsenko &lt;alex at paneuromedia dot com&gt;<br>
	<a href="http://www.phpclasses.org/browse/package/1743.html" target="_blank">http://www.phpclasses.org/browse/package/1743.html</a><br>
	<br>
	IP-to-Country Database provided by <a href="http://www.webhosting.info" target="_blank">WebHosting.Info</a><br>
	Available from <a href="http://ip-to-country.webhosting.info" target="_blank">http://ip-to-country.webhosting.info</a><br>
	<br>
	overLIB by Erik Bosrup<br>
	<a href="http://www.bosrup.com/web/overlib/" target="_blank">http://www.bosrup.com/web/overlib/</a>
	</dd></dl></blockquote>
<br>
<table width="70%">
  <tbody><tr>
    <td align="left"><a href="#Top">Back to Top</a></td>
    <td align="right">&nbsp;</td>
  </tr>
</tbody></table>
</div>';
?>