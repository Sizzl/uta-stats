#-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-
# utassault.net - Stats - Make static files
#_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_

localDirectory='/home/utassault/web/utstats/'
cd $localDirectory

starttime=`date +%H:%M:%S`
php "$localDirectory/makestatic/totals_makestatic.php" > "$localDirectory/pages/totals.php"
endtime=`date +%H:%M:%S \(%d/%m/%y\)`
echo "Executed totals.php from $starttime to $endtime" >> "$localDirectory/logs/makestaticlog/makestatic.log"

starttime=`date +%H:%M:%S`
php "$localDirectory/makestatic/home_makestatic.php" > "$localDirectory/pages/home.php"
endtime=`date +%H:%M:%S \(%d/%m/%y\)`
echo "Executed home.php from $starttime to $endtime" >> "$localDirectory/logs/makestaticlog/makestatic.log"

