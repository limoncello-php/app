const path = require('path');

const rootDir = path.resolve(__dirname, '..', '..', 'server', 'resources', 'views');
const filePaths = [
    // path.resolve(rootDir, 'pages', 'en', 'master.html.twig'),
];
const folderPaths = [
    path.resolve(rootDir, 'emails'),
    path.resolve(rootDir, 'pages', 'en'),
];

function concatUnique(array1, array2) {
    return array1.concat(array2).filter((value, index, self) => self.indexOf(value) === index);
}

module.exports = class {
    apply(compiler) {
        compiler.plugin('after-compile', (compilation, callback) => {
            compilation.fileDependencies = concatUnique(compilation.fileDependencies, filePaths);
            compilation.contextDependencies = concatUnique(compilation.contextDependencies, folderPaths);

            callback();
        });
    }
};
