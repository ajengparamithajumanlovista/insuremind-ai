const l = require('./package-lock.json');
for (const [n, e] of Object.entries(l.packages)) {
  if (e.engines && e.engines.node) console.log(n || '(root)', e.version || '', '=>', e.engines.node);
}
