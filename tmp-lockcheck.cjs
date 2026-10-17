const fs = require('fs');
const lock = JSON.parse(fs.readFileSync('package-lock.json', 'utf8'));
const pkg = JSON.parse(fs.readFileSync('package.json', 'utf8'));
const root = lock.packages && lock.packages[''] ? lock.packages[''] : {};
console.log('lockfileVersion:', lock.lockfileVersion);
console.log('lock name:', lock.name);
console.log('root devDependencies:', JSON.stringify(root.devDependencies, null, 2));
console.log('root dependencies:', JSON.stringify(root.dependencies || null));
console.log('pkg devDependencies :', JSON.stringify(pkg.devDependencies, null, 2));
const a = JSON.stringify(Object.keys(root.devDependencies || {}).sort());
const b = JSON.stringify(Object.keys(pkg.devDependencies || {}).sort());
console.log('devDependency keys match:', a === b);
for (const [k, v] of Object.entries(pkg.devDependencies || {})) {
  const rv = (root.devDependencies || {})[k];
  if (rv !== v) console.log('SPEC MISMATCH', k, 'pkg=', v, 'lock=', rv);
}
for (const name of ['vite', 'laravel-vite-plugin', '@tailwindcss/vite', 'tailwindcss']) {
  const e = lock.packages['node_modules/' + name];
  console.log(name, '->', e ? e.version : 'NOT IN LOCK');
}
