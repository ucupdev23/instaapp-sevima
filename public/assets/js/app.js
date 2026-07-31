// InstaApp - Frontend JavaScript
// Handles API calls dan interaksi UI

const API_BASE = '/instaapp-sevima/api';

// ============================================
// API Helper Functions
// ============================================
const api = {
    // Auth
    async register(username, email, password) {
        return this._post('/auth/register', { username, email, password });
    },

    async login(email, password) {
        return this._post('/auth/login', { email, password });
    },

    async logout() {
        return this._post('/auth/logout');
    },

    async getMe() {
        return this._get('/auth/me');
    },

    // Posts
    async getPosts() {
        return this._get('/posts');
    },

    async getPost(id) {
        return this._get(`/posts/${id}`);
    },

    async createPost(formData) {
        const res = await fetch(`${API_BASE}/posts`, {
            method: 'POST',
            body: formData // FormData untuk upload file
        });
        return res.json();
    },

    async deletePost(id) {
        return this._delete(`/posts/${id}`);
    },

    async getUserPosts(userId) {
        return this._get(`/posts/user/${userId}`);
    },

    // Likes
    async toggleLike(postId) {
        return this._post(`/posts/${postId}/like`);
    },

    // Comments
    async getComments(postId) {
        return this._get(`/posts/${postId}/comments`);
    },

    async addComment(postId, content) {
        return this._post(`/posts/${postId}/comments`, { content });
    },

    async deleteComment(commentId) {
        return this._delete(`/comments/${commentId}`);
    },

    // User
    async getUser(userId) {
        return this._get(`/users/${userId}`);
    },

    // Helper methods
    async _get(path) {
        const res = await fetch(`${API_BASE}${path}`);
        return res.json();
    },

    async _post(path, data = null) {
        const options = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        };
        if (data) options.body = JSON.stringify(data);
        const res = await fetch(`${API_BASE}${path}`, options);
        return res.json();
    },

    async _delete(path) {
        const res = await fetch(`${API_BASE}${path}`, { method: 'DELETE' });
        return res.json();
    }
};

// ============================================
// UI Helper Functions
// ============================================

// Tampilkan alert message
function showAlert(message, type = 'error') {
    const alert = document.getElementById('alert');
    if (!alert) return;
    alert.textContent = message;
    alert.className = `alert alert--${type} alert--show`;
    
    // Auto hide setelah 4 detik
    setTimeout(() => {
        alert.classList.remove('alert--show');
    }, 4000);
}

// Format waktu relatif (misal: "2 jam yang lalu")
function timeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const seconds = Math.floor((now - date) / 1000);

    if (seconds < 60) return 'Baru saja';
    if (seconds < 3600) return Math.floor(seconds / 60) + ' menit yang lalu';
    if (seconds < 86400) return Math.floor(seconds / 3600) + ' jam yang lalu';
    if (seconds < 604800) return Math.floor(seconds / 86400) + ' hari yang lalu';
    
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

// Cek apakah user sudah login, redirect ke login kalau belum
async function checkAuth() {
    try {
        const res = await api.getMe();
        if (res.error) {
            window.location.href = 'index.html';
            return null;
        }
        return res.user;
    } catch {
        window.location.href = 'index.html';
        return null;
    }
}

// Buat navbar HTML
function createNavbar(username, userId) {
    return `
    <nav class="navbar">
        <div class="navbar__inner">
            <a href="feed.html" class="navbar__logo">InstaApp</a>
            <div class="navbar__links">
                <a href="feed.html" class="navbar__link">🏠 Feed</a>
                <a href="create-post.html" class="navbar__link">➕ Post</a>
                <a href="profile.html?id=${userId}" class="navbar__link">👤 ${username}</a>
                <a href="#" class="navbar__link navbar__link--logout" onclick="handleLogout()">Keluar</a>
            </div>
        </div>
    </nav>`;
}

// Handle logout
async function handleLogout() {
    if (!confirm('Yakin ingin keluar?')) return;
    await api.logout();
    window.location.href = 'index.html';
}

// Navbar shadow saat scroll
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        if (window.scrollY > 10) {
            navbar.classList.add('navbar--scrolled');
        } else {
            navbar.classList.remove('navbar--scrolled');
        }
    }
});

