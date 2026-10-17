const fs = require('fs');
const path = require('path');
// Does rollup's compiled code reference @napi-rs/lzma at all?
const dir = path.join(__dirname, 'node_modules', 'rollup', 'dist');
let hits = [];
const walk = (d) => {
  for (const f of fs.readdirSync(d, { withFileTypes: true })) {
    const p = path.join(d, f.name);
    if (f.isDirectory()) walk(p);
    else if (/\.(js|cjs)$/.test(f.name)) {
      const t = fs.readFileSync(p, 'utf8');
      if (t.includes('napi-rs/lzma') || t.includes('lzma')) hits.push(p);
    }
  }
};
walk(dir);
console.log('rollup files referencing lzma:', hits.length ? hits.join(', ') : 'NONE');
