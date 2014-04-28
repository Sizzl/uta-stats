=========================
UTStats
Copyright (C) 2004/2005 azazel, AnthraX and toa

This program is free software; you can redistribute and/or modify
it under the terms of the Open Unreal Mod License.
See license.txt for more information.
=========================
UTStats Beta 4.2
Auto-Disable Logging
For Specified Gametypes

These edits by MSuLL


Code by the UTStats team

Released under the terms of
the Open Unreal Mod License
=========================



What Is This?
================
This is a modified version of UTStats Beta 4.2
It allows you to halt logging during certain gametypes.





What Can I Use It For?
=========================
This was specifically created for use with EUT 1G and above.
EUT includes it's own UTStats logging... but what if you run other
gametypes than what EUT supports?  Well if so then you're stuck
running dual-logging, which is horribly hard on your server and clients.
This modification can be configured to disable UTStats when an EUT game
has been loaded, making only one modification log accuracy, and not two.

>IMPORTANT<
**************

If you are planning on using this with EUT 1G or above, please do
the following:

Get EUT setup and working, and be sure to set the option:

bStatLink=True

...for all the gametypes you are running.





Installation:
================
First install UTStats Beta 4.2 if you have not already
done so.  Make sure everything works and runs... then
proceed to the next steps of this installation.


Shut down your server.

Upload UTStatsBeta4_2_Rev100.u to /SYSTEM

Open your server's UnrealTournament.ini/Server.ini

Find the line that says:
ServerActors=UTStatsBeta4_2.UTStatsSA

Change it to:
ServerActors=UTStatsBeta4_2_Rev100.UTStatsSA

Now at the VERY bottom of your INI file add the following:

[UTStatsBeta4_2_Rev100.UTStatsSA]
DisabledClasses[0]=
DisabledClasses[1]=
DisabledClasses[2]=
DisabledClasses[3]=
DisabledClasses[4]=
DisabledClasses[5]=
DisabledClasses[6]=
DisabledClasses[7]=


Edit the fields to your desire (explained below).

Save and close all files, and restart the server!





Field Options:
=================
The field options are the places for you to place a
gametype class that you DON'T want to have UTStats log.



If you use EUT, your configuration needs to be like so:

(... but be sure to replace _1G with the version you have installed)

[UTStatsBeta4_2_Rev100.UTStatsSA]
DisabledClasses[0]=EUT_1G.SmartCTFGame
DisabledClasses[1]=EUT_1G.EUTTeamGamePlus
DisabledClasses[2]=EUT_1G.EUTDeathMatchPlus
DisabledClasses[3]=
DisabledClasses[4]=
DisabledClasses[5]=
DisabledClasses[6]=
DisabledClasses[7]=