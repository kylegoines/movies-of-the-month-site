import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  plugins: [tailwindcss()],
  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    origin: 'http://localhost:5173',
  },
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: 'src/main.js',
    },
  },
});
