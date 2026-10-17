const fs = require('fs');
const lock = JSON.parse(fs.readFileSync('package-lock.json', 'utf8'));
console.log('lock rollup entry keys:', Object.keys(lock.packages['node_modules/rollup']).join(', '));
console.log('lock rollup optionalDependencies:', JSON.stringify(lock.packages['node_modules/rollup'].optionalDependencies, null, 2));
console.log('lock rollup dependencies:', JSON.stringify(lock.packages['node_modules/rollup'].dependencies || null));
try {
  const inst = require('./node_modules/rollup/package.json');
  console.log('installed rollup version:', inst.version);
  console.log('installed rollup optionalDependencies:', JSON.stringify(inst.optionalDependencies || null));
  console.log('installed rollup dependencies:', JSON.stringify(inst.dependencies || null));
} catch (e) {
  console.log('installed rollup not readable:', e.message);
}
const lz = lock.packages['node_modules/@napi-rs/lzma-linux-x64-gnu'];
console.log('lzma platform entry:', JSON.stringify(lz, null, 2));
