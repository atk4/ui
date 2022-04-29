const fs = require('fs');
const path = require('path');

const walkFilesSync = function (f, callback) {
    if (fs.lstatSync(f).isDirectory()) {
        return fs.readdirSync(f).flatMap(f2 => walkFilesSync(path.join(f, f2), callback));
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

// normalize EOL of text files
walkFilesSync(__dirname, function (f) {
    updateFileSync(f, function (data) {
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

fs.copyFileSync(
    path.join(__dirname, 'node_modules/form-serializer/jquery.serialize-object.js'),
    path.join(__dirname, 'node_modules/form-serializer/dist/jquery.serialize-object.js'),
);
