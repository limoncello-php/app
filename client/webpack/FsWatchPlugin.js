const fs = require('fs');
const path = require('path');

module.exports = class {
    constructor(settings = {extraRootFolders: [], extraFolders: [], extraFiles: []}) {
        this._extraFiles = settings.extraFiles;
        this._extraFolders = settings.extraFolders;
        this._extraRootFolders = settings.extraRootFolders;
    }

    // noinspection JSUnusedGlobalSymbols
    apply(compiler) {
        compiler.hooks.afterCompile.tap('LimoncelloFsWatch', (compilation) => {
            const webpackFiles = Array.isArray(compilation.fileDependencies) ? compilation.fileDependencies : [];
            const webpackFolders = Array.isArray(compilation.contextDependencies) ? compilation.contextDependencies : [];

            let extraFolders = [];
            this._extraRootFolders.forEach(extraRootFolder => {
                extraFolders = extraFolders.concat(this.addAllNestedFolders(extraRootFolder));
            });

            compilation.fileDependencies = this.constructor.makeUniqueAndNonNull(
                webpackFiles.concat(this._extraFiles)
            );
            compilation.contextDependencies = this.constructor.makeUniqueAndNonNull(
                webpackFolders.concat(extraFolders).concat(this._extraRootFolders).concat(this._extraFolders)
            );
        });
    }

    addAllNestedFolders(root, folders = []) {
        fs.readdirSync(root).forEach(fileOrFolder => {
            const fullPath = path.resolve(root, fileOrFolder);
            if (fs.lstatSync(fullPath).isDirectory() === true) {
                folders.push(fullPath);
                this.addAllNestedFolders(fullPath, folders);
            }
        });

        return folders;
    }

    static makeUniqueAndNonNull(array) {
        return array.filter((value, index, self) => value != null && self.indexOf(value) === index);
    }
};
