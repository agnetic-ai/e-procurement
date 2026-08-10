param(
    [string]$DumpPath = 'eprocurement_db (17).sql',
    [string]$Grep = ''
)

$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot
$runtimePath = Join-Path $projectRoot '.e2e-runtime'
$dataPath = Join-Path $runtimePath 'mysql-data'
$sessionPath = Join-Path $runtimePath 'sessions'
$resolvedDump = Join-Path $projectRoot $DumpPath
$mysqlServer = (Get-Command mysqld.exe -ErrorAction Stop).Source
$mysqlBin = Split-Path -Parent $mysqlServer
$mysqlBase = Split-Path -Parent $mysqlBin
$mysqlClient = Join-Path $mysqlBin 'mysql.exe'
$mysqlAdmin = Join-Path $mysqlBin 'mysqladmin.exe'
$phpExecutable = (Get-Command php.exe -ErrorAction Stop).Source
$routerPath = Join-Path $projectRoot 'tests\e2e\router.php'
$mysqlProcess = $null
$phpProcess = $null
$testExitCode = 1

New-Item -ItemType Directory -Path $runtimePath -Force | Out-Null
New-Item -ItemType Directory -Path $sessionPath -Force | Out-Null

if (-not (Test-Path -LiteralPath (Join-Path $dataPath 'mysql'))) {
    New-Item -ItemType Directory -Path $dataPath -Force | Out-Null
    & $mysqlServer --no-defaults --initialize-insecure "--basedir=$mysqlBase" "--datadir=$dataPath"
    if ($LASTEXITCODE -ne 0) {
        throw 'Failed to initialize the isolated MySQL data directory.'
    }
}

try {
    $mysqlProcess = Start-Process -FilePath $mysqlServer `
        -ArgumentList @(
            '--no-defaults',
            "--basedir=$mysqlBase",
            "--datadir=$dataPath",
            '--port=3307',
            '--bind-address=127.0.0.1',
            '--mysqlx=0',
            '--console'
        ) `
        -WorkingDirectory $projectRoot `
        -RedirectStandardOutput (Join-Path $runtimePath 'mysql.stdout.log') `
        -RedirectStandardError (Join-Path $runtimePath 'mysql.stderr.log') `
        -WindowStyle Hidden `
        -PassThru

    $databaseReady = $false
    for ($attempt = 0; $attempt -lt 20; $attempt++) {
        & $mysqlAdmin --protocol=TCP -h 127.0.0.1 -P 3307 -u root ping 2>$null
        if ($LASTEXITCODE -eq 0) {
            $databaseReady = $true
            break
        }
        Start-Sleep -Milliseconds 500
    }
    if (-not $databaseReady) {
        throw 'Isolated MySQL did not become ready.'
    }

    $sqlDump = (Resolve-Path -LiteralPath $resolvedDump).Path.Replace('\', '/')
    & $mysqlClient --protocol=TCP -h 127.0.0.1 -P 3307 -u root -e "DROP DATABASE IF EXISTS eprocurement_e2e; CREATE DATABASE eprocurement_e2e CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; USE eprocurement_e2e; SOURCE $sqlDump;"
    if ($LASTEXITCODE -ne 0) {
        throw 'Failed to import the E2E database dump.'
    }

    $migrationFiles = Get-ChildItem (Join-Path $projectRoot 'database\migrations') -Filter '*.sql' -File | Sort-Object Name
    foreach ($migrationFile in $migrationFiles) {
        $migrationPath = $migrationFile.FullName.Replace('\', '/')
        & $mysqlClient --protocol=TCP -h 127.0.0.1 -P 3307 -u root -D eprocurement_e2e -e "SOURCE $migrationPath;"
        if ($LASTEXITCODE -ne 0) {
            throw "Failed to apply migration $($migrationFile.Name)."
        }
    }

    $env:EPROC_BASE_URL = 'http://127.0.0.1:8765/'
    $env:EPROC_DB_HOST = '127.0.0.1'
    $env:EPROC_DB_PORT = '3307'
    $env:EPROC_DB_USER = 'root'
    $env:EPROC_DB_PASS = ''
    $env:EPROC_DB_NAME = 'eprocurement_e2e'
    $env:E2E_BASE_URL = $env:EPROC_BASE_URL
    $env:E2E_USERNAME = 'admin'
    $env:E2E_PASSWORD = 'admin123'
    $env:E2E_ALLOW_MUTATION = 'true'

    & $phpExecutable (Join-Path $projectRoot 'tests\integration\po-workflow.php')
    if ($LASTEXITCODE -ne 0) {
        throw 'PO workflow integration test failed.'
    }

    $phpProcess = Start-Process -FilePath $phpExecutable `
        -ArgumentList @('-d', "session.save_path=$sessionPath", '-S', '127.0.0.1:8765', '-t', $projectRoot, $routerPath) `
        -WorkingDirectory $projectRoot `
        -RedirectStandardOutput (Join-Path $runtimePath 'php.stdout.log') `
        -RedirectStandardError (Join-Path $runtimePath 'php.stderr.log') `
        -WindowStyle Hidden `
        -PassThru

    $webReady = $false
    for ($attempt = 0; $attempt -lt 20; $attempt++) {
        $httpStatus = curl.exe --noproxy '*' -sS -o NUL -w '%{http_code}' --connect-timeout 1 --max-time 2 http://127.0.0.1:8765/auth/login
        if ($LASTEXITCODE -eq 0 -and $httpStatus -eq '200') {
            $webReady = $true
            break
        }
        Start-Sleep -Milliseconds 500
    }
    if (-not $webReady) {
        throw 'PHP E2E server did not become ready.'
    }

    Push-Location $projectRoot
    try {
        $npmArguments = @('run', 'test:e2e')
        if ($Grep) {
            $npmArguments += @('--', '--grep', $Grep)
        }
        & npm.cmd @npmArguments
        $testExitCode = $LASTEXITCODE
    }
    finally {
        Pop-Location
    }
}
finally {
    if ($phpProcess -and -not $phpProcess.HasExited) {
        Stop-Process -Id $phpProcess.Id -Force
    }

    & $mysqlAdmin --protocol=TCP -h 127.0.0.1 -P 3307 -u root shutdown 2>$null
}

exit $testExitCode
