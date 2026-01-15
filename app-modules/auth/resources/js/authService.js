import axios from 'axios';

class AuthService {
  constructor() {
    this.api = axios.create({
      baseURL: '/api',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    });

    // Add token to requests if it exists
    this.api.interceptors.request.use((config) => {
      const token = this.getToken();
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
      return config;
    });

    this.events = {
      onLogin: [],
      onLogout: [],
      onError: []
    };
  }

  /**
   * Register event listener
   */
  on(event, callback) {
    if (this.events[event]) {
      this.events[event].push(callback);
    }
  }

  /**
   * Emit event
   */
  emit(event, data) {
    if (this.events[event]) {
      this.events[event].forEach(callback => callback(data));
    }
  }

  /**
   * Register new user
   */
  async register(name, email, password, passwordConfirmation) {
    try {
      const response = await this.api.post('/register', {
        name,
        email,
        password,
        password_confirmation: passwordConfirmation
      });

      const { access_token, user } = response.data;
      this.setToken(access_token);
      this.setUser(user);
      this.emit('onLogin', { user, token: access_token });

      return response.data;
    } catch (error) {
      this.emit('onError', error);
      throw error;
    }
  }

  /**
   * Login with email and password
   */
  async login(email, password) {
    try {
      const response = await this.api.post('/login', {
        email,
        password
      });

      const { access_token, user } = response.data;
      this.setToken(access_token);
      this.setUser(user);
      this.emit('onLogin', { user, token: access_token });

      return response.data;
    } catch (error) {
      this.emit('onError', error);
      throw error;
    }
  }

  /**
   * Login with Basic Auth (as per your requirement)
   * This uses HTTP Basic Authentication
   */
  async auth(userName, password) {
    try {
      const basicAuthApi = axios.create({
        auth: {
          username: userName,
          password: password
        },
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const response = await basicAuthApi.get('/api/users/me');
      const { user } = response.data;
      
      this.setUser(user);
      this.emit('onLogin', { user });

      return response.data;
    } catch (error) {
      this.emit('onError', error);
      throw error;
    }
  }

  /**
   * Logout user
   */
  async logout() {
    try {
      await this.api.post('/logout');
      this.clearAuth();
      this.emit('onLogout');
    } catch (error) {
      this.emit('onError', error);
      throw error;
    }
  }

  /**
   * Get current user
   */
  async me() {
    try {
      const response = await this.api.get('/me');
      return response.data;
    } catch (error) {
      this.emit('onError', error);
      throw error;
    }
  }

  /**
   * Refresh token
   */
  async refreshToken() {
    try {
      const response = await this.api.post('/refresh');
      const { access_token } = response.data;
      this.setToken(access_token);
      return response.data;
    } catch (error) {
      this.emit('onError', error);
      throw error;
    }
  }

  /**
   * Store token in localStorage
   */
  setToken(token) {
    localStorage.setItem('auth_token', token);
  }

  /**
   * Get token from localStorage
   */
  getToken() {
    return localStorage.getItem('auth_token');
  }

  /**
   * Store user in localStorage
   */
  setUser(user) {
    localStorage.setItem('user', JSON.stringify(user));
  }

  /**
   * Get user from localStorage
   */
  getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  }

  /**
   * Check if user is authenticated
   */
  isAuthenticated() {
    return !!this.getToken();
  }

  /**
   * Clear authentication data
   */
  clearAuth() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
  }
}

// Export singleton instance
export default new AuthService();
