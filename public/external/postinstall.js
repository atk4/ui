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

// replace absolute URLs with relative paths
walkFilesSync(__dirname, (f) => {
    updateFileSync(f, (data) => {
        if (data.includes('\0') || !f.endsWith('.css')) {
            return;
        }

        data = data.replace(new RegExp(cssUrlPattern, 'g'), (m, m1, m2, m3) => {
            if ((!m2.startsWith('https://') && (!m2.startsWith('/') || m2.startsWith('//:'))) || m2.startsWith('https://fonts.googleapis.com/css2?family=Lato:')) {
                return m;
            }

            const pathMap = {
                'https://twemoji.maxcdn.com/v/latest/svg/': path.join(__dirname, 'node_modules/twemoji/assets/svg/'),
            };

            const pathMapKeys = Object.keys(pathMap);
            for (let i = 0; i < pathMapKeys.length; i++) {
                const k = pathMapKeys[i];
                if (m2.startsWith(k)) {
                    const kRel = m2.substring(k.length);
                    const pathLocal = path.join(pathMap[k], kRel);
                    const pathRel = path.relative(path.dirname(f), pathLocal);

                    return m1 + pathRel.replaceAll('\\', '/') + m3;
                }
            }

            throw new Error('URL "' + m2 + '" has no local file mapping');
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
