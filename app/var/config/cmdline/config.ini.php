;<?php die(''); ?>
;for security reasons , don't remove or modify the first line

startModule=rarangi
startAction="default:index"



; for junittests module
enableTests=on

[error_handling]



[fileLogger]
default=error-cli.log
error=error-cli.log
warning=error-cli.log
notice=error-cli.log

[logger]
default=file
error=file
warning=file
notice=file
sql=

[modules]
rarangi_static.access=2
