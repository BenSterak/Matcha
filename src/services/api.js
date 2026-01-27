const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:3000/api';

// Helper function for authenticated requests
const apiFetch = async (endpoint, options = {}) => {
  const token = localStorage.getItem('token');

  const headers = {
    'Content-Type': 'application/json',
    ...(token && { Authorization: `Bearer ${token}` }),
    ...options.headers,
  };

  try {
    const response = await fetch(`${API_BASE}${endpoint}`, {
      ...options,
      headers,
    });

    // Handle 401 - Unauthorized
    if (response.status === 401) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.href = '/login';
      throw new Error('Session expired. Please login again.');
    }

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.error || data.message || 'Something went wrong');
    }

    return data;
  } catch (error) {
    if (error.name === 'TypeError' && error.message === 'Failed to fetch') {
      throw new Error('Unable to connect to server. Please try again.');
    }
    throw error;
  }
};

// Auth API
export const authAPI = {
  register: async (userData) => {
    return apiFetch('/auth/register', {
      method: 'POST',
      body: JSON.stringify(userData),
    });
  },

  login: async (email, password) => {
    return apiFetch('/auth/login', {
      method: 'POST',
      body: JSON.stringify({ email, password }),
    });
  },

  getMe: async () => {
    return apiFetch('/auth/me');
  },

  updateProfile: async (userData) => {
    return apiFetch('/auth/profile', {
      method: 'PUT',
      body: JSON.stringify(userData),
    });
  },
};

// Jobs API
export const jobsAPI = {
  getAll: async (filters = {}) => {
    const queryString = new URLSearchParams(filters).toString();
    const endpoint = queryString ? `/jobs?${queryString}` : '/jobs';
    return apiFetch(endpoint);
  },

  getById: async (id) => {
    return apiFetch(`/jobs/${id}`);
  },

  create: async (jobData) => {
    return apiFetch('/jobs', {
      method: 'POST',
      body: JSON.stringify(jobData),
    });
  },

  update: async (id, jobData) => {
    return apiFetch(`/jobs/${id}`, {
      method: 'PUT',
      body: JSON.stringify(jobData),
    });
  },

  delete: async (id) => {
    return apiFetch(`/jobs/${id}`, {
      method: 'DELETE',
    });
  },

  // Employer-specific
  getMyJobs: async () => {
    return apiFetch('/employer/jobs');
  },
};

// Matches API
export const matchesAPI = {
  // Candidate swipes right on a job
  createSwipe: async (jobId, status = 'LIKED') => {
    return apiFetch('/matches', {
      method: 'POST',
      body: JSON.stringify({ jobId, status }),
    });
  },

  // Get user's matches (where isMatched = true)
  getMatches: async () => {
    return apiFetch('/matches');
  },

  // Get all swipes (including pending)
  getSwipes: async () => {
    return apiFetch('/matches/swipes');
  },

  // Employer gets candidates who liked their jobs
  getPendingCandidates: async () => {
    return apiFetch('/employer/candidates');
  },

  // Employer approves a candidate
  approve: async (matchId) => {
    return apiFetch(`/matches/${matchId}/approve`, {
      method: 'POST',
    });
  },

  // Employer rejects a candidate
  reject: async (matchId) => {
    return apiFetch(`/matches/${matchId}/reject`, {
      method: 'POST',
    });
  },
};

// Messages API
export const messagesAPI = {
  getMessages: async (matchId) => {
    return apiFetch(`/matches/${matchId}/messages`);
  },

  sendMessage: async (matchId, content) => {
    return apiFetch(`/matches/${matchId}/messages`, {
      method: 'POST',
      body: JSON.stringify({ content }),
    });
  },

  markAsRead: async (messageId) => {
    return apiFetch(`/messages/${messageId}/read`, {
      method: 'PUT',
    });
  },
};

// Users API (for candidate cards viewed by employers)
export const usersAPI = {
  getById: async (id) => {
    return apiFetch(`/users/${id}`);
  },

  getCandidates: async (filters = {}) => {
    const queryString = new URLSearchParams(filters).toString();
    const endpoint = queryString ? `/candidates?${queryString}` : '/candidates';
    return apiFetch(endpoint);
  },
};

export default {
  auth: authAPI,
  jobs: jobsAPI,
  matches: matchesAPI,
  messages: messagesAPI,
  users: usersAPI,
};
