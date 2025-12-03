import React, { createContext, useState, useContext, useEffect } from 'react';
import axios from 'axios';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState(localStorage.getItem('token'));
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (token) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      // You can optionally fetch user info here
      setUser({ token });
    }
    setLoading(false);
  }, [token]);

  const login = async (email, password) => {
    try {
      const response = await axios.post('http://localhost:8000/api/login', {
        email,
        password
      });
      
      const { token } = response.data;
      localStorage.setItem('token', token);
      setToken(token);
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      setUser({ email, token });
      return { success: true };
    } catch (error) {
      return { 
        success: false, 
        error: error.response?.data?.message || 'Login failed' 
      };
    }
  };

  const register = async (email, password, name, roles = ['ROLE_USER']) => {
    try {
      await axios.post('http://localhost:8000/api/register', {
        email,
        password,
        name,
        roles
      });
      
      // Auto-login after registration
      return await login(email, password);
    } catch (error) {
      return { 
        success: false, 
        error: error.response?.data?.error || 'Registration failed' 
      };
    }
  };

  const logout = () => {
    localStorage.removeItem('token');
    setToken(null);
    setUser(null);
    delete axios.defaults.headers.common['Authorization'];
  };

  return (
    <AuthContext.Provider value={{ user, token, login, register, logout, loading }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within AuthProvider');
  }
  return context;
};
