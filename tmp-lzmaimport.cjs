const fs = require('fs');
const path = require('path');
const dir = path.join(__dirname, 'node_modules', 'rollup', 'dist');
const patterns = [/require\(['"]@napi-rs\/lzma/, /from\s+['"]@napi-rs\/lzma/, /import\(['"]@napi-rs\/lzma/];
let found = 0;
const walk = (d) => {
  for (const f of fs.readdirSync(d, { withFileTypes: true })) {
    const p = path.join(d, f.name);
    if (f.isDirectory()) walk(p);
    else if (/\.(js|cjs|mjs)$/.test(f.name)) {
      const t = fs.readFileSync(p, 'utf8');
      for (const re of patterns) {
        const m = t.match(re);
        if (m) {
          found++;
          console.log('MATCH', path.relative(__dirname, p), re, m[0]);
        }
      }
    }
  }
};
walk(dir);
console.log('total real imports of @napi-rs/lzma in rollup dist:', found);
