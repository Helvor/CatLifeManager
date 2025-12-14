import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  root: 'client',           // <-- indique le dossier client comme racine
  plugins: [react()],
  server: {
    port: 3000,
    proxy: {
      '/api': {
        target: 'http://localhost:6000',
        changeOrigin: true,
      },
    },
  },
  build: {
    outDir: 'dist',         // dist sera dans client/dist
    emptyOutDir: true,
  },
});

