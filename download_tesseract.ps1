# Create directories
New-Item -ItemType Directory -Force -Path "public\assets\js\tesseract"
New-Item -ItemType Directory -Force -Path "public\assets\lang-data"

# Download worker and wasm
Invoke-WebRequest -Uri 'https://unpkg.com/tesseract.js@5/dist/worker.min.js' -OutFile 'public\assets\js\tesseract\worker.min.js'
Invoke-WebRequest -Uri 'https://unpkg.com/tesseract.js-core@5/tesseract-core.wasm.js' -OutFile 'public\assets\js\tesseract\tesseract-core.wasm.js'

# Download traineddata
Invoke-WebRequest -Uri 'https://raw.githubusercontent.com/naptha/tessdata/gh-pages/4.0.0/ind.traineddata.gz' -OutFile 'public\assets\lang-data\ind.traineddata.gz'
Invoke-WebRequest -Uri 'https://raw.githubusercontent.com/naptha/tessdata/gh-pages/4.0.0/eng.traineddata.gz' -OutFile 'public\assets\lang-data\eng.traineddata.gz'

Write-Output "Tesseract offline files downloaded successfully"
