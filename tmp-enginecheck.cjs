const fs = require('fs');
const pkg = JSON.parse(fs.readFileSync('package.json', 'utf8'));
const lock = JSON.parse(fs.readFileSync('package-lock.json', 'utf8'));
const root = lock.packages[''];
console.log('package.json engines:', JSON.stringify(pkg.engines));
console.log('lock root engines  :', JSON.stringify(root.engines));
console.log('ENGINES SYNCED:', JSON.stringify(pkg.engines) === JSON.stringify(root.engines));
console.log('lock root keys:', Object.keys(root).join(', '));
console.log('lock devDependencies match:', JSON.stringify(pkg.devDependencies) === JSON.stringify(root.devDependencies));
