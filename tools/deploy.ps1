<#
.SYNOPSIS
    Livre movies sur https://louiecinephile.fr/movies (front Angular) et /moviesServer (back PHP).

.DESCRIPTION
    Arborescence déposée :
        /movies/        <- contenu de dist/browser (Angular 20, base href /movies/)
        /moviesServer/  <- back PHP : api/, php/, pages d'admin, images/

    La racine FTP est la racine web. Transfert en FTPS explicite avec
    vérification complète du certificat, via tools/ftps.ps1 (copie de celui de
    bank). Identifiants dans .secrets/deploy.env, jamais versionné.

    Jamais envoyés :
      - php/config.sample.php : gabarit ;
      - sql/ : scripts de migration, ils n'ont rien à faire sous la racine web ;
      - images/people/ et images/posters/ : ~270 Mo de photos qui ne vivent que
        sur le serveur (ni dans le dépôt, ni dans la livraison).

    php/config.php (identifiants MySQL et clé TMDB, hors dépôt car celui-ci est
    public) n'est envoyé qu'avec -IncludeConfig, et toujours AVANT
    php/DB.class.php qui fait un require dessus.

    Les autres images du back (catégories, pays) ne sont envoyées qu'avec
    -IncludeImages : elles changent rarement et sont déjà en place.

.PARAMETER DryRun
    Affiche le plan de transfert sans rien envoyer.

.PARAMETER TestConnection
    Vérifie la connexion et liste les dossiers distants, puis sort.

.PARAMETER SkipBuild
    Réutilise dist/browser tel quel au lieu de relancer ng build.

.PARAMETER Only
    'front' ou 'back' pour ne livrer qu'une partie.

.PARAMETER IncludeConfig
    Ajoute back/php/config.php à la livraison (première livraison depuis
    l'externalisation des identifiants, ou identifiants modifiés).

.PARAMETER IncludeImages
    Ajoute back/images/ (hors people/ et posters/) à la livraison.

.PARAMETER Cleanup
    Supprime de /movies les fichiers absents du build (bundles hashés obsolètes).
    Volontairement non actif par défaut.

.EXAMPLE
    .\tools\deploy.ps1 -TestConnection
    .\tools\deploy.ps1 -Only back -IncludeConfig -DryRun
    .\tools\deploy.ps1 -Only back -IncludeConfig
    .\tools\deploy.ps1
#>

[CmdletBinding()]
param(
	[switch]$DryRun,
	[switch]$TestConnection,
	[switch]$SkipBuild,
	[ValidateSet('front', 'back')][string]$Only,
	[switch]$IncludeConfig,
	[switch]$IncludeImages,
	[switch]$Cleanup
)

$ErrorActionPreference = 'Stop'

$racine = Split-Path -Parent $PSScriptRoot
. (Join-Path $PSScriptRoot 'ftps.ps1')

$dossierFront = '/movies'
$dossierBack = '/moviesServer'

$livrerFront = -not $Only -or $Only -eq 'front'
$livrerBack = -not $Only -or $Only -eq 'back'

function Write-Etape { param([string]$Texte) Write-Host "`n=== $Texte" -ForegroundColor Cyan }

# Chemin relatif au format distant, sans dépendre du séparateur Windows.
# [char]92 plutôt qu'un antislash littéral : cf. pièges de la skill deploy-lws.
function Get-CheminRelatif {
	param([string]$Complet, [string]$Base)
	return $Complet.Substring($Base.Length).TrimStart([char]92, '/').Replace([char]92, '/')
}

if ($TestConnection) {
	Write-Etape 'Connexion'
	Test-FtpsConnection | Format-List
	foreach ($d in @("$dossierFront/", "$dossierBack/", "$dossierBack/api/", "$dossierBack/php/")) {
		Write-Host "`n--- $d" -ForegroundColor DarkGray
		try { Get-RemoteList $d } catch { Write-Host $_.Exception.Message -ForegroundColor Red }
	}
	return
}

# --------------------------------------------------------------------------
# 1. Build du front
# --------------------------------------------------------------------------
if ($livrerFront -and -not $SkipBuild) {
	Write-Etape "Build du front (base href $dossierFront/)"
	Push-Location $racine
	try {
		& npx ng build --configuration production --base-href "$dossierFront/"
		if ($LASTEXITCODE -ne 0) { throw "ng build a échoué (code $LASTEXITCODE) — rien n'a été envoyé." }
	} finally { Pop-Location }
}

# --------------------------------------------------------------------------
# 2. Plan de transfert
# --------------------------------------------------------------------------
$transferts = @()

# Le back part en premier : quand le nouveau front arrive, l'API qu'il appelle
# est déjà à jour.
if ($livrerBack) {
	$back = Join-Path $racine 'back'
	$fichiersBack = @()

	foreach ($f in Get-ChildItem $back -Recurse -File -Force) {
		$relatif = Get-CheminRelatif $f.FullName $back

		if ($relatif -eq '.gitignore') { continue }
		if ($relatif -eq 'php/config.sample.php') { continue }
		if ($relatif -like 'sql/*') { continue }
		if ($relatif -like 'images/people/*' -or $relatif -like 'images/posters/*') { continue }
		if ($relatif -eq 'php/config.php' -and -not $IncludeConfig) { continue }
		if ($relatif -like 'images/*' -and -not $IncludeImages) { continue }

		$fichiersBack += [pscustomobject]@{
			Local   = $f.FullName
			Distant = "$dossierBack/$relatif"
			Taille  = $f.Length
			Partie  = 'back'
			# config.php, puis DB.class.php qui en dépend, puis les autres
			# bibliothèques de php/ (datamodel.php, collabs.php...), et enfin les
			# points d'entrée qui les incluent : une requête arrivant en cours de
			# livraison ne trouve jamais un include manquant.
			Ordre   = if ($relatif -eq 'php/config.php') { 0 }
			          elseif ($relatif -eq 'php/DB.class.php') { 1 }
			          elseif ($relatif -like 'php/*') { 2 }
			          else { 3 }
		}
	}

	if (-not $fichiersBack) { throw "Aucun fichier back à livrer : vérifier les exclusions." }
	$transferts += $fichiersBack | Sort-Object Ordre, Distant
}

if ($livrerFront) {
	# Angular >= 17 : la sortie est dans dist/browser, c'est son CONTENU qu'on publie.
	$dist = Join-Path $racine 'dist/browser'
	if (-not (Test-Path $dist)) { throw "$dist absent : relancer sans -SkipBuild." }

	$fichiers = Get-ChildItem $dist -Recurse -File -Force
	if (-not $fichiers) { throw "$dist est vide." }

	# index.html en dernier : les bundles qu'il référence doivent déjà être en place.
	foreach ($f in ($fichiers | Sort-Object { $_.Name -eq 'index.html' }, FullName)) {
		$transferts += [pscustomobject]@{
			Local   = $f.FullName
			Distant = "$dossierFront/$(Get-CheminRelatif $f.FullName $dist)"
			Taille  = $f.Length
			Partie  = 'front'
			Ordre   = 4
		}
	}
}

if (-not $transferts) { throw "Rien à livrer." }

# --------------------------------------------------------------------------
# 2 bis. Nettoyage des bundles obsolètes (opt-in)
# --------------------------------------------------------------------------
if ($livrerFront -and $Cleanup) {
	Write-Etape "Nettoyage de $dossierFront"

	$aEnvoyer = @($transferts | Where-Object { $_.Partie -eq 'front' } | ForEach-Object {
		$_.Distant.Substring($dossierFront.Length + 1)
	})
	# Garde-fou : un build cassé ou vide ne doit pas vider le site.
	if ($aEnvoyer.Count -lt 5) {
		throw "Seulement $($aEnvoyer.Count) fichier(s) dans le build : nettoyage refusé."
	}

	$distants = @(Get-RemoteFiles -Path $dossierFront)
	# Les fichiers cachés (.htaccess et consorts) ne sont jamais du build : on n'y touche pas.
	$residuels = @($distants | Where-Object { -not $_.Cache -and $_.Relatif -notin $aEnvoyer })

	if (-not $residuels) {
		Write-Host "  rien à supprimer ($($distants.Count) fichier(s) en place)"
	} elseif ($DryRun) {
		Write-Host "  $($residuels.Count) fichier(s) seraient supprimés" -ForegroundColor Yellow
		$residuels | ForEach-Object { "       {0,9:N0} o   {1}" -f $_.Taille, $_.Relatif }
	} else {
		$efface = 0
		foreach ($r in $residuels) {
			try { Remove-RemoteFile -RemotePath $r.Chemin; $efface++ }
			catch { Write-Host "  suppression impossible : $($r.Relatif)" -ForegroundColor DarkYellow }
		}
		Write-Host "  $efface fichier(s) supprimé(s)" -ForegroundColor Green
	}
}

# --------------------------------------------------------------------------
# 3. Transfert
# --------------------------------------------------------------------------
if ($livrerBack -and -not $IncludeConfig) {
	Write-Host "  php/config.php non inclus (utiliser -IncludeConfig si besoin)" -ForegroundColor DarkGray
}
if ($livrerBack -and -not $IncludeImages) {
	Write-Host "  back/images/ non inclus (utiliser -IncludeImages si besoin)" -ForegroundColor DarkGray
}

# Garde-fou : depuis l'externalisation des identifiants, php/DB.class.php fait un
# require de php/config.php. Livrer le premier sans que le second soit en place
# casse tout le back. On vérifie donc la présence du fichier distant.
if ($livrerBack -and -not $IncludeConfig -and -not $DryRun) {
	if (($transferts | Where-Object { $_.Distant -eq "$dossierBack/php/DB.class.php" })) {
		if ((Get-RemoteFileSize "$dossierBack/php/config.php") -lt 0) {
			throw "$dossierBack/php/config.php absent du serveur : relancer avec -IncludeConfig, sinon le back tombera sur un require d'un fichier absent."
		}
	}
}
if ($IncludeConfig -and -not (Test-Path (Join-Path $racine 'back/php/config.php'))) {
	throw "back/php/config.php absent en local : le créer depuis config.sample.php."
}

if ($DryRun) {
	Write-Etape "Plan de transfert (aucune connexion)"
	$transferts | ForEach-Object { "{0,-6} {1,9:N0} o   {2}" -f $_.Partie, $_.Taille, $_.Distant }
	$parPartie = $transferts | Group-Object Partie | ForEach-Object { "$($_.Name) : $($_.Count)" }
	Write-Host "`n$($parPartie -join ' | ') — total $([math]::Round((($transferts | Measure-Object Taille -Sum).Sum) / 1KB)) Ko" -ForegroundColor Yellow
	Write-Host "DryRun : rien n'a été transféré." -ForegroundColor Yellow
	return
}

Write-Etape "Livraison de $($transferts.Count) fichier(s)"
$envoyes = 0
foreach ($t in $transferts) {
	Send-RemoteFile -LocalPath $t.Local -RemotePath $t.Distant | Out-Null
	$envoyes++
	Write-Host ("  {0,4}/{1}  {2}" -f $envoyes, $transferts.Count, $t.Distant)
}

Write-Host "`n$envoyes fichier(s) livré(s)." -ForegroundColor Green
Write-Host "Vérifier : https://louiecinephile.fr$dossierFront/" -ForegroundColor Green
# LWS : opcache.revalidate_freq = 60. Un .php déjà servi peut renvoyer
# l'ancienne version pendant une minute — ce n'est pas un transfert raté.
Write-Host "Les changements PHP peuvent mettre jusqu'à 60 s à prendre effet (opcache LWS)." -ForegroundColor DarkGray
