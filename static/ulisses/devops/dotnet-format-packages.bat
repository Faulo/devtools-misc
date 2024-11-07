@echo off
setlocal enabledelayedexpansion
for /r "Packages" %%f in (*.asmdef) do (
    set asmdef_file=%%~nf
    set csproj_file=!asmdef_file!.csproj
	echo ^> dotnet format "!csproj_file!"
	call dotnet format "!csproj_file!" --no-restore --verbosity diagnostic
	echo(
)