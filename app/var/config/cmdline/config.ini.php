;<?php die(''); ?>
;for security reasons , don't remove or modify the first line

startModule = "rarangi"
startAction = "default:index"



; for junittests module
enableTests = on

[error_handling]
logFile = error-cli.log
default      = ECHO EXIT LOGFILE
error        = ECHO TRACE EXIT LOGFILE
warning      = ECHO TRACE LOGFILE
notice       = ECHO LOGFILE
strict       = ECHO
exception    = ECHO TRACE LOGFILE

