# Summary

This folder contains application source code in [TypeScript](http://www.typescriptlang.org/) for Authentication, backend API calls, UI logic, and etc.

Also the code is integrated with [webpack](https://webpack.js.org/) in file `Main.ts`. If you compile the code with TypeScript Compiler (`tsc`) `Main.ts` will be excluded as it is not 'real' TypeScript file (note the line where it imports `.scss` styles file). However if compiled with webpack it will be the main entry point.
