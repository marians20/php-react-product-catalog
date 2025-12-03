import axios from 'axios';

const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000/api';

const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Add request interceptor to include JWT token
apiClient.interceptors.request.use(
  (config) => {
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

// Add response interceptor to handle 401 errors
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export const productService = {
  /**
   * Get all products
   */
  getAllProducts: async () => {
    const response = await apiClient.get('/products');
    return response.data;
  },

  /**
   * Get a single product by ID
   */
  getProduct: async (id) => {
    const response = await apiClient.get(`/products/${id}`);
    return response.data;
  },

  /**
   * Create a new product
   */
  createProduct: async (productData) => {
    const response = await apiClient.post('/products', productData);
    return response.data;
  },

  /**
   * Update an existing product
   */
  updateProduct: async (id, productData) => {
    const response = await apiClient.put(`/products/${id}`, productData);
    return response.data;
  },

  /**
   * Delete a product
   */
  deleteProduct: async (id) => {
    await apiClient.delete(`/products/${id}`);
  },
};

export const userService = {
  /**
   * Get all users
   */
  getAllUsers: async () => {
    const response = await apiClient.get('/users');
    return response.data;
  },

  /**
   * Get a single user by ID
   */
  getUser: async (id) => {
    const response = await apiClient.get(`/users/${id}`);
    return response.data;
  },

  /**
   * Update an existing user
   */
  updateUser: async (id, userData) => {
    const response = await apiClient.put(`/users/${id}`, userData);
    return response.data;
  },

  /**
   * Delete a user
   */
  deleteUser: async (id) => {
    await apiClient.delete(`/users/${id}`);
  },

  /**
   * Toggle user status (enable/disable)
   */
  toggleUserStatus: async (id, enabled) => {
    const response = await apiClient.patch(`/users/${id}/toggle-status`, { enabled });
    return response.data;
  },
};

export default apiClient;
