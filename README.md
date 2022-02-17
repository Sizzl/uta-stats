# uta-stats by utassault.net

UTStats 4.2.29uta

Copyright (C) 2004/2005 azazel, )°DoE°(-AnthraX and toa
Additional copyrights held (C) 2005/2006/2007 Cratos, Timo, brajan

This program is free software; you can redistribute and/or modify
it under the terms of the Open Unreal Mod License.
See [LICENSE](LICENSE) for more information.

Contents
========

1. Additional information for 4.2.29uta Release

2. Original UTStats information


# 1. Additional information for 4.2.29uta Release


Copy to your UTServer\System folder the following files:

	UTStats50.u
	SmartAS-Message.u
	SmartAS_101.u

Remove any previous entries for UTStats and UTSAccuBeta from [Engine.GameEngine]

Add to [Engine.GameEngine] the following lines:

	ServerActors=UTStats50.UTStatsSA

For servers running assault, additionally add to [Engine.GameEngine] the following lines:

	ServerActors=SmartAS-Message.SmartASMessage
	ServerActors=SmartAS_101.SmartAS_SA
	ServerPackages=SmartAS-Message
	ServerPackages=UTStats50

Under the [Engine.GameInfo] check for:
bLocalLog=True or bLocalLog=False

This line HAS to be:
bLocalLog=False

# 2. Original UTStats information

What Is It
----------
UTStats is a Serverside Actor and websystem for the original Unreal Tournament 
that generates statistics from a custom NGStats log file.


What Does It Record
-------------------
Lots of stuff


What Will I Need
----------------
Access to the logs
PHP & MySQL enabled website
PHP needs to allow ftpconnect etc if you wish to use auto-ftp of logs


What Game Types Does it Support
-------------------------------
All the official game types.


Do You Have A Preview Site?
-------------------------------
Yes. http://utstats.unrealadmin.org/


Why Beta?/Any New Stuff to Come
-------------------------------
Possible new stuff, but until we're happy its 100% stable it stays as beta :)


I Want to Help What Can I Do?
-----------------------------
When the Beta is released please check the pages.
They might be able to be done more efficiently or other things could be added.
If you work something out, let us know, share, don't be one of the selfish people 
in life who keeps it to themselves.
Remember, the maps you likely play, the mods you use, someone shared them. 
If you can, now is your opportunity to share back.
