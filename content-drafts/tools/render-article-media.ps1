param(
	[int]$StartId = 1,
	[int]$Limit = 50
)

$ErrorActionPreference = 'Stop'

$RepoRoot = Resolve-Path (Join-Path $PSScriptRoot '..\..')
$DraftRoot = Join-Path $RepoRoot 'content-drafts'
$OutputRoot = Join-Path $DraftRoot 'generated\media-v2'
$PlanFile = Join-Path $OutputRoot 'media-plan.json'
$RendererFile = Join-Path $OutputRoot 'render-article-media.html'
$PngRoot = Join-Path $OutputRoot 'png-preview'
$ProfileDir = Join-Path $env:TEMP 'fenix-article-media-render'

& php (Join-Path $PSScriptRoot 'build-article-media-plan.php')

if ($LASTEXITCODE -ne 0) {
	throw 'Unable to build the article media plan.'
}

$ChromeCandidates = @(
	'C:\Program Files\Google\Chrome\Application\chrome.exe',
	'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe'
)
$Chrome = $ChromeCandidates | Where-Object { Test-Path -LiteralPath $_ } | Select-Object -First 1

if (-not $Chrome) {
	throw 'Google Chrome was not found.'
}

New-Item -ItemType Directory -Force -Path $PngRoot | Out-Null
New-Item -ItemType Directory -Force -Path $ProfileDir | Out-Null

$Plan = Get-Content -LiteralPath $PlanFile -Raw -Encoding UTF8 | ConvertFrom-Json
$Items = @($Plan.items |
	Where-Object { $_.id -ge $StartId } |
	Select-Object -First $Limit)
$RendererUri = [System.Uri]::new($RendererFile).AbsoluteUri
$Types = @('cover', 'context', 'summary', 'checklist')

foreach ($Item in $Items) {
	foreach ($Type in $Types) {
		$Height = if ($Type -eq 'cover') { 630 } else { 675 }
		$OutputFile = Join-Path $PngRoot ('{0}-{1}.png' -f $Item.slug, $Type)
		$Url = '{0}?id={1}&type={2}' -f $RendererUri, $Item.id, $Type
		$Args = @(
			'--headless=new',
			'--disable-gpu',
			'--hide-scrollbars',
			'--no-first-run',
			'--allow-file-access-from-files',
			'--force-device-scale-factor=1',
			'--run-all-compositor-stages-before-draw',
			'--virtual-time-budget=2500',
			"--user-data-dir=$ProfileDir",
			"--window-size=1200,$Height",
			"--screenshot=$OutputFile",
			$Url
		)

		& $Chrome @Args | Out-Null

		if ($LASTEXITCODE -ne 0 -or -not (Test-Path -LiteralPath $OutputFile)) {
			throw "Failed to render article $($Item.id), type $Type."
		}

		Write-Host ('Rendered {0:D2} {1}' -f $Item.id, $Type)
	}
}

& php (Join-Path $PSScriptRoot 'convert-article-media.php') --start=$StartId --limit=$Limit

if ($LASTEXITCODE -ne 0) {
	throw 'Unable to convert the rendered PNG files to WebP.'
}

Write-Host "Completed article media render for $($Items.Count) articles."
