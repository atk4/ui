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

// download Fomantic-UI license
// remove once https://github.com/fomantic/Fomantic-UI/issues/2356 is fixed and v2.9.0 is released
https.get(
    'https://raw.githubusercontent.com/fomantic/Fomantic-UI/2.8.8/LICENSE.md',
    (response) => response.pipe(fs.createWriteStream(path.join(__dirname, 'node_modules/fomantic-ui-css/LICENSE.md')))
);

// copy non-minified JS to make it available from the same directory as the minified version
fs.copyFileSync(
    path.join(__dirname, 'node_modules/form-serializer/jquery.serialize-object.js'),
    path.join(__dirname, 'node_modules/form-serializer/dist/jquery.serialize-object.js'),
);

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
