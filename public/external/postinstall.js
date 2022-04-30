const fs = require('fs');
const https = require('https');
const path = require('path');

const walkFilesSync = function (f, callback) {
    if (fs.lstatSync(f).isDirectory()) {
        return fs.readdirSync(f).flatMap((f2) => walkFilesSync(path.join(f, f2), callback));
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

// copy non-minified JS to make it available from the same directory as the minified version
if (fs.existsSync(path.join(__dirname, 'node_modules/form-serializer/jquery.serialize-object.js'))) {
    fs.renameSync(
        path.join(__dirname, 'node_modules/form-serializer/jquery.serialize-object.js'),
        path.join(__dirname, 'node_modules/form-serializer/dist/jquery.serialize-object.js'),
    );
}

// download Fomantic-UI license
// remove once https://github.com/fomantic/Fomantic-UI/issues/2356 is fixed and v2.9.0 is released
https.get(
    'https://raw.githubusercontent.com/fomantic/Fomantic-UI/2.8.8/LICENSE.md',
    (response) => response.pipe(fs.createWriteStream(path.join(__dirname, 'node_modules/fomantic-ui-css/LICENSE.md'))),
);

const cssUrlPattern = '((?<!\\w)url\\([\'"]?(?!data:))((?:[^(){}\\\\\'"]|\\\\.)*)([\'"]?\\))';

// use native font stack in Fomantic UI
// remove once https://github.com/fomantic/Fomantic-UI/issues/2355 is fixed and released
walkFilesSync(path.join(__dirname, 'node_modules/fomantic-ui-css'), (f) => {
    updateFileSync(f, (data) => {
        if (data.includes('\0') || !f.endsWith('.css')) {
            return;
        }

        data = data.replace(new RegExp('@import ' + cssUrlPattern + ';?', 'g'), (m, m1, m2, m3) => {
            if (m2.startsWith('https://fonts.googleapis.com/css2?family=Lato:')) {
                return '';
            }

            return m;
        });

        data = data.replace(/(font-family: *)([^{};]*)(;?)/g, (m, m1, m2, m3) => {
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
            ].join(f.match(/\.min\./) ? ',' : ', ');
            // based on https://github.com/twbs/bootstrap/blob/v5.1.3/scss/_variables.scss#L578
            const fontFamilySansMonospace = [
                'SFMono-Regular',
                'Menlo',
                'Monaco',
                'Consolas',
                '\'Liberation Mono\'',
                '\'Courier New\'',
                'monospace',
            ].join(f.match(/\.min\./) ? ',' : ', ');

            if (m2.match(/(?<!\w)Lato(?!\w)/i)) {
                return m1 + fontFamilySansSerif + m3;
            }
            if (m2.match(/(?<!\w)monospace(?!\w)/i)) {
                return m1 + fontFamilySansMonospace + m3;
            }
            if (m2 === 'inherit' || !m2.includes(',') || m2 === fontFamilySansSerif) {
                return m;
            }

            throw new Error('Font-family "' + m2 + '" has no mapping');
        });

        return data;
    });
});

// normalize EOL of text files
walkFilesSync(__dirname, (f) => {
    updateFileSync(f, (data) => {
        if (data.includes('\0') || f.match(/\.min\./)) {
            return;
        }

        data = data.replace(/\r?\n|\r/g, '\n');
        if (data.slice(-1) !== '\n') {
            data += '\n';
        }

        return data;
    });
});
