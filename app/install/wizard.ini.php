
; path related to the ini file. By default, the ini file is expected to be into the myapp/install/ directory.
pagesPath = "../../lib/installwizard/pages/"
customPath = "wizard/"
start = welcome
tempPath = "../../temp/rarangi/"
supportedLang = en

appname = Rarangi

[welcome.step]
next=checkjelix

[checkjelix.step]
next=dbprofile

[dbprofile.step]
next=installapp
availabledDrivers="mysql"
messageHeader="message.header.dbProfile"

[installapp.step]
next=end
level=notice

[end.step]
noprevious = on
messageFooter = "message.footer.end"