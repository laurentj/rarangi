;<?php die(''); ?>
;for security reasons , don't remove or modify the first line

startModule = "jphpdoc"
startAction = "default:index"



; for junittests module
enableTests = on


[error_handling]
logFile = error-cli.log

; mots clés que vous pouvez utiliser : ECHO, ECHOQUIET, EXIT, LOGFILE, SYSLOG, MAIL, TRACE
default      = ECHO EXIT LOGFILE
error        = ECHO TRACE EXIT LOGFILE
warning      = ECHO TRACE LOGFILE
notice       = ECHO LOGFILE
strict       = ECHO
; pour les exceptions, il y a implicitement un EXIT
exception    = ECHO TRACE

