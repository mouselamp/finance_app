class FinancialAppAPI {
    constructor() {
        this.baseURL = '/api';
        this.token = this.getStoredToken();
        this.setupAxiosDefaults();
    }

    // Helper method to get route URL
    getRouteUrl(routeKey, params = {}) {
        console.log('getRouteUrl called with:', routeKey, params);

        if (typeof window.route === 'function') {
            let url = window.route(routeKey, params);
            console.log('Window route returned:', url);
            return url;
        }

        // Fallback to manual URL construction if route helper not available
        const fallbackUrls = {
            'auth.login': '/auth/login',
            'auth.logout': '/auth/logout',
            'auth.me': '/auth/me',
            'auth.refresh': '/auth/refresh',
            'transactions.index': '/transactions',
            'transactions.store': '/transactions',
            'transactions.statistics': '/transactions/statistics',
            'transactions.show': (params) => `/transactions/${params.id}`,
            'transactions.update': (params) => `/transactions/${params.id}`,
            'transactions.destroy': (params) => `/transactions/${params.id}`,
            'accounts.index': '/accounts',
            'accounts.store': '/accounts',
            'accounts.show': (params) => `/accounts/${params.id}`,
            'accounts.update': (params) => `/accounts/${params.id}`,
            'accounts.destroy': (params) => `/accounts/${params.id}`,
            'categories.index': '/categories',
            'categories.store': '/categories',
            'categories.show': (params) => `/categories/${params.id}`,
            'categories.update': (params) => `/categories/${params.id}`,
            'categories.destroy': (params) => `/categories/${params.id}`
        };

        let url = fallbackUrls[routeKey];
        if (typeof url === 'function') {
            url = url(params);
        }

        // Handle query parameters for fallback
        const queryParams = new URLSearchParams();
        for (const [key, value] of Object.entries(params)) {
            if (key !== 'id' && !url.includes(`:${key}`)) {
                queryParams.append(key, value);
            }
        }

        const queryString = queryParams.toString();
        const finalUrl = queryString ? `${url}?${queryString}` : (url || routeKey);
        console.log('Fallback URL:', finalUrl);

        return finalUrl;
    }

    setupAxiosDefaults() {
        if (window.axios) {
            window.axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
        }
    }

    getStoredToken() {
        // Try to get token from various sources
        let token = localStorage.getItem('api_token');

        // If no token in localStorage, try to get from cookie
        if (!token) {
            const cookies = document.cookie.split(';');
            for (let cookie of cookies) {
                const [name, value] = cookie.trim().split('=');
                if (name === 'api_token') {
                    token = value;
                    localStorage.setItem('api_token', token);
                    break;
                }
            }
        }

        // If still no token, try to get from meta tag
        if (!token) {
            const metaToken = document.querySelector('meta[name="api-token"]');
            if (metaToken) {
                token = metaToken.getAttribute('content');
                if (token) {
                    localStorage.setItem('api_token', token);
                }
            }
        }

        return token;
    }

    setToken(token) {
        this.token = token;
        localStorage.setItem('api_token', token);
        this.setupAxiosDefaults();
    }

    clearToken() {
        this.token = null;
        localStorage.removeItem('api_token');
        if (window.axios) {
            delete window.axios.defaults.headers.common['Authorization'];
        }
    }

    async request(method, routeKey, params = {}, data = null) {
        let endpoint;

        // Use route helper if available, otherwise fallback to direct endpoint
        if (routeKey && typeof routeKey === 'string') {
            endpoint = this.getRouteUrl(routeKey, params);
        } else {
            // Fallback for backward compatibility
            endpoint = routeKey;
        }

        const config = {
            method: method,
            url: `${this.baseURL}${endpoint}`,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        };

        if (this.token) {
            config.headers.Authorization = `Bearer ${this.token}`;
        }

        if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
            config.data = data;
        }

        try {
            const response = await axios(config);
            return response.data;
        } catch (error) {
            console.error(`API Error [${method} ${endpoint}]:`, error);
            throw error;
        }
    }

    // Authentication
    async login(email, password) {
        const response = await this.request('POST', 'auth.login', {}, { email, password });
        if (response.success) {
            this.setToken(response.data.token);
        }
        return response;
    }

    async logout() {
        const response = await this.request('POST', 'auth.logout');
        this.clearToken();
        return response;
    }

    async getMe() {
        return await this.request('GET', 'auth.me');
    }

    // Transactions
    async getTransactions(page = 1) {
        return await this.request('GET', 'transactions.index', { page });
    }

    async getTransaction(id) {
        return await this.request('GET', 'transactions.show', { id });
    }

    async createTransaction(data) {
        return await this.request('POST', 'transactions.store', {}, data);
    }

    async updateTransaction(id, data) {
        return await this.request('PUT', 'transactions.update', { id }, data);
    }

    async deleteTransaction(id) {
        return await this.request('DELETE', 'transactions.destroy', { id });
    }

    async getTransactionStatistics() {
        return await this.request('GET', 'transactions.statistics');
    }

    // Accounts
    async getAccounts() {
        return await this.request('GET', 'accounts.index');
    }

    async getAccount(id) {
        return await this.request('GET', 'accounts.show', { id });
    }

    async createAccount(data) {
        return await this.request('POST', 'accounts.store', {}, data);
    }

    async updateAccount(id, data) {
        return await this.request('PUT', 'accounts.update', { id }, data);
    }

    async deleteAccount(id) {
        return await this.request('DELETE', 'accounts.destroy', { id });
    }

    // Categories
    async getCategories(type = null) {
        const params = type ? { type } : {};
        return await this.request('GET', 'categories.index', params);
    }

    async getCategory(id) {
        return await this.request('GET', 'categories.show', { id });
    }

    async createCategory(data) {
        return await this.request('POST', 'categories.store', {}, data);
    }

    async updateCategory(id, data) {
        return await this.request('PUT', 'categories.update', { id }, data);
    }

    async deleteCategory(id) {
        return await this.request('DELETE', 'categories.destroy', { id });
    }

    // Utility methods
    formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    formatDate(date) {
        return new Date(date).toLocaleDateString('id-ID');
    }

    formatDateTime(dateTime) {
        return new Date(dateTime).toLocaleString('id-ID');
    }

    showAlert(type, message) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.api-alert');
        existingAlerts.forEach(alert => alert.remove());

        // Create new alert
        const alertDiv = document.createElement('div');
        alertDiv.className = `api-alert mb-4 p-4 rounded-lg ${
            type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' :
            type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' :
            type === 'warning' ? 'bg-yellow-100 border border-yellow-400 text-yellow-700' :
            'bg-blue-100 border border-blue-400 text-blue-700'
        }`;
        alertDiv.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    ${type === 'success' ? '<i class="fas fa-check-circle"></i>' :
                      type === 'error' ? '<i class="fas fa-exclamation-circle"></i>' :
                      type === 'warning' ? '<i class="fas fa-exclamation-triangle"></i>' :
                      '<i class="fas fa-info-circle"></i>'}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="this.closest('.api-alert').remove()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;

        // Add to top of container
        const container = document.querySelector('.container') || document.body;
        container.insertBefore(alertDiv, container.firstChild);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    handleApiError(error, context = 'Operation') {
        console.error(`${context} error:`, error);

        let message = 'An error occurred';

        if (error.response) {
            if (error.response.status === 401) {
                message = 'Please login to continue';
                this.clearToken();
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            } else if (error.response.status === 403) {
                message = 'Access forbidden';
            } else if (error.response.status === 404) {
                message = 'Resource not found';
            } else if (error.response.status === 422) {
                // Validation errors
                if (error.response.data.errors) {
                    const errors = Object.values(error.response.data.errors).flat();
                    message = errors.join(', ');
                } else {
                    message = error.response.data.message || 'Validation failed';
                }
            } else if (error.response.data && error.response.data.message) {
                message = error.response.data.message;
            }
        } else if (error.message) {
            message = error.message;
        }

        this.showAlert('error', message);
    }

    handleApiSuccess(response, customMessage = null) {
        const message = customMessage || response.message || 'Operation successful';
        this.showAlert('success', message);
    }
}

// Create global instance
window.api = new FinancialAppAPI();

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FinancialAppAPI;
}