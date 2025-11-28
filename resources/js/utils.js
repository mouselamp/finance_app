// Utility functions for Finance App
window.api = {
    formatCurrency: function(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value);
    }
};

// Global error handler
window.handleApiError = function(error, context) {
    console.error(`Error in ${context}:`, error);
    
    let message = 'Terjadi kesalahan saat memproses permintaan Anda.';
    
    if (error.response) {
        // The request was made and the server responded with a status code
        // that falls out of the range of 2xx
        if (error.response.data && error.response.data.message) {
            message = error.response.data.message;
        } else {
            message = `Server Error: ${error.response.status}`;
        }
    } else if (error.request) {
        // The request was made but no response was received
        message = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
    } else {
        // Something happened in setting up the request that triggered an Error
        message = error.message;
    }
    
    // You might want to show a toast/notification here
    alert(`${context}: ${message}`);
};
