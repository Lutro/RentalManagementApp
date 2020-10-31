@echo   cd ".\..\styles" for /r %%i in (*.less) do call lessc --clean-css "%%~i" "%%~dpni.min.css" cd .. 
