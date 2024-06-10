@FOR %%I in ("*.sln") DO @(
    @echo %%I
	call dotnet format "%%I" --no-restore --verbosity detailed --report . --exclude Library Assets
)
@pause