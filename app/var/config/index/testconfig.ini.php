;<?php die(''); ?>
;for security reasons , don't remove or modify the first line

startModule=junittests
startAction="default:index"

; for junittests module
enableTests=on

dbProfils=dbprofilstests.ini.php

[coordplugins]
;nom = file_ini_name or 1

[responses]

[urlengine]
; name of url engine :  "simple" or "significant"
engine=simple


[modules]
jelix.access=2
rarangi.access=2
rarangi_web.access=2
junittests.access=2