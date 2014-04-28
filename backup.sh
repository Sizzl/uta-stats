#-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-
# utassault.net - Treacle - Automatic Backup System
#_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_

tclDate=`date +%G-%m-%d`
tclArchive='/home/utassault/backups/cupstats'
remoteHost='utassault@bacon.fraghub.net'
remoteDirectory='/home/hosted/utassault/league/backups/'
extension='tar.bz2'

clear
echo "Creating backup archive for $tclDate ..."
rm -f $tclArchive-$tclDate.$extension
tar -cpPjf $tclArchive-$tclDate.$extension . --exclude-from='/home/utassault/.fileexclusions' --exclude="./web/new/templates_c" --exclude="/home/utassault/web/utstats/data"
echo " "
echo "Archive created:"
ls -cshlt $tclArchive-$tclDate.$extension
echo " "
if [ "$1" = "sync" ]
then
 echo "Mirroring file..."
 echo "  --- You must enter your user password here --- "
 rsync -ar $tclArchive-$tclDate.$extension $remoteHost:$remoteDirectory
fi
echo "Process complete."
exit 0
