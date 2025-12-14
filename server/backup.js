const { createBackup } = require('./db');

// Script de backup manuel
console.log('🔄 Démarrage du backup...');
createBackup();
console.log('✅ Backup terminé avec succès');

// Fermer le processus après 2 secondes
setTimeout(() => {
  process.exit(0);
}, 2000);
