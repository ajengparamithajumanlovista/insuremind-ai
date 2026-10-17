const fs = require('fs');
const lock = JSON.parse(fs.readFileSync('package-lock.json', 'utf8'));
const p = lock.packages;
// Who depends on the lzma packages?
for (const [name, e] of Object.entries(p)) {
  const deps = { ...(e.dependencies || {}), ...(e.optionalDependencies || {}), ...(e.devDependencies || {}) };
  for (const [d, v] of Object.entries(deps)) {
    if (d.includes('lzma')) console.log('DEP', (name || '(root)'), '->', d, v);
  }
}
const lz = p['node_modules/@napi-rs/lzma'];
console.log('lzma entry present in lock:', !!lz);
if (lz) console.log(JSON.stringify(lz, null, 2));
console.log('platform subpackages in lock:', Object.keys(p).filter((k) => k.includes('lzma')).join(', '));
