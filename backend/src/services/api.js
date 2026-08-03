import axios from 'axios';

const API_BASE_URL = import.meta.env.VITE_API_URL || 'https://db-event.hudsam.my.id/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

// Request interceptor to automatically add authorization or other common logic if needed
api.interceptors.request.use(
  (config) => {
    // If we have an authentication token stored, add it here
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

export const authService = {
  login: async (email, password) => {
    const response = await api.post('/auth/login', { email, password });
    return response.data; // Expected response format: { status: 'success', data: user }
  },
  getUser: async (id) => {
    const response = await api.get(`/users/${id}`);
    return response.data;
  },
};

export const eventService = {
  getAll: async (status) => {
    const params = {};
    if (status) {
      params.status = status;
    }
    const response = await api.get('/events', { params });
    return response.data; // Expected format: { status: 'success', data: events[] }
  },
  getByIdOrSlug: async (idOrSlug) => {
    const response = await api.get(`/events/${idOrSlug}`);
    return response.data;
  },
  create: async (eventData) => {
    const response = await api.post('/events', eventData);
    return response.data;
  },
  update: async (id, eventData) => {
    const response = await api.put(`/events/${id}`, eventData);
    return response.data;
  },
  delete: async (id) => {
    const response = await api.delete(`/events/${id}`);
    return response.data;
  },
};

export default api;
