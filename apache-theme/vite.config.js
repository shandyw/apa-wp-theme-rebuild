import { defineConfig } from 'vite';
import { fileURLToPath } from 'node:url';

export default defineConfig({
  root: '.',
  base: './',
  publicDir: false,
  build: {
    manifest: true,
    outDir: 'assets/build',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: fileURLToPath(new URL('src/js/main.js', import.meta.url)),
        global: fileURLToPath(new URL('src/css/global.css', import.meta.url)),
        popup: fileURLToPath(new URL('src/js/popup-main.js', import.meta.url))
      },
      output: {
        entryFileNames: 'js/[name].[hash].js',
        chunkFileNames: 'js/[name].[hash].js',
        assetFileNames: (assetInfo) => {
          const name = assetInfo.name || '';

          if (name.endsWith('.css')) {
            return 'css/[name].[hash][extname]';
          }

          if (/\.(woff2?|ttf|otf|eot)$/i.test(name)) {
            return 'fonts/[name].[hash][extname]';
          }

          if (/\.(png|jpe?g|gif|webp|avif|svg)$/i.test(name)) {
            return 'images/[name].[hash][extname]';
          }

          return 'assets/[name].[hash][extname]';
        }
      }
    }
  }
});
