const { execSync } = require('child_process');
// Reproduce nixpacks' real environment: no inherited env vars, plus generic
// build-time variables and a Node 20 image without esbuild's binary cache.
const base = {
  PATH: process.env.PATH,
  HOME: process.env.HOME,
  USERPROFILE: process.env.USERPROFILE,
  SystemRoot: process.env.SystemRoot,
  ComSpec: process.env.ComSpec,
  PATHEXT: process.env.PATHEXT,
  APPDATA: process.env.APPDATA,
  LOCALAPPDATA: process.env.LOCALAPPDATA,
  TEMP: process.env.TEMP,
  TMP: process.env.TMP,
  NODE_ENV: 'production',
  CI: 'true',
  NIXPACKS: 'true',
  RAILWAY_ENVIRONMENT: 'production',
};
const run = (label, cmd) => {
  console.log('=== ' + label + ' ===');
  try {
    const out = execSync(cmd, { cwd: __dirname, env: base, encoding: 'utf8', stdio: 'pipe' });
    console.log(out.trim() || '(no output)');
    console.log(label + ' EXIT=0');
  } catch (e) {
    console.log((e.stdout || '').toString().trim());
    console.log((e.stderr || '').toString().trim());
    console.log(label + ' EXIT=' + e.status);
  }
};
run('npm ci (production env)', 'npm ci --include=dev');
run('vite build (production env)', 'npm run build');
run('show config', 'node -e "const c=require(\'vite\').resolveConfig;c({configFile:\'vite.config.js\'}, \'build\').then(r=>console.log(\'root=\'+r.root, \'plugins=\'+r.plugins.map(p=>p.name).join(\',\'))).catch(e=>{console.error(e.message);process.exit(1)})"');
