// API utilities pour CatLife Tracker
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:6000/api';

// Helper pour les requêtes
async function request(endpoint, options = {}) {
  const url = `${API_BASE_URL}${endpoint}`;
  
  const config = {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      ...options.headers,
    },
  };

  try {
    const response = await fetch(url, config);
    
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.error || 'Une erreur est survenue');
    }
    
    return await response.json();
  } catch (error) {
    console.error('API Error:', error);
    throw error;
  }
}

// ====== CATS ======
export const catsAPI = {
  // Récupérer tous les chats
  getAll: () => request('/cats'),
  
  // Récupérer un chat par ID
  getById: (id) => request(`/cats/${id}`),
  
  // Créer un nouveau chat
  create: (catData) => request('/cats', {
    method: 'POST',
    body: JSON.stringify(catData),
  }),
  
  // Mettre à jour un chat
  update: (id, catData) => request(`/cats/${id}`, {
    method: 'PUT',
    body: JSON.stringify(catData),
  }),
  
  // Supprimer un chat
  delete: (id) => request(`/cats/${id}`, {
    method: 'DELETE',
  }),
};

// ====== WEIGHT ======
export const weightAPI = {
  // Récupérer l'historique du poids
  getHistory: (catId) => request(`/cats/${catId}/weight`),
  
  // Ajouter une pesée
  add: (catId, weightData) => request(`/cats/${catId}/weight`, {
    method: 'POST',
    body: JSON.stringify(weightData),
  }),
  
  // Supprimer une pesée
  delete: (catId, weightId) => request(`/cats/${catId}/weight/${weightId}`, {
    method: 'DELETE',
  }),
};

// ====== VACCINATIONS ======
export const vaccinationsAPI = {
  // Récupérer toutes les vaccinations
  getAll: (catId) => request(`/cats/${catId}/vaccinations`),
  
  // Ajouter une vaccination
  add: (catId, vaccinationData) => request(`/cats/${catId}/vaccinations`, {
    method: 'POST',
    body: JSON.stringify(vaccinationData),
  }),
  
  // Mettre à jour une vaccination
  update: (catId, vaccinationId, vaccinationData) => 
    request(`/cats/${catId}/vaccinations/${vaccinationId}`, {
      method: 'PUT',
      body: JSON.stringify(vaccinationData),
    }),
  
  // Supprimer une vaccination
  delete: (catId, vaccinationId) => 
    request(`/cats/${catId}/vaccinations/${vaccinationId}`, {
      method: 'DELETE',
    }),
};

// ====== TREATMENTS ======
export const treatmentsAPI = {
  // Récupérer tous les traitements
  getAll: (catId) => request(`/cats/${catId}/treatments`),
  
  // Ajouter un traitement
  add: (catId, treatmentData) => request(`/cats/${catId}/treatments`, {
    method: 'POST',
    body: JSON.stringify(treatmentData),
  }),
  
  // Mettre à jour un traitement
  update: (catId, treatmentId, treatmentData) => 
    request(`/cats/${catId}/treatments/${treatmentId}`, {
      method: 'PUT',
      body: JSON.stringify(treatmentData),
    }),
  
  // Supprimer un traitement
  delete: (catId, treatmentId) => 
    request(`/cats/${catId}/treatments/${treatmentId}`, {
      method: 'DELETE',
    }),
};

// ====== PHOTOS ======
export const photosAPI = {
  // Récupérer toutes les photos
  getAll: (catId) => request(`/cats/${catId}/photos`),
  
  // Upload une photo
  upload: async (catId, formData) => {
    const url = `${API_BASE_URL}/cats/${catId}/photos`;
    
    try {
      const response = await fetch(url, {
        method: 'POST',
        body: formData, // FormData ne nécessite pas de Content-Type
      });
      
      if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Erreur lors de l\'upload');
      }
      
      return await response.json();
    } catch (error) {
      console.error('Upload Error:', error);
      throw error;
    }
  },
  
  // Supprimer une photo
  delete: (catId, photoId) => 
    request(`/cats/${catId}/photos/${photoId}`, {
      method: 'DELETE',
    }),
};

// ====== REMINDERS ======
export const remindersAPI = {
  // Récupérer tous les rappels actifs
  getAll: (catId) => request(`/cats/${catId}/reminders`),
  
  // Créer un rappel
  create: (catId, reminderData) => request(`/cats/${catId}/reminders`, {
    method: 'POST',
    body: JSON.stringify(reminderData),
  }),
  
  // Marquer un rappel comme complété
  complete: (reminderId) => request(`/reminders/${reminderId}/complete`, {
    method: 'PATCH',
  }),
  
  // Supprimer un rappel
  delete: (reminderId) => request(`/reminders/${reminderId}`, {
    method: 'DELETE',
  }),
};

// ====== VET VISITS ======
export const vetVisitsAPI = {
  // Récupérer toutes les visites
  getAll: (catId) => request(`/cats/${catId}/vet-visits`),
  
  // Ajouter une visite
  add: (catId, visitData) => request(`/cats/${catId}/vet-visits`, {
    method: 'POST',
    body: JSON.stringify(visitData),
  }),
  
  // Mettre à jour une visite
  update: (catId, visitId, visitData) => 
    request(`/cats/${catId}/vet-visits/${visitId}`, {
      method: 'PUT',
      body: JSON.stringify(visitData),
    }),
  
  // Supprimer une visite
  delete: (catId, visitId) => 
    request(`/cats/${catId}/vet-visits/${visitId}`, {
      method: 'DELETE',
    }),
};

// ====== BACKUP ======
export const backupAPI = {
  // Créer un backup manuel
  create: () => request('/backup', {
    method: 'POST',
  }),
  
  // Vérifier l'état de l'API
  healthCheck: () => request('/health'),
};

// Export par défaut de toutes les APIs
export default {
  cats: catsAPI,
  weight: weightAPI,
  vaccinations: vaccinationsAPI,
  treatments: treatmentsAPI,
  photos: photosAPI,
  reminders: remindersAPI,
  vetVisits: vetVisitsAPI,
  backup: backupAPI,
};
