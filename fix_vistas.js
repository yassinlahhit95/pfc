const fs = require('fs');
const path = require('path');

const mangledChars = {
    // Original list
    'Ã³': 'ó', 'Ã­': 'í', 'Ã©': 'é', 'Ã¡': 'á', 'Ãº': 'ú', 'Ã±': 'ñ',
    'Ã“': 'Ó', 'Ã': 'Í', 'Ã‰': 'É', 'Ã': 'Á', 'Ãš': 'Ú', 'Ã‘': 'Ñ',
    'Â¿': '¿', 'â†': '←', 'â‚¬': '€', 'Âª': 'ª',
    // Additional found in grep
    'Ã‚¿': '¿',
    'ÃƒÂ¡': 'á',
    'ÃƒÂ³': 'ó',
    'ÃƒÂ©': 'é',
    'ÃƒÂ­': 'í',
    'ÃƒÂº': 'ú',
    'ÃƒÂ±': 'ñ',
    'ÃƒÂ³': 'ó',
    'ÃƒÂ³': 'ó',
    'Â©': '©',
    'â€”': '—',
    'Â·': '·',
    'Âº': 'º',
    'Â¡': '¡'
};

function fixContent(content, filePath) {
    let originalContent = content;
    // 1. Fix mangled characters
    // Sort keys by length descending to avoid partial matches
    const sortedKeys = Object.keys(mangledChars).sort((a, b) => b.length - a.length);
    for (const mangled of sortedKeys) {
        const fixed = mangledChars[mangled];
        content = content.split(mangled).join(fixed);
    }

    // 2. Standardize Browser Titles
    content = content.replace(/(\$(?:titulo_pagina|tituloDelPagina))\s*=\s*"([^"]*)"/gi, (match, varName, titleVal) => {
        const cleanTitle = titleVal.replace(/^AULAPRO\s*\|\s*/i, '');
        return `${varName} = "AULAPRO | ${cleanTitle.toUpperCase()}"`;
    });
    content = content.replace(/(\$(?:titulo_pagina|tituloDelPagina))\s*=\s*'([^']*)'/gi, (match, varName, titleVal) => {
        const cleanTitle = titleVal.replace(/^AULAPRO\s*\|\s*/i, '');
        return `${varName} = "AULAPRO | ${cleanTitle.toUpperCase()}"`;
    });

    // 3. Standardize Section Headers <h1>
    content = content.replace(/<h1([^>]*)>([\s\S]*?)<\/h1>/gi, (match, attrs, inner) => {
        const parts = inner.split(/(<\?[\s\S]*?\?>)/g);
        const newInner = parts.map(part => {
            if (part.startsWith('<?')) {
                return part;
            } else {
                return part.toUpperCase();
            }
        }).join('');
        return `<h1${attrs}>${newInner}</h1>`;
    });

    return content;
}

function walk(dir, callback) {
    if (!fs.existsSync(dir)) return;
    fs.readdirSync(dir).forEach(f => {
        let dirPath = path.join(dir, f);
        let stat = fs.statSync(dirPath);
        if (stat.isDirectory()) {
            walk(dirPath, callback);
        } else {
            callback(dirPath);
        }
    });
}

walk('vistas', filePath => {
    if (filePath.endsWith('.php')) {
        try {
            const content = fs.readFileSync(filePath, 'utf8');
            const newContent = fixContent(content, filePath);
            if (newContent !== content) {
                fs.writeFileSync(filePath, newContent, 'utf8');
                console.log(`Fixed: ${filePath}`);
            }
        } catch (err) {
            console.error(`Error processing ${filePath}: ${err.message}`);
        }
    }
});
