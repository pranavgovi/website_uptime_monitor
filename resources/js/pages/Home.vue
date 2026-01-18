<template>
    <div class="home-container">
        <div class="content-wrapper">
            <h1>Website Uptime Monitor</h1>
            
            <div class="client-select-section">
                <label for="client-select">Select Client Email:</label>
                <select 
                    id="client-select"
                    v-model="selectedClientId"
                    @change="loadWebsites"
                    class="client-select"
                >
                    <option value="">-- Please select a client --</option>
                    <option 
                        v-for="client in clients" 
                        :key="client.id" 
                        :value="client.id"
                    >
                        {{ client.email }}
                    </option>
                </select>
            </div>

            <div v-if="websites.length > 0" class="websites-section">
                <h2>Monitored Websites:</h2>
                <ul class="websites-list">
                    <li v-for="website in websites" :key="website.id">
                        <a 
                            href="#" 
                            @click.prevent="handleWebsiteClick(website.url)"
                            class="website-link"
                        >
                            {{ website.url }}
                        </a>
                        <span 
                            :class="['status-badge', website.is_up ? 'status-up' : 'status-down']"
                        >
                            {{ website.is_up ? 'Up' : 'Down' }}
                        </span>
                    </li>
                </ul>
            </div>

            <div v-if="selectedClientId && websites.length === 0 && !loading" class="no-websites">
                <p>No websites found for this client.</p>
            </div>

            <div v-if="loading" class="loading">
                <p>Loading...</p>
            </div>
        </div>

        <div v-if="showDialog" class="dialog-overlay" @click="closeDialog">
            <div class="dialog-content" @click.stop>
                <h3>Confirmation</h3>
                <p>{{ dialogMessage }}</p>
                <div class="dialog-actions">
                    <button @click="confirmVisit" class="btn btn-primary">Continue</button>
                    <button @click="closeDialog" class="btn btn-secondary">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'Home',
    data() {
        return {
            clients: [],
            selectedClientId: '',
            websites: [],
            loading: false,
            showDialog: false,
            pendingUrl: null
        };
    },
    computed: {
        dialogMessage() {
            return this.pendingUrl 
                ? `You are about to visit ${this.pendingUrl}. Do you want to continue?`
                : '';
        }
    },
    mounted() {
        this.loadClients();
    },
    methods: {
        async loadClients() {
            try {
                const response = await axios.get('/clients');
                this.clients = response.data;
            } catch (error) {
                console.error('Error loading clients:', error);
                alert('Failed to load clients. Please try again.');
            }
        },
        async loadWebsites() {
            if (!this.selectedClientId) {
                this.websites = [];
                return;
            }

            this.loading = true;
            try {
                const response = await axios.get(`/clients/${this.selectedClientId}/websites`);
                this.websites = response.data;
            } catch (error) {
                console.error('Error loading websites:', error);
                alert('Failed to load websites. Please try again.');
                this.websites = [];
            } finally {
                this.loading = false;
            }
        },
        handleWebsiteClick(url) {
            this.pendingUrl = url;
            this.showDialog = true;
        },
        confirmVisit() {
            if (this.pendingUrl) {
                let urlToOpen = this.pendingUrl;
                if (!urlToOpen.startsWith('http://') && !urlToOpen.startsWith('https://')) {
                    urlToOpen = 'https://' + urlToOpen;
                }
                window.open(urlToOpen, '_blank');
            }
            this.closeDialog();
        },
        closeDialog() {
            this.showDialog = false;
            this.pendingUrl = null;
        }
    }
};
</script>

<style scoped>
.home-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.content-wrapper {
    background: white;
    border-radius: 8px;
    padding: 2rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

h1 {
    margin-bottom: 2rem;
    color: #2c3e50;
    font-size: 2rem;
}

h2 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: #34495e;
    font-size: 1.5rem;
}

.client-select-section {
    margin-bottom: 2rem;
}

.client-select-section label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #555;
}

.client-select {
    width: 100%;
    max-width: 500px;
    padding: 0.75rem;
    font-size: 1rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: white;
    cursor: pointer;
}

.client-select:hover {
    border-color: #999;
}

.client-select:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

.websites-section {
    margin-top: 2rem;
}

.websites-list {
    list-style: none;
    padding: 0;
}

.websites-list li {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    background-color: #f9f9f9;
    border-radius: 4px;
    border-left: 4px solid #4a90e2;
}

.website-link {
    color: #4a90e2;
    text-decoration: none;
    font-weight: 500;
    flex-grow: 1;
}

.website-link:hover {
    text-decoration: underline;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-left: 1rem;
}

.status-up {
    background-color: #d4edda;
    color: #155724;
}

.status-down {
    background-color: #f8d7da;
    color: #721c24;
}

.no-websites,
.loading {
    margin-top: 2rem;
    padding: 1rem;
    text-align: center;
    color: #666;
}

.dialog-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.dialog-content {
    background: white;
    border-radius: 8px;
    padding: 2rem;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.dialog-content h3 {
    margin-bottom: 1rem;
    color: #2c3e50;
}

.dialog-content p {
    margin-bottom: 1.5rem;
    color: #555;
    line-height: 1.5;
}

.dialog-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s;
}

.btn-primary {
    background-color: #4a90e2;
    color: white;
}

.btn-primary:hover {
    background-color: #357abd;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5a6268;
}
</style>
