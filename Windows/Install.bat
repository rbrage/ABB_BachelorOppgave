mkdir c:\Apache24\
Xcopy /e /h Apache24 c:\Apache24\
mkdir c:\php\
Xcopy /e /h php c:\php\
mkdir C:\TriggerAnalysisWebScripts
Xcopy /e /h ..\Workspace\ABB C:\TriggerAnalysisWebScripts\
c:\Apache24\bin\httpd.exe -k install
c:\Apache24\bin\httpd.exe -k start