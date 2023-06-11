const fs = require('node:fs');
const path = require('node:path');

const walkFilesSync = function (f, callback) {
    if (fs.lstatSync(f).isDirectory()) {
        return fs.readdirSync(f).sort().flatMap((f2) => walkFilesSync(path.join(f, f2), callback));
    }

    return [callback(f)];
};

const updateFileSync = function (f, callback) {
    const dataOrig = fs.readFileSync(f, 'binary');
    const dataNew = callback(dataOrig);
    if (dataNew !== undefined && dataNew !== dataOrig) {
        fs.writeFileSync(f, dataNew, { encoding: 'binary' });
    }
};

// move node_modules/ files to parent directory
for (const f of [
    '@highlightjs',
    '@shopify',
    'chart.js',
    'flatpickr',
    'fomantic-ui',
    'jquery',
    'twemoji',
]) {
    fs.cpSync(
        path.join(path.join(__dirname, 'node_modules'), f),
        path.join(__dirname, f),
        { recursive: true }
    );
}

const cssUrlPattern = '((?<!\\w)url\\([\'"]?(?!data:))((?:[^(){}\\\\\'"]|\\\\.)*)([\'"]?\\))';

// use native font stack in Fomantic-UI
// https://github.com/fomantic/Fomantic-UI/issues/2355
walkFilesSync(path.join(__dirname, 'fomantic-ui'), (f) => {
    updateFileSync(f, (data) => {
        if (!f.endsWith('.css')) {
            return;
        }

        data = data.replaceAll(new RegExp('\\s*@font-face\\s*\\{[^{}]*' + cssUrlPattern + '[^{}]+\\}', 'g'), (m, m1, m2, m3) => {
            if (m2.includes('/assets/fonts/Lato')) {
                return '';
            }

            return m;
        });

        data = data.replaceAll(/(font-family: *)([^;{}]*)(;?)/g, (m, m1, m2, m3) => {
            // based on https://github.com/twbs/bootstrap/blob/v5.1.3/scss/_variables.scss#L577
            const fontFamilySansSerif = [
                'system-ui',
                '-apple-system',
                '\'Segoe UI\'',
                'Roboto',
                '\'Helvetica Neue\'',
                'Arial',
                '\'Noto Sans\'',
                '\'Liberation Sans\'',
                'sans-serif',
                '\'Apple Color Emoji\'',
                '\'Segoe UI Emoji\'',
                '\'Segoe UI Symbol\'',
                '\'Noto Color Emoji\'',
            ].join(/\.min\./.test(f) ? ',' : ', ');
            // based on https://github.com/twbs/bootstrap/blob/v5.1.3/scss/_variables.scss#L578
            const fontFamilySansMonospace = [
                'SFMono-Regular',
                'Menlo',
                'Monaco',
                'Consolas',
                '\'Liberation Mono\'',
                '\'Courier New\'',
                'monospace',
            ].join(/\.min\./.test(f) ? ',' : ', ');

            if (/(?<!\w)lato(?!\w)/i.test(m2)) {
                return m1 + fontFamilySansSerif + m3;
            }
            if (/(?<!\w)monospace(?!\w)/i.test(m2)) {
                return m1 + fontFamilySansMonospace + m3;
            }
            if (m2 === 'inherit' || !m2.includes(',') || m2 === fontFamilySansSerif) {
                return m;
            }

            throw new Error('Font-family "' + m2 + '" has no mapping');
        });

        // change bold (700) font weight to 600 to match the original Lato font weight better
        // see https://github.com/fomantic/Fomantic-UI/pull/2359#discussion_r867457881 discussion
        data = data.replaceAll(/(font-weight: *)([^;{}]*)(;?)/g, (m, m1, m2, m3) => {
            if (m2 === 'bold' || m2 === '700') {
                return m1 + '600' + m3;
            }

            return m;
        });

        return data;
    });
});

// remove links to fonts with format other than woff2 from Fomantic-UI
walkFilesSync(path.join(__dirname, 'fomantic-ui'), (f) => {
    updateFileSync(f, (data) => {
        if (!f.endsWith('.css')) {
            return;
        }

        data = data.replaceAll(new RegExp('(src:\\s*(?!\\s))[^{};]*((?=[^{};,]+\\.woff2(?!\\w))' + cssUrlPattern + ')[^{};]*(;)', 'g'), '$1$2 format(\'woff2\')$6');

        return data;
    });
});

// remove twemoji images from Fomantic-UI, reduce total size by about 3500 files and 25 MB
// wait until https://github.com/fomantic/Fomantic-UI/issues/2363 is implemented or pack all images in one phar
walkFilesSync(path.join(__dirname, 'fomantic-ui'), (f) => {
    updateFileSync(f, (data) => {
        if (!f.endsWith('.css')) {
            return;
        }

        data = data.replaceAll(/\s*((?<!\w)em\[data-emoji=[^[\\\]{}]+]::before,?\s*)+{[^{}]*background-image:[^{}]+}/g, '');

        return data;
    });
});

// replace absolute URLs with relative paths
walkFilesSync(__dirname, (f) => {
    updateFileSync(f, (data) => {
        if (f.startsWith(path.join(__dirname, 'node_modules/'))
            || !f.endsWith('.css')
            || f.startsWith(path.join(__dirname, 'chart.js/dist/docs/'))
        ) {
            return;
        }

        data = data.replaceAll(new RegExp(cssUrlPattern, 'g'), (m, m1, m2, m3) => {
            let pathRel = null;
            if (m2.startsWith('http://') || m2.startsWith('https://') || m2.startsWith('//')) {
                const pathMap = {
                    'https://cdn.jsdelivr.net/gh/twitter/twemoji@latest/assets/svg/': path.join(__dirname, 'twemoji/assets/svg/'),
                };

                const pathMapKeys = Object.keys(pathMap);
                for (const k of pathMapKeys) {
                    if (m2.startsWith(k)) {
                        const kRel = m2.slice(k.length);
                        const pathLocal = path.join(pathMap[k], kRel);
                        pathRel = path.relative(path.dirname(f), pathLocal);

                        break;
                    }
                }

                if (pathRel === null) {
                    throw new Error('URL "' + m2 + '" linked from "' + f + '"  has no local file mapping');
                }
            } else {
                pathRel = m2;
            }

            pathRel = pathRel.replaceAll('\\', '/');
            if (!pathRel.startsWith('.')) {
                pathRel = './' + pathRel;
            }

            if (!fs.existsSync(path.join(path.dirname(f), pathRel))) {
                throw new Error('File "' + pathRel + '" linked from "' + f + '" does not exist');
            }

            return m1 + pathRel + m3;
        });

        return data;
    });
});

// remove repeated Fomantic-UI version comments for easier diff
// https://github.com/fomantic/Fomantic-UI/issues/2468
walkFilesSync(path.join(__dirname, 'fomantic-ui'), (f) => {
    updateFileSync(f, (data) => {
        if (!f.endsWith('.css') && !f.endsWith('.js')) {
            return;
        }

        data = data.replaceAll(/(?<!^)\/\*!(?:(?!\/\*).)*# Fomantic-UI \d+\.\d+\.(?:(?!\/\*).)*MIT license(?:(?!\/\*).)*\*\/\n?/gs, '');

        return data;
    });
});

// replace Fomantic-UI modal module hideAll function
// https://github.com/fomantic/Fomantic-UI/issues/2526
walkFilesSync(path.join(__dirname, 'fomantic-ui'), (f) => {
    updateFileSync(f, (data) => {
        if (!f.endsWith('.js')) {
            return;
        }

        data = data.replaceAll(/(!\w+\.hide)All(\(\))/gs, '$1$2');

        return data;
    });
});

const cssTokenSelectorPattern = '(?:(?:[^(){}\'",+>~;/\\s]|\'[^\'\\\\{};]*\'|"[^"\\\\{};]*")+)';
const cssSimpleSelectorPattern = '(?:' + cssTokenSelectorPattern + '(?:\\(\\s*' + cssTokenSelectorPattern + '\\s*\\)' + cssTokenSelectorPattern + '?)*)';
const cssSingleSelectorPattern = '(?:' + cssSimpleSelectorPattern + '(?:\\s*[ +>~]\\s*' + cssSimpleSelectorPattern + ')*)';

// update Fomantic-UI ":first-child" selectors to work with immediately closed form tag
// https://github.com/atk4/ui/issues/1970
walkFilesSync(path.join(__dirname, 'fomantic-ui'), (f) => {
    updateFileSync(f, (data) => {
        if (!f.endsWith('.css')) {
            return;
        }

        data = data.replaceAll(new RegExp(cssSingleSelectorPattern + '(?=\\s*(,\\s*' + cssSingleSelectorPattern + '\\s*)*\\{)', 'g'), (mSingle) => (mSingle.includes(':first-child')
            ? mSingle.replaceAll(new RegExp('^(.*?)(' + cssSimpleSelectorPattern + '):first-child(' + cssSimpleSelectorPattern + '?)(.*)$', 'g'), (m, m1, m2, m3, m4) => (m1 === '' || /\.form(?!\w|.*[ +>~])/g.test(m1.trimEnd())
                ? m + ', '
                    + m1.trimEnd() + (m1 !== '' ? ' ' : '')
                    + 'form:first-child + ' + m2 + m3
                    + (m4 !== '' ? ' ' : '') + m4.trimStart()
                : m))
            : mSingle));

        return data;
    });
});

// normalize EOL of text files
walkFilesSync(__dirname, (f) => {
    updateFileSync(f, (data) => {
        if (data.includes('\0') || /\.min\./i.test(f)) {
            return;
        }

        data = data.replaceAll(/\r?\n|\r/g, '\n');
        if (data.slice(-1) !== '\n') {
            data += '\n';
        }

        return data;
    });
});
