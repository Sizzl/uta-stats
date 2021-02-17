<?php
// echo $defaultsidebar;
echo '
            <TABLE>
                <TR>
                <TD align="center">
                  <ul id="css3menu">
                    <li class="topfirst"><a href="./?&amp;'.(isset($a_test) ? "archive=".$a_test : "").'" title="Summary">Summary</a></li>
                    <li class="topitem"><a href="./?p=utapugrecent&amp;'.(isset($a_test) ? "archive=".$a_test : "").'" title="PUG Matches"><span>PUG Matches</span></a></li>
                    <!-- <li class="topitem"><a href="./?p=utapugsummary&amp;'.(isset($a_test) ? "archive=".$a_test : "").'" title="PUG Totals"><span>PUG Totals</span></a></li> -->
                    <li class="topitem"><a href="./?p=utarecordzone&amp;'.(isset($a_test) ? "archive=".$a_test : "").'" title="Assault Records"><span>Assault Records</span></a></li>
                    <li class="topitem"><a href="./?p=rank&amp;year='.date("Y").'&amp;'.(isset($a_test) ? "archive=".$a_test : "").'" title="Player Rankings"><span>Rankings ('.date("Y").')</span></a></li>
                    <li class="topitem"><a href="./?p=rank&amp;'.(isset($a_test) ? "archive=".$a_test : "").'" title="Player Rankings"><span>Rankings (All)</span></a></li>
                    <li class="topitem"><a href="./?p=servers&amp;'.(isset($a_test) ? "archive=".$a_test : "").'" title="Servers"><span>Servers</span></a></li>
                    <li class="topitem"><a href="./?p=maps&amp;'.(isset($a_test) ? "archive=".$a_test : "").'" title="Map Statistics"><span>Map Stats</span></a></li>
                    <li class="topitem"><a href="./?p=totals&amp;'.(isset($a_test) ? "archive=".$a_test : "").'" title="Totals"><span>Totals</span></a></li>
                    <!-- <li class="topitem">
                        <a href="./?p='.$_GET['p'].'&amp;archive='.intval(date("Y")-1).'" title="Archives">Archives</a>
                      <ul>
                        <li class="subfirst"><a href="./?p='.$_GET['p'].'&amp;pid='.$_GET['pid'].'&amp;gid='.$_GET['gid'].'&amp;archive=0" title="Current">Current season</a></li>';
	for ($i=2012; $i < intval(date("Y")); $i++) {
		echo '
                        <li class="subfirst"><a href="./?p='.$_GET['p'].'&amp;pid='.$_GET['pid'].'&amp;gid='.$_GET['gid'].'&amp;archive='.$i.'" title="'.$i.'">'.$i.' season</a></li>';
	}
echo '
                      </ul>

                    </li> -->

                    <li class="toplast">
                        <a href="http://forums.utassault.net/forumdisplay.php?f=350" target="_blank" title="Forums">Forums</a>
                      <ul>
                        <li class="subfirst"><a href="http://forums.utassault.net/" target="_blank" title="utassault.net">utassault.net</a></li>
                        <li class="subfirst"><a href="http://forums.utassault.net/forumdisplay.php?f=350" target="_blank" title="utassault.net: Pug:AS">utassault.net: Pug:AS</a></li>
                      </ul>

                    </li>
                  </ul>
                </TD>
                 </TR></TABLE>';
?>
