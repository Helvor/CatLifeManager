// Ce fichier doit contenir exactement le même code que l'artifact React 
// "Application React - CatLife Tracker" créé précédemment.
// 
// Copiez le contenu de cet artifact dans ce fichier.
//
// Le composant commence par :
// import React, { useState, useEffect } from 'react';
// import { Heart, Syringe, Weight, Camera, Calendar, PlusCircle, Bell, TrendingUp, FileText, Activity } from 'lucide-react';
// import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, BarChart, Bar } from 'recharts';
//
// export default function CatLifeTracker() {
//   ...
// }

import React, { useState, useEffect } from 'react';
import { Heart, Syringe, Dumbbell, Camera, Calendar, PlusCircle, Bell, TrendingUp, FileText, Activity, Trash2 } from 'lucide-react';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, BarChart, Bar } from 'recharts';
import AddCatModal from './AddCatModal';
import AddVaccinationModal from './AddVaccinationModal';
import AddTreatmentModal from './AddTreatmentModal';
import AddWeightModal from './AddWeightModal';
import AddPhotoModal from './AddPhotoModal';
import api from '../utils/api';

export default function CatLifeTracker() {
  const [cats, setCats] = useState([]);
  const [selectedCat, setSelectedCat] = useState(null);
  const [activeTab, setActiveTab] = useState('dashboard');
  const [weightData, setWeightData] = useState([]);
  const [vaccinations, setVaccinations] = useState([]);
  const [treatments, setTreatments] = useState([]);
  const [photos, setPhotos] = useState([]);
  const [reminders, setReminders] = useState([]);
  const [vetVisits, setVetVisits] = useState([]);
  
  // Modals
  const [showAddCatModal, setShowAddCatModal] = useState(false);
  const [showAddVaccinationModal, setShowAddVaccinationModal] = useState(false);
  const [showAddTreatmentModal, setShowAddTreatmentModal] = useState(false);
  const [showAddWeightModal, setShowAddWeightModal] = useState(false);
  const [showAddPhotoModal, setShowAddPhotoModal] = useState(false);

  // Charger les chats au démarrage
  useEffect(() => {
    loadCats();
  }, []);

  // Charger les données du chat sélectionné
  useEffect(() => {
    if (selectedCat) {
      loadCatData(selectedCat.id);
    }
  }, [selectedCat]);

  const loadCats = async () => {
    try {
      const data = await api.cats.getAll();
      setCats(data);
      if (data.length > 0 && !selectedCat) {
        setSelectedCat(data[0]);
      }
    } catch (error) {
      console.error('Erreur chargement chats:', error);
    }
  };

  const loadCatData = async (catId) => {
    try {
      const [weights, vaccs, treats, pics, rems, visits] = await Promise.all([
        api.weight.getHistory(catId),
        api.vaccinations.getAll(catId),
        api.treatments.getAll(catId),
        api.photos.getAll(catId),
        api.reminders.getAll(catId),
        api.vetVisits.getAll(catId)
      ]);

      setWeightData(weights);
      setVaccinations(vaccs);
      setTreatments(treats);
      setPhotos(pics);
      setReminders(rems);
      
      // Transformer les visites pour le graphique
      const visitsByMonth = {};
      visits.forEach(visit => {
        const month = visit.date.substring(0, 7);
        visitsByMonth[month] = (visitsByMonth[month] || 0) + 1;
      });
      const chartData = Object.keys(visitsByMonth).map(month => ({
        date: month,
        count: visitsByMonth[month]
      }));
      setVetVisits(chartData);
    } catch (error) {
      console.error('Erreur chargement données:', error);
    }
  };

  const handleAddCat = async (catData) => {
    try {
      await api.cats.create(catData);
      await loadCats();
    } catch (error) {
      console.error('Erreur création chat:', error);
      alert('Erreur lors de la création du chat');
    }
  };

  const handleAddVaccination = async (vaccData) => {
    try {
      await api.vaccinations.add(selectedCat.id, vaccData);
      await loadCatData(selectedCat.id);
    } catch (error) {
      console.error('Erreur ajout vaccination:', error);
      alert('Erreur lors de l\'ajout de la vaccination');
    }
  };

  const handleAddTreatment = async (treatmentData) => {
    try {
      await api.treatments.add(selectedCat.id, treatmentData);
      await loadCatData(selectedCat.id);
    } catch (error) {
      console.error('Erreur ajout traitement:', error);
      alert('Erreur lors de l\'ajout du traitement');
    }
  };

  const handleAddWeight = async (weightData) => {
    try {
      await api.weight.add(selectedCat.id, weightData);
      await loadCatData(selectedCat.id);
    } catch (error) {
      console.error('Erreur ajout poids:', error);
      alert('Erreur lors de l\'ajout de la pesée');
    }
  };

  const handleAddPhoto = async (formData) => {
    try {
      await api.photos.upload(selectedCat.id, formData);
      await loadCatData(selectedCat.id);
    } catch (error) {
      console.error('Erreur upload photo:', error);
      alert('Erreur lors de l\'upload de la photo');
    }
  };

  const handleCompleteReminder = async (reminderId) => {
    try {
      await api.reminders.complete(reminderId);
      await loadCatData(selectedCat.id);
    } catch (error) {
      console.error('Erreur complétion rappel:', error);
    }
  };

  const handleDeleteVaccination = async (vaccinationId) => {
    if (confirm('Voulez-vous vraiment supprimer cette vaccination ?')) {
      try {
        await api.vaccinations.delete(selectedCat.id, vaccinationId);
        await loadCatData(selectedCat.id);
      } catch (error) {
        console.error('Erreur suppression vaccination:', error);
      }
    }
  };

  const handleDeleteTreatment = async (treatmentId) => {
    if (confirm('Voulez-vous vraiment supprimer ce traitement ?')) {
      try {
        await api.treatments.delete(selectedCat.id, treatmentId);
        await loadCatData(selectedCat.id);
      } catch (error) {
        console.error('Erreur suppression traitement:', error);
      }
    }
  };

  const handleDeletePhoto = async (photoId) => {
    if (confirm('Voulez-vous vraiment supprimer cette photo ?')) {
      try {
        await api.photos.delete(selectedCat.id, photoId);
        await loadCatData(selectedCat.id);
      } catch (error) {
        console.error('Erreur suppression photo:', error);
      }
    }
  };

  const calculateAge = (birthDate) => {
    const birth = new Date(birthDate);
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
      age--;
    }
    return age;
  };

  const Dashboard = () => (
    <div className="space-y-6">
      {/* En-tête avec info du chat */}
      <div className="bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl p-8 text-white">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-4xl font-bold mb-2">{selectedCat?.name || 'Mon Chat'}</h1>
            <p className="text-purple-100 text-lg">
              {selectedCat?.breed} • {calculateAge(selectedCat?.birth_date)} ans • {selectedCat?.gender}
            </p>
          </div>
          <div className="text-6xl">🐱</div>
        </div>
      </div>

      {/* Statistiques rapides */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white rounded-xl p-6 shadow-lg border-l-4 border-blue-500">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-500 text-sm">Poids actuel</p>
              <p className="text-2xl font-bold text-gray-800">
                {weightData[weightData.length - 1]?.weight || 0} kg
              </p>
            </div>
            <Dumbbell className="text-blue-500" size={32} />
          </div>
        </div>

        <div className="bg-white rounded-xl p-6 shadow-lg border-l-4 border-green-500">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-500 text-sm">Vaccinations</p>
              <p className="text-2xl font-bold text-gray-800">{vaccinations.length}</p>
            </div>
            <Syringe className="text-green-500" size={32} />
          </div>
        </div>

        <div className="bg-white rounded-xl p-6 shadow-lg border-l-4 border-orange-500">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-500 text-sm">Photos</p>
              <p className="text-2xl font-bold text-gray-800">{photos.length}</p>
            </div>
            <Camera className="text-orange-500" size={32} />
          </div>
        </div>

        <div className="bg-white rounded-xl p-6 shadow-lg border-l-4 border-red-500">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-500 text-sm">Rappels</p>
              <p className="text-2xl font-bold text-gray-800">{reminders.length}</p>
            </div>
            <Bell className="text-red-500" size={32} />
          </div>
        </div>
      </div>

      {/* Rappels importants */}
      <div className="bg-white rounded-xl p-6 shadow-lg">
        <h3 className="text-xl font-bold mb-4 flex items-center gap-2">
          <Bell className="text-red-500" />
          Rappels à venir
        </h3>
        <div className="space-y-3">
          {reminders.slice(0, 3).map(reminder => (
            <div key={reminder.id} className="flex items-center justify-between p-4 bg-red-50 rounded-lg border-l-4 border-red-500">
              <div>
                <p className="font-semibold text-gray-800">{reminder.title}</p>
                <p className="text-sm text-gray-600">{new Date(reminder.reminder_date).toLocaleDateString('fr-FR')}</p>
              </div>
              <button className="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                Marquer fait
              </button>
            </div>
          ))}
        </div>
      </div>
    </div>
  );

  const HealthTab = () => (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h2 className="text-2xl font-bold text-gray-800">Carnet de Santé</h2>
      </div>

      {/* Vaccinations */}
      <div className="bg-white rounded-xl p-6 shadow-lg">
        <div className="flex justify-between items-center mb-4">
          <h3 className="text-xl font-bold flex items-center gap-2">
            <Syringe className="text-green-500" />
            Vaccinations
          </h3>
          <button 
            onClick={() => setShowAddVaccinationModal(true)}
            className="flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition"
          >
            <PlusCircle size={20} />
            Ajouter
          </button>
        </div>
        <div className="space-y-3">
          {vaccinations.length === 0 ? (
            <p className="text-gray-400 text-center py-8">Aucune vaccination enregistrée</p>
          ) : (
            vaccinations.map(vacc => (
              <div key={vacc.id} className="p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
                <div className="flex justify-between items-start">
                  <div>
                    <p className="font-semibold text-gray-800">{vacc.vaccine_type}</p>
                    <p className="text-sm text-gray-600">Date: {new Date(vacc.date).toLocaleDateString('fr-FR')}</p>
                    {vacc.next_date && (
                      <p className="text-sm text-gray-600">Rappel: {new Date(vacc.next_date).toLocaleDateString('fr-FR')}</p>
                    )}
                    {vacc.vet_name && (
                      <p className="text-sm text-gray-500">Vétérinaire: {vacc.vet_name}</p>
                    )}
                  </div>
                  <div className="flex items-center gap-2">
                    <span className="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">À jour</span>
                    <button
                      onClick={() => handleDeleteVaccination(vacc.id)}
                      className="text-red-500 hover:text-red-700"
                    >
                      <Trash2 size={18} />
                    </button>
                  </div>
                </div>
              </div>
            ))
          )}
        </div>
      </div>

      {/* Traitements */}
      <div className="bg-white rounded-xl p-6 shadow-lg">
        <div className="flex justify-between items-center mb-4">
          <h3 className="text-xl font-bold flex items-center gap-2">
            <FileText className="text-blue-500" />
            Traitements
          </h3>
          <button 
            onClick={() => setShowAddTreatmentModal(true)}
            className="flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
          >
            <PlusCircle size={20} />
            Ajouter
          </button>
        </div>
        <div className="space-y-3">
          {treatments.length === 0 ? (
            <p className="text-gray-400 text-center py-8">Aucun traitement enregistré</p>
          ) : (
            treatments.map(treatment => (
              <div key={treatment.id} className="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                <div className="flex justify-between items-start">
                  <div>
                    <p className="font-semibold text-gray-800">{treatment.treatment_type}</p>
                    {treatment.product_name && (
                      <p className="text-sm text-gray-600">{treatment.product_name}</p>
                    )}
                    <p className="text-sm text-gray-600">Dernier: {new Date(treatment.date).toLocaleDateString('fr-FR')}</p>
                    {treatment.next_date && (
                      <p className="text-sm text-gray-600">Prochain: {new Date(treatment.next_date).toLocaleDateString('fr-FR')}</p>
                    )}
                  </div>
                  <button
                    onClick={() => handleDeleteTreatment(treatment.id)}
                    className="text-red-500 hover:text-red-700"
                  >
                    <Trash2 size={18} />
                  </button>
                </div>
              </div>
            ))
          )}
        </div>
      </div>
    </div>
  );

  const WeightTab = () => (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h2 className="text-2xl font-bold text-gray-800">Suivi du Poids</h2>
        <button 
          onClick={() => setShowAddWeightModal(true)}
          className="flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
        >
          <PlusCircle size={20} />
          Ajouter mesure
        </button>
      </div>

      {weightData.length > 0 ? (
        <>
          <div className="bg-white rounded-xl p-6 shadow-lg">
            <h3 className="text-lg font-semibold mb-4">Évolution du poids</h3>
            <ResponsiveContainer width="100%" height={300}>
              <LineChart data={weightData}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="date" />
                <YAxis />
                <Tooltip />
                <Legend />
                <Line type="monotone" dataKey="weight" stroke="#8b5cf6" strokeWidth={2} name="Poids (kg)" />
              </LineChart>
            </ResponsiveContainer>
          </div>

          <div className="bg-white rounded-xl p-6 shadow-lg">
            <h3 className="text-lg font-semibold mb-4">Historique des pesées</h3>
            <div className="space-y-2">
              {weightData.slice().reverse().map((record) => (
                <div key={record.id} className="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                  <div>
                    <span className="font-bold text-gray-800">{record.weight} kg</span>
                    <span className="text-gray-600 ml-3">{new Date(record.date).toLocaleDateString('fr-FR')}</span>
                    {record.notes && (
                      <p className="text-sm text-gray-500 mt-1">{record.notes}</p>
                    )}
                  </div>
                </div>
              ))}
            </div>
          </div>
        </>
      ) : (
        <div className="bg-white rounded-xl p-12 shadow-lg text-center">
          <Dumbbell size={64} className="mx-auto text-gray-300 mb-4" />
          <p className="text-gray-400 text-lg">Aucune pesée enregistrée</p>
          <p className="text-sm text-gray-400 mt-2">Commencez à suivre le poids de {selectedCat?.name}</p>
        </div>
      )}
    </div>
  );

  const ChartsTab = () => (
    <div className="space-y-6">
      <h2 className="text-2xl font-bold text-gray-800">Statistiques & Graphiques</h2>

      <div className="bg-white rounded-xl p-6 shadow-lg">
        <h3 className="text-lg font-semibold mb-4">Visites vétérinaires par mois</h3>
        <ResponsiveContainer width="100%" height={300}>
          <BarChart data={vetVisits}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="date" />
            <YAxis />
            <Tooltip />
            <Legend />
            <Bar dataKey="count" fill="#ec4899" name="Nombre de visites" />
          </BarChart>
        </ResponsiveContainer>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="bg-white rounded-xl p-6 shadow-lg">
          <h3 className="text-lg font-semibold mb-4">Résumé annuel</h3>
          <div className="space-y-3">
            <div className="flex justify-between p-3 bg-purple-50 rounded-lg">
              <span>Visites vétérinaires</span>
              <span className="font-bold">5</span>
            </div>
            <div className="flex justify-between p-3 bg-green-50 rounded-lg">
              <span>Vaccinations</span>
              <span className="font-bold">2</span>
            </div>
            <div className="flex justify-between p-3 bg-blue-50 rounded-lg">
              <span>Traitements</span>
              <span className="font-bold">12</span>
            </div>
          </div>
        </div>

        <div className="bg-white rounded-xl p-6 shadow-lg">
          <h3 className="text-lg font-semibold mb-4">Variation de poids</h3>
          <div className="text-center py-8">
            <p className="text-5xl font-bold text-green-500">+0.8 kg</p>
            <p className="text-gray-600 mt-2">depuis le début de l'année</p>
            <div className="mt-4 flex items-center justify-center gap-2 text-green-500">
              <TrendingUp />
              <span>Croissance saine</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );

  const PhotosTab = () => (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h2 className="text-2xl font-bold text-gray-800">Galerie Photos</h2>
        <button 
          onClick={() => setShowAddPhotoModal(true)}
          className="flex items-center gap-2 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition"
        >
          <Camera size={20} />
          Ajouter photo
        </button>
      </div>

      <div className="bg-white rounded-xl p-6 shadow-lg">
        {photos.length === 0 ? (
          <div className="text-center py-12 text-gray-400">
            <Camera size={64} className="mx-auto mb-4 opacity-50" />
            <p className="text-lg">Aucune photo pour le moment</p>
            <p className="text-sm mt-2">Commencez à capturer les moments précieux de {selectedCat?.name}</p>
          </div>
        ) : (
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            {photos.map(photo => (
              <div key={photo.id} className="relative group">
                <img 
                  src={`http://localhost:6000${photo.url}`} 
                  alt={photo.title || 'Photo'} 
                  className="w-full h-48 object-cover rounded-lg"
                />
                <div className="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition rounded-lg flex items-center justify-center">
                  <button
                    onClick={() => handleDeletePhoto(photo.id)}
                    className="opacity-0 group-hover:opacity-100 transition bg-red-500 text-white p-2 rounded-lg"
                  >
                    <Trash2 size={20} />
                  </button>
                </div>
                {photo.title && (
                  <p className="mt-2 text-sm text-gray-700 font-medium">{photo.title}</p>
                )}
                {photo.tags && (
                  <div className="flex flex-wrap gap-1 mt-1">
                    {photo.tags.split(',').map((tag, idx) => (
                      <span key={idx} className="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">
                        {tag.trim()}
                      </span>
                    ))}
                  </div>
                )}
                <p className="text-xs text-gray-500 mt-1">{new Date(photo.date).toLocaleDateString('fr-FR')}</p>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );

  return (
    <div className="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50">
      {/* Header */}
      <header className="bg-white shadow-md">
        <div className="max-w-7xl mx-auto px-4 py-4">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="text-4xl">🐾</div>
              <h1 className="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                CatLife Tracker
              </h1>
            </div>
            <button 
              onClick={() => setShowAddCatModal(true)}
              className="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:opacity-90 transition"
            >
              + Nouveau chat
            </button>
          </div>
        </div>
      </header>

      <div className="max-w-7xl mx-auto px-4 py-8">
        {cats.length === 0 ? (
          <div className="text-center py-20">
            <div className="text-6xl mb-4">🐱</div>
            <h2 className="text-2xl font-bold text-gray-700 mb-2">Aucun chat enregistré</h2>
            <p className="text-gray-500 mb-6">Commencez par ajouter votre premier chat</p>
            <button 
              onClick={() => setShowAddCatModal(true)}
              className="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:opacity-90 transition"
            >
              + Ajouter mon premier chat
            </button>
          </div>
        ) : (
          <div className="flex gap-6">
            {/* Sidebar */}
            <aside className="w-64 bg-white rounded-xl shadow-lg p-4 h-fit sticky top-8">
              <nav className="space-y-2">
                {[
                  { id: 'dashboard', label: 'Tableau de bord', icon: Activity },
                  { id: 'health', label: 'Santé', icon: Heart },
                  { id: 'weight', label: 'Poids', icon: Weight },
                  { id: 'photos', label: 'Photos', icon: Camera },
                  { id: 'charts', label: 'Statistiques', icon: TrendingUp },
                ].map(tab => (
                  <button
                    key={tab.id}
                    onClick={() => setActiveTab(tab.id)}
                    className={`w-full flex items-center gap-3 px-4 py-3 rounded-lg transition ${
                      activeTab === tab.id
                        ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white'
                        : 'text-gray-700 hover:bg-gray-100'
                    }`}
                  >
                    <tab.icon size={20} />
                    {tab.label}
                  </button>
                ))}
              </nav>
            </aside>

            {/* Main Content */}
            <main className="flex-1">
              {activeTab === 'dashboard' && <Dashboard />}
              {activeTab === 'health' && <HealthTab />}
              {activeTab === 'weight' && <WeightTab />}
              {activeTab === 'photos' && <PhotosTab />}
              {activeTab === 'charts' && <ChartsTab />}
            </main>
          </div>
        )}
      </div>

      {/* Modals */}
      <AddCatModal 
        isOpen={showAddCatModal} 
        onClose={() => setShowAddCatModal(false)} 
        onSave={handleAddCat}
      />
      <AddVaccinationModal 
        isOpen={showAddVaccinationModal} 
        onClose={() => setShowAddVaccinationModal(false)} 
        onSave={handleAddVaccination}
      />
      <AddTreatmentModal 
        isOpen={showAddTreatmentModal} 
        onClose={() => setShowAddTreatmentModal(false)} 
        onSave={handleAddTreatment}
      />
      <AddWeightModal 
        isOpen={showAddWeightModal} 
        onClose={() => setShowAddWeightModal(false)} 
        onSave={handleAddWeight}
      />
      <AddPhotoModal 
        isOpen={showAddPhotoModal} 
        onClose={() => setShowAddPhotoModal(false)} 
        onSave={handleAddPhoto}
      />
    </div>
  );
}
