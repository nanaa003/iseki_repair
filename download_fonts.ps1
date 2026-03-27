$fonts = @(
    @{ weight=300; url='https://fonts.gstatic.com/s/inter/v20/UcCO3FwrK3iLTeHuS_nVMrMxCp50SjIw2boKoduKmMEVuOKfMZg.ttf'; name='inter-300.ttf' },
    @{ weight=400; url='https://fonts.gstatic.com/s/inter/v20/UcCO3FwrK3iLTeHuS_nVMrMxCp50SjIw2boKoduKmMEVuLyfMZg.ttf'; name='inter-400.ttf' },
    @{ weight=500; url='https://fonts.gstatic.com/s/inter/v20/UcCO3FwrK3iLTeHuS_nVMrMxCp50SjIw2boKoduKmMEVuI6fMZg.ttf'; name='inter-500.ttf' },
    @{ weight=600; url='https://fonts.gstatic.com/s/inter/v20/UcCO3FwrK3iLTeHuS_nVMrMxCp50SjIw2boKoduKmMEVuGKYMZg.ttf'; name='inter-600.ttf' },
    @{ weight=700; url='https://fonts.gstatic.com/s/inter/v20/UcCO3FwrK3iLTeHuS_nVMrMxCp50SjIw2boKoduKmMEVuFuYMZg.ttf'; name='inter-700.ttf' },
    @{ weight=800; url='https://fonts.gstatic.com/s/inter/v20/UcCO3FwrK3iLTeHuS_nVMrMxCp50SjIw2boKoduKmMEVuDyYMZg.ttf'; name='inter-800.ttf' }
)

foreach ($f in $fonts) {
    Write-Output "Downloading Inter weight $($f.weight)..."
    Invoke-WebRequest -Uri $f.url -OutFile "public\assets\fonts\inter\$($f.name)"
}
Write-Output "All Inter fonts downloaded"
