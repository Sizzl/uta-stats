==========================================================
UTSTATS-Trigger v1.0
by +++crowbar+++
crowbar666@gmx.net
==========================================================

1. Introduction
---------------
If you dont have access to cron jobs on your webserver and
your sick of manually updating your UTSTATS through the 
IMPORT menu option?

Well, here'S a little muatator, that may help ;-)

Your UTSTATS-installation *MUST* be configured for ftp-
import. 

2. Install
----------
Copy all files form the archives into your system folder 
of your UT-server. Modify the ini-file:

WebServer=www.webserver.com
- Please NOT http://www.webserver.com 
         NOT http://www.webserver.com/
         only       www.webserver.com 

FilePath=/utstats
- FilePath is the path on your webserver to your UTSTATS
  installation. Examples:
  www.webserver.com/foo/utstats/ --> /foo/utstats
  www.webserver.com/utstats      --> /utstats
  www.webserver.com/             --> /


Port=80
- The webserver port of your UTSTATS installation. Port
  80 ist the default port, that will work in 99%.


AdminKey=password
- The admin key of your UTSTATS installation

2.1 UT-Server configuration
---------------------------
Add the following line into the [Engine.GameEngine]
section of your server.ini

	ServerActors=UTSTATS_Trigger.UTSTATS_Trigger

Restart server.
Done.


3. Licence
----------
No copyright, no licence, no GPL-bullshit. This is 
absolutly FREE software. Use at own will and risk.


Have fun!
+++crowbar+++